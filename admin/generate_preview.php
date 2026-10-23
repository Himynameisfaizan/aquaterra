<?php
// admin/generate_preview.php
// This generates a preview image with admin's customization applied

function generateProductPreview($product_id, $conn) {
    // Fetch product details
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) return false;
    
    // Get product image
    $images = explode(',', $product['pro_img']);
    $mainImage = !empty($images[0]) ? $images[0] : '';
    
    if (empty($mainImage)) return false;
    
    $imagePath = 'assets/img/uploads/' . $mainImage;
    
    if (!file_exists($imagePath)) return false;
    
    // Load original image
    $imageInfo = getimagesize($imagePath);
    $mime = $imageInfo['mime'];
    
    switch($mime) {
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = imagecreatefromjpeg($imagePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($imagePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($imagePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) return false;
    
    $width = imagesx($sourceImage);
    $height = imagesy($sourceImage);
    
    // Create preview canvas (same size)
    $preview = imagecreatetruecolor($width, $height);
    
    // Preserve transparency for PNG
    if ($mime == 'image/png') {
        imagesavealpha($preview, true);
        $transparent = imagecolorallocatealpha($preview, 0, 0, 0, 127);
        imagefill($preview, 0, 0, $transparent);
    }
    
    // Copy original image
    imagecopy($preview, $sourceImage, 0, 0, 0, 0, $width, $height);
    
    // Parse editor config
    $editorConfig = [];
    if (!empty($product['editor_config'])) {
        $editorConfig = json_decode($product['editor_config'], true);
    }
    
    // Apply color change if configured
    $ec_bg_color = isset($editorConfig['bg_color']) ? (bool)$editorConfig['bg_color'] : false;
    
    if ($ec_bg_color && !empty($editorConfig['preset_colors'])) {
        $colors = explode(',', $editorConfig['preset_colors']);
        $applyColor = trim($colors[0]); // First preset color
        $threshold = 80; // Default threshold
        
        // Apply color change to dark areas
        $rgb = sscanf($applyColor, "#%02x%02x%02x");
        if (count($rgb) === 3) {
            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $height; $y++) {
                    $pixel = imagecolorat($preview, $x, $y);
                    $alpha = ($pixel >> 24) & 0x7F;
                    
                    if ($alpha > 0) {
                        $r = ($pixel >> 16) & 0xFF;
                        $g = ($pixel >> 8) & 0xFF;
                        $b = $pixel & 0xFF;
                        
                        $brightness = ($r + $g + $b) / 3;
                        
                        if ($brightness <= $threshold) {
                            $newColor = imagecolorallocatealpha($preview, $rgb[0], $rgb[1], $rgb[2], $alpha);
                            imagesetpixel($preview, $x, $y, $newColor);
                        }
                    }
                }
            }
        }
    }
    
    // Add text zones if configured
    $textZones = [];
    if (!empty($product['text_zones'])) {
        $textZones = json_decode($product['text_zones'], true);
    }
    
    // Path to a TrueType font (you need to upload one)
    $fontPath = __DIR__ . '/assets/fonts/arial.ttf';
    
    // If no TTF font, use a basic one
    if (!file_exists($fontPath)) {
        // Try common font locations
        $possibleFonts = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            'C:/Windows/Fonts/arial.ttf'
        ];
        
        foreach ($possibleFonts as $font) {
            if (file_exists($font)) {
                $fontPath = $font;
                break;
            }
        }
    }
    
    foreach ($textZones as $zone) {
        $text = $zone['defaultText'] ?? $zone['label'] ?? 'Your Text';
        $fontSize = isset($zone['size']) ? intval($zone['size']) : 30;
        $fontColor = $zone['color'] ?? '#ffffff';
        $x = isset($zone['x']) ? intval($zone['x']) : $width / 2;
        $y = isset($zone['y']) ? intval($zone['y']) : $height / 4;
        
        // Convert hex to RGB
        $hex = str_replace('#', '', $fontColor);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $color = imagecolorallocate($preview, $r, $g, $b);
        
        if (file_exists($fontPath)) {
            // Add text shadow
            $shadowColor = imagecolorallocatealpha($preview, 0, 0, 0, 80);
            
            // Get text bounding box for centering
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
            $textWidth = abs($bbox[2] - $bbox[0]);
            $textX = $x - ($textWidth / 2); // Center align
            
            // Draw shadow
            imagettftext($preview, $fontSize, 0, $textX + 2, $y + 2, $shadowColor, $fontPath, $text);
            // Draw main text
            imagettftext($preview, $fontSize, 0, $textX, $y, $color, $fontPath, $text);
        } else {
            // Fallback: Use GD built-in font (not as nice but works)
            $textWidth = imagefontwidth(5) * strlen($text);
            $textX = $x - ($textWidth / 2);
            imagestring($preview, 5, $textX, $y - 8, $text, $color);
        }
    }
    
    // Save preview image
    $previewDir = 'assets/img/previews/';
    if (!is_dir($previewDir)) {
        mkdir($previewDir, 0755, true);
    }
    
    $previewFilename = 'product_' . $product_id . '_preview.jpg';
    $previewPath = $previewDir . $previewFilename;
    
    // Save as JPEG (smaller file size)
    imagejpeg($preview, $previewPath, 85);
    
    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($preview);
    
    // Update database with preview path
    $updateQuery = "UPDATE products SET preview_image = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $dbPreviewPath = 'previews/' . $previewFilename;
    $updateStmt->bind_param("si", $dbPreviewPath, $product_id);
    $updateStmt->execute();
    $updateStmt->close();
    
    return $previewPath;
}

// Run if called directly
if (isset($_GET['generate_all']) && $_GET['generate_all'] == 1) {
    include_once "config/connect.php";
    
    $query = "SELECT id FROM products WHERE has_customization = 1 AND status = 1";
    $result = mysqli_query($conn, $query);
    
    $generated = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        if (generateProductPreview($row['id'], $conn)) {
            $generated++;
        }
    }
    
    echo "Generated $generated previews successfully!";
    exit;
}

if (isset($_GET['product_id'])) {
    include_once "config/connect.php";
    $product_id = intval($_GET['product_id']);
    $result = generateProductPreview($product_id, $conn);
    
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'preview' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to generate preview']);
    }
    exit;
}
?>