<?php
include ('config/connect.php');
include ('includes/header.php');
?>
<?php
$slider_banners = [];
if (isset($conn) && tableExists($conn, 'banners')) {
    $banner_query = "SELECT * FROM `banners` ORDER BY display_order ASC, id DESC";
    $banner_result = $conn->query($banner_query);

    if ($banner_result && $banner_result->num_rows > 0) {
        while ($row = $banner_result->fetch_assoc()) {
            // Image path check karna
            $b_path = $row['banner_path'] ?? $row['image'] ?? '';
            if (!empty($b_path)) {
                $slider_banners[] = [
                    'title'       => $row['title'] ?? 'Farm-Fresh Spices, Pulses & Dry Fruits',
                    'description' => $row['description'] ?? 'Aquaterra Organics carefully sourced agricultural products from trusted growers to your kitchen, store and business.',
                    'image'       => $b_path,
                    'link_url'    => $row['link_url'] ?? 'products.php'
                ];
            }
        }
    }
}

if (empty($slider_banners)) {
    $slider_banners = [
        [
            'title'       => 'Farm-Fresh Spices, Pulses & Dry Fruits',
            'description' => 'Aquaterra Organics carefully sourced agricultural products from trusted growers to your kitchen, store and business.',
            'image'       => 'uploads/banners/banner_6a9fe95131879.jpg', // Reference fallback
            'link_url'    => 'products.php'
        ],
        [
            'title'       => 'Premium Export Quality, From Farm to Table',
            'description' => 'We bridge the gap between hardworking farmers and global kitchens. Delivering hygienically processed, unadulterated organic products worldwide.',
            'image'       => 'uploads/banners/banner_6a9fe982908a4.jpg', // Reference fallback
            'link_url'    => 'products.php'
        ]
    ];
}
?>

<!-- ==================== HERO SECTION WITH DYNAMIC SLIDER START ==================== -->
<section class="aq-hero-section position-relative overflow-hidden">
    <!-- Bootstrap Carousel for Dynamic Slider -->
    <div id="aqHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6000">

        <!-- Carousel Indicators / Dots -->
        <div class="carousel-indicators aq-carousel-indicators">
            <?php foreach ($slider_banners as $index => $banner): ?>
            <button type="button" data-bs-target="#aqHeroCarousel" data-bs-slide-to="<?php echo $index; ?>"
                <?php echo ($index === 0) ? 'class="active" aria-current="true"' : ''; ?>
                aria-label="Slide <?php echo $index + 1; ?>"></button>
            <?php endforeach; ?>
        </div>

        <!-- Carousel Inner Items -->
        <div class="carousel-inner">
            <?php foreach ($slider_banners as $index => $banner):
                // Image path ko correctly resolve karna
                $img_src = $banner['image'];
                if (!file_exists(__DIR__ . '/' . $img_src)) {
                    // Agar direct path na mile toh uploads folder check karein
                    if (file_exists(__DIR__ . '/admin/' . $img_src)) {
                        $img_src = 'admin/' . $img_src;
                    } elseif (file_exists(__DIR__ . '/uploads/banners/' . basename($img_src))) {
                        $img_src = 'uploads/banners/' . basename($img_src);
                    } else {
                        // Default Unsplash Agriculture placeholder agar image server par na ho
                        $img_src = 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=1920&q=80';
                    }
                }
            ?>
            <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">

                <!-- Background Slide Image with Zoom Animation -->
                <div class="aq-hero-bg" style="background-image: url('<?php echo htmlspecialchars($img_src); ?>');">
                </div>

                <!-- Dark Organic Gradient Overlay for High Readability -->
                <div class="aq-hero-overlay"></div>

                <!-- Hero Content Container -->
                <div class="container aq-hero-container">
                    <div class="row align-items-center min-vh-100 py-5">
                        <div class="col-lg-8 col-xl-7">

                            <!-- Small Subtitle Badge -->
                            <div class="aq-hero-badge animate-fade-in-up">
                                <i class="bi bi-shield-check text-warning"></i>
                                <span>Certified Organic & Export Quality Supplier</span>
                            </div>

                            <!-- Dynamic Main Title -->
                            <h1 class="aq-hero-title animate-fade-in-up delay-1">
                                <?php echo htmlspecialchars($banner['title']); ?>
                            </h1>

                            <!-- Dynamic Description -->
                            <p class="aq-hero-desc animate-fade-in-up delay-2">
                                <?php echo htmlspecialchars($banner['description']); ?>
                            </p>

                            <!-- Action Buttons -->
                            <div class="aq-hero-btns animate-fade-in-up delay-3 d-flex flex-wrap gap-3 pt-2">
                                <a href="products.php" class="btn aq-btn-primary">
                                    <span>Explore products</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                <a href="#order-enquiry" class="btn aq-btn-outline">
                                    <span>Request a quote</span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

        <!-- Carousel Navigation Controls (Left / Right Arrows) -->
        <button class="carousel-control-prev aq-carousel-control" type="button" data-bs-target="#aqHeroCarousel"
            data-bs-slide="prev" aria-label="Previous Slide">
            <span class="carousel-control-prev-icon" aria-hidden="true">
                <i class="bi bi-chevron-left"></i>
            </span>
        </button>
        <button class="carousel-control-next aq-carousel-control" type="button" data-bs-target="#aqHeroCarousel"
            data-bs-slide="next" aria-label="Next Slide">
            <span class="carousel-control-next-icon" aria-hidden="true">
                <i class="bi bi-chevron-right"></i>
            </span>
        </button>

    </div>
</section>

<!-- ==================== FEATURE HIGHLIGHT STRIP (Reference Match) ==================== -->
<section class="aq-feature-strip py-4">
    <div class="container">
        <div class="row g-4 justify-content-between align-items-center">

            <div class="col-md-6 col-lg-3 aq-feature-item">
                <div class="d-flex align-items-start gap-3">
                    <div class="aq-feature-icon">
                        <i class="bi bi-tree-fill"></i>
                    </div>
                    <div>
                        <h4 class="aq-feature-heading">Farm-direct sourcing</h4>
                        <p class="aq-feature-text">Bought from growers we know, not from the open market.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 aq-feature-item">
                <div class="d-flex align-items-start gap-3">
                    <div class="aq-feature-icon">
                        <i class="bi bi-check2-all"></i>
                    </div>
                    <div>
                        <h4 class="aq-feature-heading">Graded batch by batch</h4>
                        <p class="aq-feature-text">Every lot is colour and moisture checked before packing.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 aq-feature-item">
                <div class="d-flex align-items-start gap-3">
                    <div class="aq-feature-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h4 class="aq-feature-heading">Clean, sealed packing</h4>
                        <p class="aq-feature-text">Retail packs and bulk sacks, closed to keep freshness in.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 aq-feature-item">
                <div class="d-flex align-items-start gap-3">
                    <div class="aq-feature-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <h4 class="aq-feature-heading">Dependable dispatch</h4>
                        <p class="aq-feature-text">Clear timelines and updates until your order arrives.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>