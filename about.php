<?php
require_once 'config/connect.php';
require_once 'includes/functions.php';
// ---- Sirf ye section dynamic hai (about_sections table). Baaki sab static. ----
$rows = q("SELECT * FROM about_sections ORDER BY section_order ASC, id ASC");
if (!$rows) $rows = [['title' => 'Trusted Agricultural Products Supplier', 'content' => '<p>Premium whole spices, dry fruits and agricultural products, sourced and exported with care.</p>', 'image_url' => '', 'meta_title' => '', 'meta_desc' => '', 'meta_key' => '']];
$page_meta_title = $rows[0]['meta_title'] ?: 'About Us | Aquaterra Organics';
$page_meta_desc  = $rows[0]['meta_desc'];
$page_meta_key   = $rows[0]['meta_key'];
include ROOT . 'includes/header.php';
page_hero('About Us', ['About'], 'Farm-fresh spices, pulses and dry fruits — sourced with care, graded with discipline, delivered with honesty.');

$values = [['shield-check', 'Integrity', 'Honest grading and honest pricing — what we promise is what ships.'], ['award', 'Quality', 'Every lot is checked for colour, moisture and purity before packing.'], ['flower1', 'Sustainability', 'Respect for the farmers and the land that grow what we sell.'], ['clock-history', 'Reliability', 'Clear timelines and regular updates, from order to delivery.']];
$why = [['tree-fill', 'Direct from growers', 'We buy from growers we know, so you get traceable, farm-fresh produce.'], ['check2-all', 'Strict quality checks', 'Batch-wise inspection keeps grade and flavour consistent.'], ['box-seam', 'Hygienic packing', 'Sealed retail packs and bulk sacks that protect freshness.'], ['layers', 'Flexible quantities', 'From trial lots to container loads, we adapt to your requirement.'], ['file-earmark-text', 'Documentation support', 'Guidance on the paperwork that export shipments need.'], ['chat-heart', 'Responsive team', 'Quick replies and straight answers to every query.']];
$steps = [['Farm sourcing', 'Bought directly from growers we know.'], ['Grading & checks', 'Colour, moisture and purity checked per lot.'], ['Hygienic packing', 'Sealed retail packs and bulk sacks.'], ['Dispatch & updates', 'Clear timelines until delivery.']];
?>

<!-- ============ ABOUT (DYNAMIC) ============ -->
<?php foreach ($rows as $i => $r): $flip = $i % 2; ?>
<section class="aq-sec <?= $i % 2 ? 'bg-snow' : 'bg-cream' ?>">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 position-relative reveal <?= $flip ? 'order-lg-2' : '' ?>">
                <img src="<?= e(img($r['image_url'], 'about_us')) ?>" alt="<?= e($r['title']) ?>"
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
            <div class="col-lg-6 reveal <?= $flip ? 'order-lg-1' : '' ?>" <?= d(2) ?>>
                <div class="ps-lg-4">
                    <span class="aq-eyebrow text-uppercase fw-bold small d-block mb-2">Who we are</span>
                    <h2 class="serif-heading display-6 fw-bold mb-4"><?= e($r['title']) ?></h2>
                    <div class="text-muted line-height-relaxed mb-4"><?= $r['content'] /* admin HTML */ ?></div>
                    <div class="d-flex flex-wrap gap-3"><a href="<?= $base ?>products.php"
                            class="btn aq-btn-primary px-4 py-3"><span>Explore products</span><i
                                class="bi bi-arrow-right"></i></a><a href="<?= $base ?>index.php#order-enquiry"
                            class="btn btn-outline-dark rounded-pill px-4 fw-semibold d-inline-flex align-items-center">Get
                            a quote</a></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endforeach; ?>

<!-- ============ MISSION & VISION (STATIC) ============ -->
<section class="aq-sec bg-sage">
    <div class="container">
        <?php sec_head('Our purpose', 'Mission & Vision'); ?>
        <div class="row g-4">
            <div class="col-lg-6 reveal">
                <div class="aq-vm bg-white border">
                    <div class="ic"><i class="bi bi-bullseye"></i></div>
                    <h3 class="serif-heading fw-bold">Our Mission</h3>
                    <p class="text-muted mb-0">To deliver naturally grown, carefully graded agricultural products to
                        kitchens and businesses around the world — with honest pricing, clear communication and
                        dependable delivery.</p>
                </div>
            </div>
            <div class="col-lg-6 reveal" <?= d(1) ?>>
                <div class="aq-vm dark">
                    <div class="ic"><i class="bi bi-eye"></i></div>
                    <h3 class="serif-heading fw-bold text-white">Our Vision</h3>
                    <p class="mb-0">To be the name buyers trust first when they think of Indian spices, pulses and dry
                        fruits — known for quality that stays consistent, shipment after shipment.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ VALUES (STATIC) ============ -->
<section class="aq-sec bg-snow">
    <div class="container">
        <?php sec_head('What guides us', 'Our Core Values'); ?>
        <div class="row g-4">
            <?php foreach ($values as $i => $v): ?><div class="col-md-6 col-lg-3 reveal" <?= d($i) ?>>
                <div class="aq-card aq-val shadow-sm"><i class="bi bi-<?= $v[0] ?>"></i>
                    <h3 class="h5 fw-bold"><?= $v[1] ?></h3>
                    <p class="text-muted small mb-0"><?= $v[2] ?></p>
                </div>
            </div><?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ WHY CHOOSE US (STATIC) ============ -->
<section class="aq-band" style="padding:90px 0">
    <div class="container">
        <div class="text-center mx-auto mb-5 reveal" style="max-width:680px"><span
                class="aq-eyebrow text-uppercase fw-bold small d-block mb-2" style="color:var(--clr-accent)">Why buyers
                choose us</span>
            <h2 class="serif-heading display-6 fw-bold text-white">Built on Trust, Packed with Care</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($why as $i => $w): ?><div class="col-md-6 col-lg-4 reveal" <?= d($i % 3) ?>>
                <div class="aq-glass"><i class="bi bi-<?= $w[0] ?>"></i>
                    <h3 class="h5 text-white fw-bold mt-3"><?= $w[1] ?></h3>
                    <p class="small mb-0"><?= $w[2] ?></p>
                </div>
            </div><?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ PROCESS (STATIC) ============ -->
<section class="aq-sec bg-sage">
    <div class="container">
        <?php sec_head('How we work', 'From Farm to Your Warehouse'); ?>
        <div class="row g-4 text-center aq-steps">
            <?php foreach ($steps as $i => $s): ?><div class="col-md-6 col-lg-3 aq-step reveal" <?= d($i) ?>>
                <div class="aq-step-no"><?= $i + 1 ?></div>
                <h5 class="fw-bold"><?= $s[0] ?></h5>
                <p class="small text-muted mb-0 mx-auto" style="max-width:230px"><?= $s[1] ?></p>
            </div><?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="bg-cream py-5">
    <div class="container">
        <div class="aq-cta reveal d-lg-flex align-items-center justify-content-between gap-4">
            <div>
                <h3 class="serif-heading">Looking for a reliable supplier?</h3>
                <p class="mb-0">Tell us your requirement — we will share rates, grades and packing options.</p>
            </div>
            <div class="d-flex flex-wrap gap-3 mt-4 mt-lg-0"><a href="<?= $base ?>index.php#order-enquiry"
                    class="aq-btn-dark">Get a quote <i class="bi bi-arrow-right"></i></a><a
                    href="<?= $base ?>products.php" class="aq-btn-dark" style="background:#fff;color:#14221a">Browse
                    products</a></div>
        </div>
    </div>
</section>

<?php include ROOT . 'includes/footer.php'; ?>