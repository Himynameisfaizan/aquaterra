<?php
// google_fonts_list.php - 100% Working English Only Fonts List with API Fallback
function getGoogleFontsList() {
    $cache_file = __DIR__ . '/google_fonts_cache.json';
    $cache_time = 86400; // 24 hours
    
    // Check cache
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        $cached = json_decode(file_get_contents($cache_file), true);
        if (!empty($cached)) return $cached;
    }
    
    // Try Google Fonts API
    $api_url = 'https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity';
    $fonts = [];
    $response = @file_get_contents($api_url);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        if (!empty($data['items'])) {
            foreach ($data['items'] as $font) {
                $fonts[$font['family']] = $font['family'];
            }
            file_put_contents($cache_file, json_encode($fonts));
            return $fonts;
        }
    }
    
    // 🔥 Fallback Static Master List (100% Guaranteed to Work English-Only Fonts)
    return [
        // --- Premium & Trendy Sans-Serif Fonts ---
        'Bebas Neue' => 'Bebas Neue',
        'Montserrat' => 'Montserrat',
        'Poppins' => 'Poppins',
        'Roboto' => 'Roboto',
        'Open Sans' => 'Open Sans',
        'Lato' => 'Lato',
        'Raleway' => 'Raleway',
        'Nunito' => 'Nunito',
        'Ubuntu' => 'Ubuntu',
        'Inter' => 'Inter',
        'Rubik' => 'Rubik',
        'Oswald' => 'Oswald',
        'Quicksand' => 'Quicksand',
        'Josefin Sans' => 'Josefin Sans',
        'Barlow' => 'Barlow',
        'DM Sans' => 'DM Sans',
        
        // --- Premium Serif & Luxury Fonts ---
        'Merriweather' => 'Merriweather',
        'Playfair Display' => 'Playfair Display',
        'Lora' => 'Lora',
        'PT Serif' => 'PT Serif',
        'Libre Baskerville' => 'Libre Baskerville',
        'EB Garamond' => 'EB Garamond',
        'Crimson Text' => 'Crimson Text',
        
        // --- Calligraphy, Script & Handwritten Fonts ---
        'Pacifico' => 'Pacifico',
        'Lobster' => 'Lobster',
        'Dancing Script' => 'Dancing Script',
        'Satisfy' => 'Satisfy',
        'Great Vibes' => 'Great Vibes',
        'Sacramento' => 'Sacramento',
        'Allura' => 'Allura',
        'Caveat' => 'Caveat',
        'Permanent Marker' => 'Permanent Marker',
        
        // --- Modern Coding & Monospace Fonts ---
        'Roboto Mono' => 'Roboto Mono',
        'Source Code Pro' => 'Source Code Pro',
        'Fira Code' => 'Fira Code',
        'Space Mono' => 'Space Mono',
        'Inconsolata' => 'Inconsolata',

        // --- Standard Universal Web-Safe System Fonts ---
        'Arial' => 'Arial',
        'Georgia' => 'Georgia',
        'Impact' => 'Impact',
        'Verdana' => 'Verdana',
        'Times New Roman' => 'Times New Roman',
        'Tahoma' => 'Tahoma',
        'Courier New' => 'Courier New'
    ];
}

$GOOGLE_FONTS = getGoogleFontsList();
?>