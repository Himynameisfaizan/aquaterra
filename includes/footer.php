<?php
$base = $base ?? '';
$ft = [];
if (isset($conn)) { try { $r = $conn->query("SELECT * FROM contacts LIMIT 1"); $ft = $r ? ($r->fetch_assoc() ?: []) : []; } catch (Throwable $t) {} }
$wa = preg_replace('/\D/', '', $ft['wp_number'] ?? ''); if (strlen($wa) === 10) $wa = '91' . $wa;
?>
<footer class="aq-dark aq-foot pt-5 pb-3"
    style="background:linear-gradient(180deg,#0f291e,#0a1d15);color:rgba(255,255,255,.78)">
    <div class="container">
        <div class="row g-4 pb-4">
            <div class="col-lg-4">
                <h3 class="serif-heading text-white"><?= htmlspecialchars($site_Info['brand_main']) ?> <small
                        class="text-warning fs-6"><?= htmlspecialchars($site_Info['brand_sub']) ?></small></h3>
                <p class="small">Carefully sourced spices, pulses, grains and dry fruits — graded, packed and exported
                    with care.</p>
                <div class="d-flex gap-3 fs-5">
                    <?php foreach (['facebook' => 'facebook', 'instagram' => 'instagram', 'twitter' => 'twitter-x', 'linkdin' => 'linkedin'] as $col => $icon) if (!empty($ft[$col])): ?><a
                        href="<?= htmlspecialchars($ft[$col]) ?>" target="_blank" rel="noopener"
                        aria-label="<?= $col ?>"><i class="bi bi-<?= $icon ?>"></i></a><?php endif; ?></div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-white mb-3">Quick links</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="<?= $base ?>index.php">Home</a></li>
                    <li><a href="<?= $base ?>about.php">About</a></li>
                    <li><a href="<?= $base ?>products.php">Products</a></li>
                    <li><a href="<?= $base ?>gallery.php">Gallery</a></li>
                    <li><a href="<?= $base ?>blog.php">Blog</a></li>
                    <li><a href="<?= $base ?>index.php#order-enquiry">Get a quote</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="text-white mb-3">Categories</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <?php foreach ($nav_categories ?? [] as $c): ?><li><a
                            href="<?= $base ?>category/<?= rawurlencode($c['slug']) ?>"><?= htmlspecialchars($c['name']) ?></a>
                    </li><?php endforeach; ?></ul>
            </div>
            <div class="col-lg-3 small">
                <h6 class="text-white mb-3">Contact</h6>
                <p class="mb-2"><i
                        class="bi bi-geo-alt-fill text-warning me-2"></i><?= htmlspecialchars($site_Info['address']) ?>
                </p>
                <p class="mb-2"><i
                        class="bi bi-telephone-fill text-warning me-2"></i><?= htmlspecialchars($site_Info['phone']) ?>
                </p>
                <p class="mb-0"><i
                        class="bi bi-envelope-fill text-warning me-2"></i><?= htmlspecialchars($site_Info['email']) ?>
                </p>
            </div>
        </div>
        <div class="border-top border-light border-opacity-10 pt-3 d-flex flex-wrap justify-content-between small">
            <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($site_Info['company_name']) ?>. All rights
                reserved.</span><span>GSTIN: <?= htmlspecialchars($site_Info['gst_no']) ?></span>
        </div>
    </div>
</footer>

<div class="aq-fab">
    <?php if ($wa): ?><a class="aq-wa" href="https://wa.me/<?= $wa ?>" target="_blank" rel="noopener"
        aria-label="Chat on WhatsApp"><i class="bi bi-whatsapp"></i></a><?php endif; ?>
    <button class="aq-top" type="button" aria-label="Back to top"><i class="bi bi-arrow-up"></i></button>
</div>
<div class="modal fade" id="aqLb" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0"><button type="button"
                class="btn-close btn-close-white ms-auto mb-2" data-bs-dismiss="modal" aria-label="Close"></button><img
                alt="" class="w-100 rounded-4"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base ?>assets/js/main.js?v=<?= @filemtime(__DIR__ . '/../assets/js/main.js') ?>"></script>
</body>

</html>