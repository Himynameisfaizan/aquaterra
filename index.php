<?php
include('config/connect.php');
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['csrf'] ??= bin2hex(random_bytes(16));

include 'includes/functions.php';

// ---------- INQUIRY FORM HANDLER ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'inquiry') {
    $n = trim($_POST['name'] ?? ''); $em = trim($_POST['email'] ?? ''); $ph = mb_substr(trim($_POST['phone'] ?? ''), 0, 20);
    $pr = trim($_POST['product'] ?? ''); $msg = trim($_POST['message'] ?? '');
    $valid = hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '') && $n !== '' && $msg !== '' && filter_var($em, FILTER_VALIDATE_EMAIL) && empty($_POST['website']);
    $_SESSION['flash'] = 'invalid';
    if ($valid) {
        $subject = 'Quote request' . ($pr !== '' ? ': ' . $pr : '');
        $st = $conn->prepare("INSERT INTO inquiries (name, email, phone, subject, message, status) VALUES (?,?,?,?,?,0)");
        $st->bind_param('sssss', $n, $em, $ph, $subject, $msg);
        $_SESSION['flash'] = $st->execute() ? 'ok' : 'error';
    }
    header('Location: index.php#order-enquiry'); exit;
}
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);

include('includes/header.php');

// ---------- DATA ----------
$slides = q("SELECT * FROM banners WHERE (start_date IS NULL OR start_date <= NOW()) AND (end_date IS NULL OR end_date >= NOW()) ORDER BY display_order ASC, id DESC");
if (!$slides) $slides = [['banner_path' => '', 'title' => 'Farm-Fresh Spices, Pulses & Dry Fruits', 'description' => 'Carefully sourced agricultural products from trusted growers.', 'link_url' => '']];
$about = q("SELECT * FROM about_sections ORDER BY section_order ASC LIMIT 1")[0] ?? ['title' => 'Trusted Agricultural Products Supplier', 'content' => '<p>Premium whole spices, dry fruits and agricultural products, sourced and exported with care.</p>', 'image_url' => ''];
$cats     = q("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.pro_cate = c.cate_id AND p.status = 1) AS cnt FROM categories c WHERE c.status = 1 ORDER BY c.id ASC");
$products = q("SELECT p.*, c.categories AS cat FROM products p LEFT JOIN categories c ON c.cate_id = p.pro_cate WHERE p.status = 1 AND IFNULL(p.is_disabled,0) = 0 ORDER BY p.id DESC LIMIT 8");
$prodNames = array_column(q("SELECT pro_name FROM products WHERE status = 1 ORDER BY pro_name"), 'pro_name');
$services = q("SELECT * FROM services ORDER BY id ASC");
$certs    = q("SELECT * FROM certificates WHERE status = 1 ORDER BY cert_id DESC");
$testis   = q("SELECT * FROM testimonials WHERE status = 1 ORDER BY display_order DESC, test_id DESC LIMIT 6");
$rate     = q("SELECT ROUND(AVG(rating),1) AS a, COUNT(*) AS n FROM testimonials WHERE status = 1")[0] ?? ['a' => 0, 'n' => 0];
$gallery  = q("SELECT * FROM gallery ORDER BY ID DESC LIMIT 8");
$blogs    = q("SELECT * FROM blogs WHERE status = 1 ORDER BY created_at DESC LIMIT 3");
$ct       = q("SELECT * FROM contacts LIMIT 1")[0] ?? [];
$phone = $ct['phone'] ?? $site_Info['phone']; $mail = $ct['email'] ?? $site_Info['email'];
$stats = [[count($prodNames), 'Products listed'], [count($cats), 'Product categories']];
if ((int)$rate['n'] > 0) $stats[] = [(int)$rate['n'], 'Verified buyer reviews'];
$steps = [['Farm sourcing', 'Bought directly from growers we know.'], ['Grading & checks', 'Colour, moisture and purity checked per lot.'], ['Hygienic packing', 'Sealed retail packs and bulk sacks.'], ['Dispatch & updates', 'Clear timelines until delivery.']];
$quality = [['droplet-half', 'Moisture & purity checks', 'Every lot is tested before it is packed.'], ['upc-scan', 'Batch traceability', 'Each batch can be traced back to its source.'], ['box2-heart', 'Food-grade packaging', 'Clean, sealed packs that protect freshness.'], ['file-earmark-text', 'Export documentation', 'Help with the paperwork for your shipment.']];
$faqs = [['What is the minimum order quantity (MOQ)?', 'MOQ depends on the product — it is mentioned on each product page. For anything else, send us an inquiry and we will confirm quickly.'], ['Can I get a sample before a bulk order?', 'Yes, please mention the product in the inquiry form and our team will share sample and grade details.'], ['What packing options do you offer?', 'Retail packs and bulk sacks. Tell us your requirement and we will suggest the right packing.'], ['Do you export outside India?', 'Yes. Share your destination country and quantity, and we will guide you on timelines and documentation.'], ['How do I get the latest price?', 'Use the “Get a quote” form — rates vary with grade, quantity and packing, so we quote per requirement.']];
?>

<!-- ==================== HERO ==================== -->
<section class="aq-hero-section position-relative overflow-hidden">
    <div id="aqHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6000">
        <div class="carousel-indicators aq-carousel-indicators">
            <?php foreach ($slides as $i => $s): ?><button type="button" data-bs-target="#aqHeroCarousel"
                data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?>
                aria-label="Slide <?= $i + 1 ?>"></button><?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($slides as $i => $s): ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div class="aq-hero-bg" style="background-image:url('<?= e(img($s['banner_path'], 'banners')) ?>')">
                </div>
                <div class="aq-hero-overlay"></div>
                <div class="container aq-hero-container">
                    <div class="row align-items-center min-vh-100 py-5">
                        <div class="col-lg-8 col-xl-7">
                            <div class="aq-hero-badge"><i class="bi bi-shield-check text-warning"></i><span>Certified
                                    Organic &amp; Export Quality Supplier</span></div>
                            <h1 class="aq-hero-title"><?= e($s['title']) ?></h1>
                            <p class="aq-hero-desc"><?= e($s['description']) ?></p>
                            <div class="aq-hero-btns d-flex flex-wrap gap-3 pt-2">
                                <a href="<?= e($s['link_url'] ?: 'products.php') ?>"
                                    class="btn aq-btn-primary"><span>Explore products</span><i
                                        class="bi bi-arrow-right"></i></a>
                                <a href="#order-enquiry" class="btn aq-btn-outline"><span>Request a quote</span></a>
                            </div>
                            <div class="aq-hero-chips"><span><i class="bi bi-patch-check-fill"></i>GST
                                    registered</span><span><i class="bi bi-globe-asia-australia"></i>Export
                                    ready</span><span><i class="bi bi-box-seam-fill"></i>Bulk &amp; retail packs</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($slides) > 1): ?>
        <button class="carousel-control-prev aq-carousel-control" type="button" data-bs-target="#aqHeroCarousel"
            data-bs-slide="prev" aria-label="Previous"><span class="carousel-control-prev-icon"><i
                    class="bi bi-chevron-left"></i></span></button>
        <button class="carousel-control-next aq-carousel-control" type="button" data-bs-target="#aqHeroCarousel"
            data-bs-slide="next" aria-label="Next"><span class="carousel-control-next-icon"><i
                    class="bi bi-chevron-right"></i></span></button>
        <?php endif; ?>
    </div>
</section>

<!-- ==================== WHY CHOOSE US (STATIC) ==================== -->
<section class="aq-feature-strip py-4">
    <div class="container">
        <div class="row g-4">
            <?php foreach ([['tree-fill', 'Farm-direct sourcing', 'Bought from growers we know, not from the open market.'], ['check2-all', 'Graded batch by batch', 'Every lot is colour and moisture checked before packing.'], ['box-seam', 'Clean, sealed packing', 'Retail packs and bulk sacks, closed to keep freshness in.'], ['truck', 'Dependable dispatch', 'Clear timelines and updates until your order arrives.']] as $i => $f): ?>
            <div class="col-md-6 col-lg-3 reveal" <?= d($i) ?>>
                <div class="d-flex align-items-start gap-3">
                    <div class="aq-feature-icon"><i class="bi bi-<?= $f[0] ?>"></i></div>
                    <div>
                        <h4 class="aq-feature-heading"><?= $f[1] ?></h4>
                        <p class="aq-feature-text"><?= $f[2] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($prodNames): ?>
<!-- ==================== PRODUCT TICKER ==================== -->
<div class="aq-marquee" aria-hidden="true">
    <div class="aq-marquee-track">
        <?php for ($k = 0; $k < 2; $k++): ?><span><?php foreach ($prodNames as $n) echo e($n) . ' <i class="bi bi-circle-fill"></i> '; ?></span><?php endfor; ?>
    </div>
</div>
<?php endif; ?>

<!-- ==================== ABOUT ==================== -->
<section class="aq-sec bg-cream">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 position-relative reveal">
                <img src="<?= e(img($about['image_url'], 'about_us')) ?>" alt="<?= e($site_Info['company_name']) ?>"
                    class="rounded-4 shadow-lg w-100 object-fit-cover bg-white" style="min-height:420px">
                <div class="aq-about-badge-box">
                    <div class="d-flex align-items-center gap-3">
                        <div class="aq-badge-icon"><i class="bi bi-award-fill"></i></div>
                        <div>
                            <h6 class="mb-0 fw-bold">Quality First</h6>
                            <p class="mb-0 small text-muted">Every lot is checked before it ships</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 reveal" <?= d(2) ?>>
                <div class="ps-lg-4">
                    <span class="aq-eyebrow text-uppercase fw-bold small d-block mb-2">About
                        <?= e($site_Info['company_name']) ?></span>
                    <h2 class="serif-heading display-6 fw-bold mb-4"><?= e($about['title']) ?></h2>
                    <div class="text-muted line-height-relaxed mb-4"><?= $about['content'] /* admin HTML */ ?></div>
                    <div class="row g-2 mb-4">
                        <?php foreach (['Direct sourcing from trusted growers', 'Hygienic grading and sealed packing', 'Bulk and retail quantities, on time'] as $c): ?>
                        <div class="col-12 d-flex gap-2 align-items-center"><i class="bi bi-check-circle-fill"
                                style="color:var(--clr-leaf)"></i><span class="fw-semibold small"><?= $c ?></span></div>
                        <?php endforeach; ?>
                    </div>
                    <a href="about.php" class="btn aq-btn-primary px-4 py-3"><span>Read our story</span><i
                            class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($cats): ?>
<!-- ==================== CATEGORIES ==================== -->
<section class="aq-sec bg-snow">
    <div class="container">
        <?php sec_head('Product range', 'Shop by Category'); ?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($cats as $i => $c): ?>
            <div class="col-md-6 col-lg-4 reveal" <?= d($i) ?>><a href="category/<?= rawurlencode($c['slug_url']) ?>"
                    class="aq-cat-card">
                    <img src="<?= e(img($c['image'], 'categories')) ?>" alt="<?= e($c['categories']) ?>" loading="lazy">
                    <div class="cap">
                        <h3 class="h4 mb-1 text-white"><?= e($c['categories']) ?></h3><small><?= (int)$c['cnt'] ?>
                            products <i class="bi bi-arrow-right ms-1"></i></small>
                    </div>
                </a></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($products): ?>
<!-- ==================== PRODUCTS (with filter) ==================== -->
<section class="aq-sec bg-sage">
    <div class="container">
        <div class="row align-items-end mb-4 reveal">
            <div class="col-lg-7"><span class="aq-eyebrow text-uppercase fw-bold small d-block mb-2">Fresh from our
                    stock</span>
                <h2 class="serif-heading display-6 fw-bold mb-0">Popular &amp; Newly Added Products</h2>
            </div>
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0"><a href="products.php"
                    class="btn btn-outline-dark rounded-pill px-4 fw-semibold">View all products <i
                        class="bi bi-arrow-right ms-1"></i></a></div>
        </div>
        <?php if (count($cats) > 1): ?><div class="d-flex flex-wrap gap-2 mb-4 reveal"><button class="aq-chip on"
                data-f="all">All</button>
            <?php foreach ($cats as $c): ?><button class="aq-chip"
                data-f="<?= (int)$c['cate_id'] ?>"><?= e($c['categories']) ?></button><?php endforeach; ?></div>
        <?php endif; ?>
        <div class="row g-4">
            <?php foreach ($products as $i => $p): ?>
            <div class="col-md-6 col-lg-3 reveal" data-cat="<?= (int)$p['pro_cate'] ?>" <?= d($i % 4) ?>>
                <div class="aq-card shadow-sm position-relative">
                    <div class="ph position-relative" style="aspect-ratio:4/3"><img
                            src="<?= e(img($p['pro_img'], 'products')) ?>" alt="<?= e($p['pro_name']) ?>"
                            loading="lazy"><?php if ($p['cat']): ?><span
                            class="aq-pill"><?= e($p['cat']) ?></span><?php endif; ?></div>
                    <div class="p-4">
                        <h3 class="h5 fw-bold mb-2"><a href="product-details.php?slug=<?= urlencode($p['slug_url']) ?>"
                                class="text-decoration-none text-dark stretched-link"><?= e($p['pro_name']) ?></a></h3>
                        <p class="text-muted small line-clamp-2">
                            <?= e(txt($p['short_desc'] ?: $p['description'], 120)) ?></p>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top"><span
                                class="small fw-semibold" style="color:var(--clr-leaf)">View details &amp;
                                quote</span><span class="aq-go"><i class="bi bi-arrow-right"></i></span></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ==================== BULK CTA BANNER ==================== -->
<section class="bg-cream pt-5">
    <div class="container">
        <div class="aq-cta reveal d-lg-flex align-items-center justify-content-between gap-4">
            <div>
                <h3 class="serif-heading">Need bulk pricing or samples?</h3>
                <p class="mb-0">Tell us the product and quantity — we will come back with rates and packing options.</p>
            </div>
            <div class="d-flex flex-wrap gap-3 mt-4 mt-lg-0"><a href="#order-enquiry" class="aq-btn-dark">Get a quote <i
                        class="bi bi-arrow-right"></i></a><a href="tel:<?= e(preg_replace('/[^+\d]/', '', $phone)) ?>"
                    class="aq-btn-dark" style="background:#fff;color:#14221a"><i class="bi bi-telephone-fill"></i> Call
                    us</a></div>
        </div>
    </div>
</section>

<?php if ($services): ?>
<!-- ==================== SERVICES ==================== -->
<section id="services" class="aq-sec bg-cream">
    <div class="container">
        <?php sec_head('What we do', 'Our Export & Supply Services'); ?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($services as $i => $s): ?>
            <div class="col-md-6 col-lg-4 reveal" <?= d($i) ?>>
                <div class="aq-card aq-svc shadow-sm position-relative">
                    <div class="ph" style="height:190px"><img src="<?= e(img($s['img_path'], 'services')) ?>"
                            alt="<?= e($s['service_name']) ?>" loading="lazy"></div><span
                        class="no"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="p-4">
                        <h3 class="h5 fw-bold"><?= e($s['service_name']) ?></h3>
                        <p class="text-muted small mb-0"><?= e($s['short_desc']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ==================== STATS BAND ==================== -->
<section class="aq-band">
    <div class="container">
        <div class="row text-center g-4">
            <?php foreach ($stats as $i => $s): ?><div class="col-6 col-md reveal aq-stat" <?= d($i) ?>><b
                    data-count="<?= (int)$s[0] ?>" data-suffix="+">0</b><?= e($s[1]) ?></div><?php endforeach; ?>
            <div class="col-6 col-md reveal aq-stat" <?= d(4) ?>><b>GST</b>Registered business</div>
        </div>
    </div>
</section>

<!-- ==================== QUALITY + PROCESS ==================== -->
<section id="quality" class="aq-sec bg-sage">
    <div class="container">
        <?php sec_head('How we work', 'From Farm to Your Warehouse'); ?>
        <div class="row g-4 text-center aq-steps mb-5">
            <?php foreach ($steps as $i => $s): ?><div class="col-md-6 col-lg-3 aq-step reveal" <?= d($i) ?>>
                <div class="aq-step-no"><?= $i + 1 ?></div>
                <h5 class="fw-bold"><?= $s[0] ?></h5>
                <p class="small text-muted mb-0 mx-auto" style="max-width:230px"><?= $s[1] ?></p>
            </div><?php endforeach; ?>
        </div>
        <div class="row g-3">
            <?php foreach ($quality as $i => $s): ?><div class="col-md-6 col-lg-3 reveal" <?= d($i) ?>>
                <div class="aq-q"><i class="bi bi-<?= $s[0] ?>"></i>
                    <div>
                        <h6 class="fw-bold mb-1"><?= $s[1] ?></h6>
                        <p class="small text-muted mb-0"><?= $s[2] ?></p>
                    </div>
                </div>
            </div><?php endforeach; ?>
        </div>
        <div class="text-center mt-5 reveal">
            <span class="aq-gst"><i class="bi bi-patch-check-fill" style="color:var(--clr-leaf)"></i> Registered
                business · GSTIN <?= e($site_Info['gst_no']) ?></span>
            <?php if ($certs): ?><div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                <?php foreach ($certs as $c): ?><img src="<?= e(img($c['image'], 'certificates')) ?>"
                    alt="<?= e($c['title']) ?>" class="bg-white rounded-3 p-2 shadow-sm" style="height:96px"
                    loading="lazy"><?php endforeach; ?></div><?php endif; ?>
        </div>
    </div>
</section>

<?php if ($testis): ?>
<!-- ==================== TESTIMONIALS ==================== -->
<section class="aq-sec bg-snow">
    <div class="container">
        <?php sec_head('Client feedback', 'What Our Buyers Say'); ?>
        <?php if ((int)$rate['n'] > 0): ?><div class="text-center mb-5 mt-n3 reveal">
            <div class="aq-rate"><b><?= e($rate['a']) ?></b><span
                    class="aq-stars"><?php for ($i = 1; $i <= 5; $i++) echo '<i class="bi bi-star' . ($i <= round($rate['a']) ? '-fill' : '') . '"></i> '; ?></span><small
                    class="text-muted">from <?= (int)$rate['n'] ?> buyer reviews</small></div>
        </div><?php endif; ?>
        <div class="row g-4">
            <?php foreach ($testis as $i => $t): ?>
            <div class="col-md-6 col-lg-4 reveal" <?= d($i % 3) ?>>
                <div class="aq-card aq-quote-card shadow-sm d-flex flex-column">
                    <div class="aq-stars mb-3">
                        <?php for ($k = 1; $k <= 5; $k++) echo '<i class="bi bi-star' . ($k <= (int)$t['rating'] ? '-fill' : '') . '"></i> '; ?>
                    </div>
                    <p class="text-muted flex-grow-1"><?= e(txt($t['message'], 220)) ?></p>
                    <div class="d-flex align-items-center gap-3 pt-3 border-top">
                        <div class="aq-avatar"><?= e(mb_strtoupper(mb_substr($t['name'], 0, 1))) ?></div>
                        <div>
                            <div class="fw-bold"><?= e($t['name']) ?></div><small
                                class="text-muted"><?= e(trim($t['designation'] . ($t['client_company'] ? ', ' . $t['client_company'] : ''), ', ')) ?><?= $t['project_name'] ? ' · ' . e($t['project_name']) : '' ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($gallery): ?>
<!-- ==================== GALLERY ==================== -->
<section class="aq-sec bg-cream">
    <div class="container">
        <?php sec_head('Gallery', 'A Look Inside Our Operations'); ?>
        <div class="row g-3 aq-gallery">
            <?php foreach ($gallery as $i => $g): $src = img($g['image_path'], 'gallery'); ?>
            <div class="col-6 col-md-3 reveal" <?= d($i % 4) ?>><a href="<?= e($src) ?>" class="aq-lb"><img
                        src="<?= e($src) ?>" alt="<?= e($g['image_name']) ?>" class="w-100 object-fit-cover"
                        loading="lazy"></a></div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4"><a href="gallery.php"
                class="btn btn-outline-dark rounded-pill px-4 fw-semibold">View full gallery</a></div>
    </div>
</section>
<?php endif; ?>

<?php if ($blogs): ?>
<!-- ==================== BLOG ==================== -->
<section class="aq-sec bg-snow">
    <div class="container">
        <?php sec_head('Insights', 'Latest from Our Blog'); ?>
        <div class="row g-4">
            <?php foreach ($blogs as $i => $b): ?>
            <div class="col-md-6 col-lg-4 reveal" <?= d($i) ?>>
                <article class="aq-card shadow-sm position-relative">
                    <div class="ph" style="height:210px"><img src="<?= e(img($b['image'], 'blogs')) ?>"
                            alt="<?= e($b['title']) ?>" loading="lazy"></div>
                    <div class="p-4"><small class="text-muted"><i
                                class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($b['created_at'])) ?> ·
                            <?= e($b['author']) ?></small>
                        <h3 class="h5 fw-bold my-2"><a href="blog-details.php?slug=<?= urlencode($b['slug']) ?>"
                                class="text-dark text-decoration-none stretched-link"><?= e($b['title']) ?></a></h3>
                        <p class="text-muted small line-clamp-2 mb-0"><?= e(txt($b['description'], 130)) ?></p>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ==================== FAQ ==================== -->
<section class="aq-sec bg-cream">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php sec_head('Buyer questions', 'Frequently Asked Questions'); ?>
                <div class="accordion reveal" id="aqFaq">
                    <?php foreach ($faqs as $i => $f): ?><div class="accordion-item">
                        <h3 class="accordion-header"><button class="accordion-button <?= $i ? 'collapsed' : '' ?>"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq<?= $i ?>"><?= $f[0] ?></button></h3>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i ? '' : 'show' ?>"
                            data-bs-parent="#aqFaq">
                            <div class="accordion-body text-muted"><?= $f[1] ?></div>
                        </div>
                    </div><?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== INQUIRY / GET A QUOTE ==================== -->
<section id="order-enquiry" class="aq-sec bg-sage">
    <div class="container">
        <div class="aq-quote reveal">
            <div class="aq-quote-info">
                <span class="aq-eyebrow text-uppercase fw-bold small d-block mb-2" style="color:var(--clr-accent)">Get a
                    quote</span>
                <h2 class="serif-heading display-6 fw-bold mb-3">Tell us what you need</h2>
                <p>Product, quantity and destination batayein — hamari team rates aur packing options ke saath reply
                    karegi.</p>
                <ul class="list-unstyled mt-4 d-grid gap-3 mb-0">
                    <li class="d-flex gap-3"><i
                            class="bi bi-geo-alt-fill text-warning"></i><span><?= e($ct['address'] ?? $site_Info['address']) ?></span>
                    </li>
                    <li class="d-flex gap-3"><i class="bi bi-telephone-fill text-warning"></i><a
                            class="text-white text-decoration-none"
                            href="tel:<?= e(preg_replace('/[^+\d]/', '', $phone)) ?>"><?= e($phone) ?></a></li>
                    <li class="d-flex gap-3"><i class="bi bi-envelope-fill text-warning"></i><a
                            class="text-white text-decoration-none" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a>
                    </li>
                    <?php if (!empty($ct['working_hours'])): ?><li class="d-flex gap-3"><i
                            class="bi bi-clock-fill text-warning"></i><span><?= e($ct['working_hours']) ?></span></li>
                    <?php endif; ?>
                </ul>
            </div>
            <form method="post" action="index.php#order-enquiry" class="aq-form text-dark">
                <?php if ($flash === 'ok'): ?><div class="alert alert-success">Thank you! Aapki inquiry mil gayi hai,
                    hum jaldi sampark karenge.</div>
                <?php elseif ($flash): ?><div class="alert alert-danger">Form submit nahi hua. Kripya sahi details bhar
                    ke dobara try karein.</div><?php endif; ?>
                <input type="hidden" name="form" value="inquiry"><input type="hidden" name="csrf"
                    value="<?= e($_SESSION['csrf']) ?>">
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="d-none" aria-hidden="true">
                <div class="row g-3">
                    <div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="Your name *"
                            required></div>
                    <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email *"
                            required></div>
                    <div class="col-md-6"><input type="tel" name="phone" class="form-control"
                            placeholder="Phone / WhatsApp" maxlength="20"></div>
                    <div class="col-md-6"><select name="product" class="form-select">
                            <option value="">Product of interest</option><?php foreach ($prodNames as $n): ?><option>
                                <?= e($n) ?></option><?php endforeach; ?>
                        </select></div>
                    <div class="col-12"><textarea name="message" rows="5" class="form-control"
                            placeholder="Quantity, packing, destination country… *" required></textarea></div>
                    <div class="col-12"><button class="btn aq-btn-primary px-4 py-3" type="submit"><span>Send
                                inquiry</span><i class="bi bi-send-fill"></i></button></div>
                </div>
            </form>
        </div>
        <?php if (str_starts_with($ct['map'] ?? '', 'https://www.google.com/maps/embed')): ?>
        <div class="aq-map mt-5 reveal"><iframe src="<?= e($ct['map']) ?>" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade" title="Our location"></iframe></div><?php endif; ?>
    </div>
</section>

<?php include('includes/footer.php'); ?>