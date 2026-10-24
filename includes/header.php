<?php
$base = $base ?? '';
// Database Connection Check (Agar pehle se db connection include nahi hai to yahan connect karega)
if (!isset($conn) && !isset($pdo)) {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'aquaterra'; // Aapke SQL dump ka database name

    $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        // Agar custom config file hai to usko check karein
        if (file_exists(__DIR__ . '/config.php')) {
            include_once __DIR__ . '/config.php';
        } elseif (file_exists(__DIR__ . '/db.php')) {
            include_once __DIR__ . '/db.php';
        }
    } else {
        $conn->set_charset("utf8mb4");
    }
}

// Default Company Details (GST Certificate ke hisaab se)
$site_Info = [
    'company_name' => 'AQUATERRA ORGANICS',
    'brand_main'   => 'Aquaterra',
    'brand_sub'    => 'ORGANICS',
    'gst_no'       => '37ACOFA7394P1ZB',
    'address'      => '2-84, Devarapalle, Village Thimiram, Anakapalli, Andhra Pradesh - 531075',
    'phone'        => '+91 98765 43210',
    'email'        => 'info@aquaterraorganics.com',
    'logo_path'    => '' // Agar backend se logo aayega to yahan store hoga
];

// Helper Function: Check if table exists in Database
function tableExists($conn, $tableName) {
    if (!$conn || $conn->connect_error) return false;
    $res = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($tableName) . "'");
    return ($res && $res->num_rows > 0);
}

// 1. Backend Settings / Contact Table se Dynamic Logo aur Details Fetch Karna
$settings_tables = ['contacts', 'site_settings', 'settings', 'contact_info', 'company_info', 'general_settings'];
foreach ($settings_tables as $tbl) {
    if (tableExists($conn, $tbl)) {
        $res = $conn->query("SELECT * FROM `$tbl` LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            
            // Dynamic Logo Check (alag-alag column names ko support karta hai)
            $logo_cols = ['logo', 'site_logo', 'logo_url', 'logo_path', 'header_logo', 'image'];
            foreach ($logo_cols as $col) {
                if (!empty($row[$col])) {
                    $site_Info['logo_path'] = trim($row[$col]);
                    break;
                }
            }

            // Dynamic Phone, Email, Address Check
            if (!empty($row['phone']))   $site_Info['phone']   = $row['phone'];
            if (!empty($row['mobile']))  $site_Info['phone']   = $row['mobile'];
            if (!empty($row['email']))   $site_Info['email']   = $row['email'];
            if (!empty($row['address'])) $site_Info['address'] = $row['address'];
            if (!empty($row['site_name']) || !empty($row['company_name'])) {
                $fetched_name = !empty($row['company_name']) ? $row['company_name'] : $row['site_name'];
                // Agar पुराना default naam 'Bhagirath' hai to AQUATERRA ORGANICS hi rakhein
                if (stripos($fetched_name, 'bhagirath') === false) {
                    $site_Info['company_name'] = $fetched_name;
                }
            }
            break;
        }
    }
}

// Logo `logos` table se (location = header, active)
if (empty($site_Info['logo_path']) && tableExists($conn, 'logos')) {
    $lr = $conn->query("SELECT logo_path FROM logos WHERE location='header' AND is_active=1 ORDER BY id DESC LIMIT 1");
    if ($lr && $lr->num_rows) $site_Info['logo_path'] = $lr->fetch_assoc()['logo_path'];
}

// Verify ki Logo File Server par Asal Mein Exist Karti Hai Ya Nahi
$has_valid_logo = false;
$final_logo_url = '';
if (!empty($site_Info['logo_path'])) {
    $possible_paths = [
        $site_Info['logo_path'],
        'uploads/' . ltrim($site_Info['logo_path'], '/'),
        'uploads/logo/' . ltrim($site_Info['logo_path'], '/'),
        'admin/assets/img/uploads/' . ltrim($site_Info['logo_path'], '/'),
        'admin/uploads/logo/' . ltrim($site_Info['logo_path'], '/'),
        'admin/uploads/' . ltrim($site_Info['logo_path'], '/'),
        'assets/images/' . ltrim($site_Info['logo_path'], '/')
    ];
    foreach ($possible_paths as $p) {
        if (file_exists(dirname(__DIR__) . '/' . $p) && is_file(dirname(__DIR__) . '/' . $p)) {
            $has_valid_logo = true;
            $final_logo_url = $base . implode('/', array_map('rawurlencode', explode('/', $p)));
            break;
        }
    }
}

// 2. Dynamic Product Categories Fetch Karna (Navbar Dropdown ke liye)
$nav_categories = [];
$cat_tables = ['categories', 'product_categories', 'category'];
foreach ($cat_tables as $ctbl) {
    if (tableExists($conn, $ctbl)) {
        $cat_res = $conn->query("SELECT * FROM `$ctbl` WHERE status=1 ORDER BY id ASC LIMIT 8");
        if ($cat_res && $cat_res->num_rows > 0) {
            while ($c = $cat_res->fetch_assoc()) {
                $cat_name = $c['categories'] ?? $c['name'] ?? $c['category_name'] ?? $c['title'] ?? '';
                $cat_slug = $c['slug_url'] ?? $c['slug'] ?? $c['category_slug'] ?? $c['id'] ?? '';
                if (!empty($cat_name)) {
                    $nav_categories[] = [
                        'name' => $cat_name,
                        'slug' => $cat_slug
                    ];
                }
            }
            break;
        }
    }
}

// Active Page Detection
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($page_meta_title) ? htmlspecialchars($page_meta_title) : htmlspecialchars($site_Info['company_name']) . ' | Farm-Fresh Spices, Pulses & Dry Fruits Supplier'; ?>
    </title>
    <meta name="description"
        content="<?php echo isset($page_meta_desc) ? htmlspecialchars($page_meta_desc) : 'Aquaterra Organics supplies carefully sourced agricultural products, whole spices, pulses, grains, and premium dry fruits directly from trusted growers.'; ?>">
    <?php if (isset($page_meta_key) && !empty($page_meta_key)): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($page_meta_key); ?>">
    <?php endif; ?>

    <!-- Google Fonts: Cormorant Garamond (Premium Editorial Heading Font) & Plus Jakarta Sans (Clean Modern Body Font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/include.css">
    <link rel="stylesheet"
        href="<?php echo $base; ?>assets/css/home.css?v=<?php echo @filemtime(__DIR__ . '/../assets/css/home.css'); ?>">
    <script>
    document.documentElement.classList.add('js')
    </script>
</head>

<body>
    <div id="aqProgress"></div>

    <!-- ==================== MAIN HEADER START ==================== -->
    <header id="aqMainHeader"
        class="aq-header-wrapper <?php echo ($current_page !== 'index.php' && $current_page !== '') ? 'inner-page-header' : ''; ?>">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">

                <!-- 1. Brand Logo (Dynamic Image OR Stylized Typographic Fallback) -->
                <a href="<?php echo $base; ?>index.php" class="aq-brand"
                    aria-label="<?php echo htmlspecialchars($site_Info['company_name']); ?>">
                    <?php if ($has_valid_logo): ?>
                    <!-- Jab Backend se Logo Upload Hoga to Ye Image Dikhegi -->
                    <img src="<?php echo htmlspecialchars($final_logo_url); ?>"
                        alt="<?php echo htmlspecialchars($site_Info['company_name']); ?>" class="aq-brand-img">
                    <?php else: ?>
                    <!-- Jab Logo Nahi Hai to Ye Premium Typographic Brand Name Dikhega -->
                    <div class="aq-text-logo">
                        <span class="aq-text-logo-main">
                            <?php echo htmlspecialchars($site_Info['brand_main']); ?>
                            <i class="bi bi-flower3 aq-text-logo-leaf" aria-hidden="true"></i>
                        </span>
                        <div class="aq-text-logo-sub">
                            <span><?php echo htmlspecialchars($site_Info['brand_sub']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </a>

                <!-- 2. Desktop Navigation Links + CTA Button -->
                <div class="d-none d-lg-flex align-items-center gap-3">
                    <nav aria-label="Main Navigation">
                        <ul class="aq-nav-list">
                            <li>
                                <a href="<?php echo $base; ?>index.php"
                                    class="aq-nav-link <?php echo ($current_page == 'index.php' || $current_page == '') ? 'active' : ''; ?>">Home</a>
                            </li>
                            <li>
                                <a href="<?php echo $base; ?>about.php"
                                    class="aq-nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a>
                            </li>

                            <!-- Products Link (With Dynamic Category Dropdown if Categories Exist) -->
                            <?php if (!empty($nav_categories)): ?>
                            <li class="aq-dropdown">
                                <a href="<?php echo $base; ?>products.php"
                                    class="aq-nav-link <?php echo (in_array($current_page, ['products.php','category.php']) || $current_page == 'product-details.php') ? 'active' : ''; ?>">
                                    Products <i class="bi bi-chevron-down" style="font-size: 0.72rem;"></i>
                                </a>
                                <ul class="aq-dropdown-menu">
                                    <?php foreach ($nav_categories as $navCat): ?>
                                    <li>
                                        <a href="<?php echo $base; ?>category/<?php echo rawurlencode($navCat['slug']); ?>"
                                            class="aq-dropdown-item">
                                            <span><?php echo htmlspecialchars($navCat['name']); ?></span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <a href="<?php echo $base; ?>products.php" class="aq-dropdown-item fw-semibold"
                                            style="color: var(--clr-primary);">
                                            <span>View All Products</span>
                                            <i class="bi bi-grid-fill" style="opacity: 1;"></i>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php else: ?>
                            <li>
                                <a href="<?php echo $base; ?>products.php"
                                    class="aq-nav-link <?php echo (in_array($current_page, ['products.php','category.php']) || $current_page == 'product-details.php') ? 'active' : ''; ?>">Products</a>
                            </li>
                            <?php endif; ?>

                            <li>
                                <a href="<?php echo $base; ?>index.php#services"
                                    class="aq-nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">Services</a>
                            </li>
                            <li>
                                <a href="<?php echo $base; ?>gallery.php"
                                    class="aq-nav-link <?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">Gallery</a>
                            </li>
                            <li>
                                <a href="<?php echo $base; ?>blog.php"
                                    class="aq-nav-link <?php echo ($current_page == 'blog.php' || $current_page == 'blog-details.php') ? 'active' : ''; ?>">Blog</a>
                            </li>
                        </ul>
                    </nav>

                    <!-- Get a Quote CTA Button (Reference Design Match) -->
                    <a href="<?php echo $base; ?>index.php#order-enquiry" class="btn-aq-quote ms-2">
                        Get a quote
                    </a>
                </div>

                <!-- 3. Mobile Hamburger Menu Toggle -->
                <div class="d-flex align-items-center gap-2 d-lg-none">
                    <a href="<?php echo $base; ?>index.php#order-enquiry" class="btn-aq-quote py-2 px-3"
                        style="font-size: 0.82rem;">
                        Get a quote
                    </a>
                    <button class="aq-mobile-toggle" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#aqMobileMenu" aria-controls="aqMobileMenu" aria-label="Toggle Menu">
                        <i class="bi bi-list"></i>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <!-- ==================== MOBILE OFFCANVAS MENU ==================== -->
    <div class="offcanvas offcanvas-end aq-offcanvas" tabindex="-1" id="aqMobileMenu"
        aria-labelledby="aqMobileMenuLabel">
        <div class="offcanvas-header">
            <div id="aqMobileMenuLabel">
                <?php if ($has_valid_logo): ?>
                <img src="<?php echo htmlspecialchars($final_logo_url); ?>"
                    alt="<?php echo htmlspecialchars($site_Info['company_name']); ?>" style="max-height: 42px;">
                <?php else: ?>
                <div class="aq-text-logo align-items-start">
                    <span class="aq-text-logo-main" style="font-size: 1.75rem;">
                        <?php echo htmlspecialchars($site_Info['brand_main']); ?>
                        <i class="bi bi-flower3 aq-text-logo-leaf"></i>
                    </span>
                    <div class="aq-text-logo-sub">
                        <span><?php echo htmlspecialchars($site_Info['brand_sub']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column justify-content-between p-4">
            <div>
                <ul class="aq-mobile-nav">
                    <li>
                        <a href="<?php echo $base; ?>index.php"
                            class="<?php echo ($current_page == 'index.php' || $current_page == '') ? 'active' : ''; ?>">
                            <span>Home</span> <i class="bi bi-chevron-right small"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>about.php"
                            class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">
                            <span>About Us</span> <i class="bi bi-chevron-right small"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>products.php"
                            class="<?php echo (in_array($current_page, ['products.php','category.php'])) ? 'active' : ''; ?>">
                            <span>Our Products</span> <i class="bi bi-chevron-right small"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>index.php#services">
                            <span>Services</span> <i class="bi bi-chevron-right small"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>gallery.php"
                            class="<?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">
                            <span>Gallery</span> <i class="bi bi-chevron-right small"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>blog.php"
                            class="<?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>">
                            <span>Blog & Insights</span> <i class="bi bi-chevron-right small"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>contact.php"
                            class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
                            <span>Contact Us</span> <i class="bi bi-chevron-right small"></i>
                        </a>
                    </li>
                </ul>

                <div class="mt-4">
                    <a href="<?php echo $base; ?>index.php#order-enquiry"
                        class="btn-aq-quote w-100 justify-content-center py-3" data-bs-dismiss="offcanvas">
                        <i class="bi bi-send-fill"></i> Request Bulk Quote
                    </a>
                </div>
            </div>

            <!-- Company Info Footer inside Mobile Drawer -->
            <div class="pt-4 mt-4 border-top border-secondary border-opacity-25 small text-white-50">
                <div class="aq-gst-badge mb-3">
                    <i class="bi bi-patch-check-fill" style="color: var(--clr-accent);"></i>
                    <span>GSTIN: <?php echo htmlspecialchars($site_Info['gst_no']); ?></span>
                </div>
                <p class="mb-2 d-flex gap-2">
                    <i class="bi bi-geo-alt-fill" style="color: var(--clr-accent);"></i>
                    <span><?php echo htmlspecialchars($site_Info['address']); ?></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Scroll Script for Sticky Header Animation -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.getElementById('aqMainHeader');
        const handleScroll = () => {
            if (window.scrollY > 40) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', handleScroll, {
            passive: true
        });
        handleScroll();
    });
    </script>
    <!-- ==================== MAIN HEADER END ==================== -->