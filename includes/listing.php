<?php
// Products + Category pages ka shared template.
// Expects: $cat (null = saare products | false = category nahi mili | array = category row), $side ('left' | 'right' = sidebar ki side)
$perPage = 9;
$left = ($side ?? 'left') === 'left';
$qs = trim((string)($_GET['q'] ?? ''));
$pageUrl = $cat ? $base . 'category/' . rawurlencode($cat['slug_url']) : $base . 'products.php';
$total = 0; $items = []; $page = 1; $pages = 1;

if ($cat !== false) {
    $where = "p.status = 1 AND IFNULL(p.is_disabled,0) = 0"; $types = ''; $args = [];
    if ($cat) { $where .= " AND p.pro_cate = ?"; $types .= 'i'; $args[] = (int)$cat['cate_id']; }
    if ($qs !== '') { $where .= " AND (p.pro_name LIKE ? OR p.short_desc LIKE ? OR p.description LIKE ?)"; $lk = '%' . $qs . '%'; $types .= 'sss'; array_push($args, $lk, $lk, $lk); }
    $st = $conn->prepare("SELECT COUNT(*) FROM products p WHERE $where");
    if ($args) $st->bind_param($types, ...$args);
    $st->execute(); $total = (int)$st->get_result()->fetch_row()[0];
    $pages = max(1, (int)ceil($total / $perPage));
    $page = min(max(1, (int)($_GET['page'] ?? 1)), $pages); $off = ($page - 1) * $perPage;
    $st = $conn->prepare("SELECT p.*, c.categories AS cat FROM products p LEFT JOIN categories c ON c.cate_id = p.pro_cate WHERE $where ORDER BY p.id DESC LIMIT $perPage OFFSET $off");
    if ($args) $st->bind_param($types, ...$args);
    $st->execute(); $items = $st->get_result()->fetch_all(MYSQLI_ASSOC);
}
$cats = q("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.pro_cate = c.cate_id AND p.status = 1 AND IFNULL(p.is_disabled,0) = 0) AS cnt FROM categories c WHERE c.status = 1 ORDER BY c.id ASC");
$allCount = (int)(q("SELECT COUNT(*) AS n FROM products WHERE status = 1 AND IFNULL(is_disabled,0) = 0")[0]['n'] ?? 0);

include ROOT . 'includes/header.php';
if ($cat === false)  page_hero('Category not found', [['Products', $base . 'products.php'], 'Not found']);
elseif ($cat)        page_hero($cat['categories'], [['Products', $base . 'products.php'], $cat['categories']], txt($cat['meta_desc'], 170));
else                 page_hero('Our Products', ['Products'], 'Farm-fresh spices, pulses, seeds and dry fruits — graded, packed and ready to export.');
?>
<section class="aq-sec bg-cream" style="padding-top:64px">
    <div class="container">
        <div class="row g-4 g-xl-5">

            <!-- ===== SIDEBAR: search + categories + quote ===== -->
            <aside class="col-lg-4 col-xl-3 order-2 order-lg-<?= $left ? 1 : 2 ?>">
                <div class="aq-side">
                    <div class="aq-side-card reveal d-none d-lg-block">
                        <h3 class="aq-side-title">Search</h3><?php search_form($pageUrl, $qs); ?>
                    </div>
                    <div class="aq-side-card reveal" <?= d(1) ?>>
                        <h3 class="aq-side-title">Categories</h3>
                        <ul class="aq-catlist">
                            <li><a href="<?= $base ?>products.php" class="<?= $cat === null ? 'on' : '' ?>">All products
                                    <span><?= $allCount ?></span></a></li>
                            <?php foreach ($cats as $c): ?><li><a
                                    href="<?= $base ?>category/<?= rawurlencode($c['slug_url']) ?>"
                                    class="<?= ($cat && $cat['id'] == $c['id']) ? 'on' : '' ?>"><?= e($c['categories']) ?>
                                    <span><?= (int)$c['cnt'] ?></span></a></li><?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="reveal" <?= d(2) ?>><?php quote_card(); ?></div>
                </div>
            </aside>

            <!-- ===== PRODUCTS ===== -->
            <div class="col-lg-8 col-xl-9 order-1 order-lg-<?= $left ? 2 : 1 ?>">
                <div class="d-lg-none mb-4"><?php search_form($pageUrl, $qs); ?></div>
                <?php if ($cat === false): ?>
                <div class="aq-empty reveal"><i class="bi bi-compass"></i>
                    <h3 class="serif-heading mt-3">Ye category nahi mili</h3>
                    <p class="text-muted">Link galat ho sakta hai ya category hata di gayi hai.</p><a
                        href="<?= $base ?>products.php" class="btn aq-btn-primary"><span>Saare products dekhein</span><i
                            class="bi bi-arrow-right"></i></a>
                </div>
                <?php else: ?>
                <div class="aq-bar reveal">
                    <div class="text-muted small"><?php if ($total): ?>Showing <b
                            class="text-dark"><?= $off + 1 ?>–<?= $off + count($items) ?></b> of <b
                            class="text-dark"><?= $total ?></b> products<?php else: ?>0 products<?php endif; ?>
                        <?php if ($qs !== ''): ?> for “<b class="text-dark"><?= e($qs) ?></b>” <a
                            href="<?= e($pageUrl) ?>" class="ms-2 text-decoration-none" style="color:var(--clr-leaf)"><i
                                class="bi bi-x-circle"></i> Clear</a><?php endif; ?></div>
                    <?php if ($cat): ?><span class="aq-pill"
                        style="position:static"><?= e($cat['categories']) ?></span><?php endif; ?>
                </div>
                <?php if ($items): ?>
                <div class="row g-4"><?php foreach ($items as $i => $p) product_card($p, $i); ?></div>
                <?php pager($page, $pages, $pageUrl, $qs !== '' ? ['q' => $qs] : []); ?>
                <?php else: ?>
                <div class="aq-empty reveal"><i class="bi bi-search-heart"></i>
                    <h3 class="serif-heading mt-3">Koi product nahi mila</h3>
                    <p class="text-muted">Dusra keyword try karein ya sidebar se category chunein. Chahein to apni
                        requirement bhej dein — hum arrange kar denge.</p><a href="<?= $base ?>index.php#order-enquiry"
                        class="btn aq-btn-primary"><span>Request a quote</span><i class="bi bi-arrow-right"></i></a>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
<?php include ROOT . 'includes/footer.php'; ?>