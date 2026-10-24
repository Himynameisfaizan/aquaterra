<?php
// Shared helpers — products.php, category.php, about.php, index.php sab yahin se use karte hain
define('ROOT', dirname(__DIR__) . '/');
// $base = site ka root URL (auto). Clean URLs (category/spices) par bhi CSS/images sahi load honge.
$base = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
      . rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/') . '/';

const PH = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400"><rect width="100%" height="100%" fill="%23dfe8da"/></svg>';
// Admin upload folders (content type => folder)
const IMG_DIRS = [
    'products'     => ['admin/assets/img/uploads/'],
    'categories'   => ['admin/uploads/category/'],
    'blogs'        => ['admin/assets/img/uploads/blogs/'],
    'services'     => ['admin/assets/img/uploads/'],
    'gallery'      => ['admin/assets/img/uploads/gallery/', 'admin/uploads/gallery/', 'admin/assets/img/uploads/'],
    'certificates' => ['admin/assets/img/uploads/certificates/', 'admin/uploads/certificates/', 'admin/assets/img/uploads/'],
];
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function q($sql) { global $conn; try { $r = $conn->query($sql); return $r ? $r->fetch_all(MYSQLI_ASSOC) : []; } catch (Throwable $t) { return []; } }
function txt($h, $n = 110) { $t = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode((string)$h)))); return mb_strlen($t) > $n ? mb_substr($t, 0, $n - 1) . '…' : $t; }
function d($i) { return 'style="--d:' . round($i * 0.09, 2) . 's"'; }
function img($f, $type) {
    global $base;
    $f = ltrim(trim(explode(',', (string)$f)[0]), '/');
    if ($f === '') return PH;
    if (preg_match('#^https?://#', $f)) return $f;
    foreach (array_merge(IMG_DIRS[$type] ?? [], ['', 'admin/', "uploads/$type/", 'admin/uploads/', 'admin/assets/img/uploads/']) as $dir)
        if (is_file(ROOT . $dir . $f)) return $base . implode('/', array_map('rawurlencode', explode('/', $dir . $f)));
    return PH;
}
function sec_head($eyebrow, $title) {
    echo '<div class="aq-sec-head text-center mx-auto mb-5 reveal" style="max-width:680px"><span class="aq-eyebrow text-uppercase fw-bold small d-block mb-2">' . e($eyebrow) . '</span><h2 class="serif-heading display-6 fw-bold">' . e($title) . '</h2></div>';
}
// Inner page banner + breadcrumb. $crumbs: string ya [label, url]
function page_hero($title, $crumbs = [], $sub = '') {
    global $base;
    echo '<section class="aq-page-hero"><span class="ring"></span><span class="ring r2"></span><div class="container position-relative"><nav class="aq-crumb" aria-label="breadcrumb"><a href="' . $base . 'index.php">Home</a>';
    foreach ($crumbs as $c) echo '<i class="bi bi-chevron-right"></i>' . (is_array($c) ? '<a href="' . e($c[1]) . '">' . e($c[0]) . '</a>' : '<span>' . e($c) . '</span>');
    echo '</nav><h1 class="aq-page-title">' . e($title) . '</h1>' . ($sub ? '<p class="aq-page-sub">' . e($sub) . '</p>' : '') . '</div></section>';
}
function search_form($action, $qv = '') {
    echo '<form class="aq-search" method="get" action="' . e($action) . '" role="search"><input type="search" name="q" value="' . e($qv) . '" placeholder="Search products…" aria-label="Search products"><button type="submit" aria-label="Search"><i class="bi bi-search"></i></button></form>';
}
// Index page wala hi product card design
function product_card($p, $i = 0) {
    global $base; ?>
<div class="col-md-6 col-xl-4 reveal" <?= d($i % 3) ?>>
    <div class="aq-card shadow-sm position-relative">
        <div class="ph position-relative" style="aspect-ratio:4/3"><img src="<?= e(img($p['pro_img'], 'products')) ?>"
                alt="<?= e($p['pro_name']) ?>" loading="lazy"><?php if ($p['cat']): ?><span
                class="aq-pill"><?= e($p['cat']) ?></span><?php endif; ?></div>
        <div class="p-4">
            <h3 class="h5 fw-bold mb-2"><a href="<?= $base ?>product-details.php?slug=<?= urlencode($p['slug_url']) ?>"
                    class="text-decoration-none text-dark stretched-link"><?= e($p['pro_name']) ?></a></h3>
            <p class="text-muted small line-clamp-2"><?= e(txt($p['short_desc'] ?: $p['description'], 120)) ?></p>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top"><span
                    class="small fw-semibold" style="color:var(--clr-leaf)">View details &amp; quote</span><span
                    class="aq-go"><i class="bi bi-arrow-right"></i></span></div>
        </div>
    </div>
</div>
<?php }
function pager($page, $pages, $url, $params = []) {
    if ($pages < 2) return;
    $href = function ($n) use ($url, $params) { if ($n > 1) $params['page'] = $n; return e($url . ($params ? '?' . http_build_query($params) : '')); };
    $nums = array_filter([1, $page - 1, $page, $page + 1, $pages], fn($n) => $n >= 1 && $n <= $pages); $nums = array_unique($nums); sort($nums);
    echo '<ul class="aq-pager reveal">';
    echo $page > 1 ? '<li><a href="' . $href($page - 1) . '" aria-label="Previous"><i class="bi bi-chevron-left"></i></a></li>' : '<li class="dis"><span><i class="bi bi-chevron-left"></i></span></li>';
    $prev = 0;
    foreach ($nums as $n) {
        if ($n - $prev > 1) echo '<li class="dis"><span>…</span></li>';
        echo $n == $page ? '<li class="on"><span>' . $n . '</span></li>' : '<li><a href="' . $href($n) . '">' . $n . '</a></li>';
        $prev = $n;
    }
    echo $page < $pages ? '<li><a href="' . $href($page + 1) . '" aria-label="Next"><i class="bi bi-chevron-right"></i></a></li>' : '<li class="dis"><span><i class="bi bi-chevron-right"></i></span></li>';
    echo '</ul>';
}
// Sidebar "Request a quote" — contact details contacts table se (dynamic)
function quote_card() {
    global $base, $site_Info;
    $ct = q("SELECT * FROM contacts LIMIT 1")[0] ?? [];
    $phone = $ct['phone'] ?? $site_Info['phone']; $mail = $ct['email'] ?? $site_Info['email']; $addr = $ct['address'] ?? $site_Info['address'];
    $wa = preg_replace('/\D/', '', $ct['wp_number'] ?? ''); if (strlen($wa) === 10) $wa = '91' . $wa; ?>
<div class="aq-side-card aq-qside">
    <h3 class="aq-side-title">Request a quote</h3>
    <p class="small">Product, quantity aur destination batayein — hum rates aur packing options ke saath reply karenge.
    </p>
    <ul class="list-unstyled mb-4">
        <?php if ($addr): ?><li><i class="bi bi-geo-alt-fill"></i><span><?= e($addr) ?></span></li><?php endif; ?>
        <?php if ($phone): ?><li><i class="bi bi-telephone-fill"></i><a class="text-white text-decoration-none"
                href="tel:<?= e(preg_replace('/[^+\d]/', '', $phone)) ?>"><?= e($phone) ?></a></li><?php endif; ?>
        <?php if ($mail): ?><li><i class="bi bi-envelope-fill"></i><a class="text-white text-decoration-none"
                href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></li><?php endif; ?>
        <?php if (!empty($ct['working_hours'])): ?><li><i
                class="bi bi-clock-fill"></i><span><?= e($ct['working_hours']) ?></span></li><?php endif; ?>
    </ul>
    <div class="d-grid gap-2">
        <a class="btn aq-btn-primary justify-content-center" href="<?= $base ?>index.php#order-enquiry"><span>Get a
                quote</span><i class="bi bi-arrow-right"></i></a>
        <?php if ($wa): ?><a class="btn aq-btn-outline justify-content-center py-2" href="https://wa.me/<?= $wa ?>"
            target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i><span>WhatsApp us</span></a><?php endif; ?>
    </div>
</div>
<?php }