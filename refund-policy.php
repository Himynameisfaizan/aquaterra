<?php
include('config/connect.php');

$pageTitle = "Refund and Cancellation Policy | Bhagirath Enterprises";
$meta_description = "Read the refund and cancellation policy of Bhagirath Enterprises for premium spices, dry fruits, seeds, and kernels orders[cite: 23].";
$meta_key = "refund policy, cancellation policy, bhagirath enterprises, spices export";

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<section class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="row g-5">
            
            <!-- LEFT COLUMN: Content -->
            <div class="col-lg-8">
                <div class="bg-white p-4 p-md-5 rounded shadow-sm border">
                    <h1 class="mb-4" style="color: #2b5e2c; font-weight: 700;">Refund and Cancellation Policy</h1>
                    <p class="text-muted mb-5"><strong>Last Updated:</strong> <?= date('F d, Y'); ?></p>

                    <p>At <strong>Bhagirath Enterprises</strong>, we ensure the highest quality of our agricultural commodities, including premium spices, dry fruits, seeds, and kernels[cite: 23]. Because we deal in consumable food products, our refund and cancellation policies are structured to comply with strict food safety, hygiene, and trade standards[cite: 23].</p>

                    <h3 class="mt-5 mb-3" style="color: #e65c00; font-size: 1.3rem;">Cancellation Policy</h3>
                    
                    <h4 class="mt-4" style="font-size: 1.1rem; color: #2b5e2c; font-weight: 600;">1. Order Cancellation Before Dispatch</h4>
                    <p>You may cancel your order within <strong>24 hours</strong> of placing it, provided it has not yet been processed, packed, or dispatched from our facility[cite: 23]. A full refund will be processed immediately upon valid request sent to our official support email[cite: 23].</p>

                    <h4 class="mt-4" style="font-size: 1.1rem; color: #2b5e2c; font-weight: 600;">2. Cancellation After Dispatch</h4>
                    <p>Due to the perishable and consumable nature of spices and dry fruits, orders <strong>cannot be cancelled</strong> once they have been handed over to our logistics partners or shipped[cite: 23].</p>
                    
                    <h4 class="mt-4" style="font-size: 1.1rem; color: #2b5e2c; font-weight: 600;">3. Bulk & Wholesale Orders</h4>
                    <p>For custom-packaged or bulk container export orders of seeds and kernels, cancellations are not permitted once sourcing and processing have commenced[cite: 23]. Advance payments for bulk wholesale orders remain strictly non-refundable[cite: 23].</p>

                    <hr class="my-5">

                    <h3 class="mb-3" style="color: #e65c00; font-size: 1.3rem;">Return and Refund Policy</h3>
                    
                    <h4 class="mt-4" style="font-size: 1.1rem; color: #2b5e2c; font-weight: 600;">1. Eligibility for Returns & Refunds</h4>
                    <p>We <strong>do not accept general returns</strong> (such as change of mind) due to strict food safety and hygiene regulations[cite: 23]. However, replacements or refunds are applicable if[cite: 23]:</p>
                    <ul>
                        <li>The product packaging was severely damaged or compromised during transit[cite: 23].</li>
                        <li>An incorrect product or quantity was delivered[cite: 23].</li>
                    </ul>
                    <p><em>Note: You must notify us within <strong>48 hours of delivery</strong> with unboxing video or photographic proof[cite: 23].</em></p>

                    <h4 class="mt-4" style="font-size: 1.1rem; color: #2b5e2c; font-weight: 600;">2. Refund Timeline</h4>
                    <p>Approved refunds are credited back to the original payment method within <strong>5 to 7 business days</strong>[cite: 23].</p>
                </div>
            </div>

            <!-- RIGHT COLUMN: Sidebar Widgets -->
            <div class="col-lg-4">
                <div class="sidebar-widgets position-sticky" style="top: 20px;">
                    <div class="widget-box bg-white p-4 rounded shadow-sm border mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Search Products</h5>
                        <form action="products.php" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search..." required style="font-size: 0.9rem;">
                            <button type="submit" class="btn text-white px-3" style="background: var(--primary-green, #2b5e2c);"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <div class="widget-box bg-white p-4 rounded shadow-sm border mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Categories</h5>
                        <ul class="list-unstyled mb-0 category-list">
                            <?php
                            $catQuery = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1");
                            if ($catQuery && mysqli_num_rows($catQuery) > 0) {
                                while($catRow = mysqli_fetch_assoc($catQuery)) {
                            ?>
                            <li class="mb-2 pb-2 border-bottom">
                                <a href="category.php?slug=<?= htmlspecialchars($catRow['slug_url']) ?>" class="text-decoration-none d-flex justify-content-between align-items-center text-muted" style="font-size: 0.95rem;">
                                    <span><i class="fa-solid fa-angle-right me-2" style="font-size: 0.75rem;"></i> <?= htmlspecialchars($catRow['categories']) ?></span>
                                </a>
                            </li>
                            <?php 
                                }
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="widget-box rounded shadow-sm text-center p-4 text-white" style="background: linear-gradient(135deg, var(--primary-green, #2b5e2c) 0%, #1a3f1b 100%);">
                        <div class="icon-wrap mb-3"><i class="fa-solid fa-headset fs-1 text-white opacity-75"></i></div>
                        <h4 class="fw-bold mb-2 text-white">Need Help?</h4>
                        <p class="small mb-4 opacity-75">Contact our support team for any order assistance.</p>
                        <a href="contact.php" class="btn bg-white w-100 fw-bold shadow-sm mb-3" style="color: var(--primary-green, #2b5e2c);">Contact Us</a>
                        <a href="tel:+918448211202" class="text-white text-decoration-none small fw-bold"><i class="fa-solid fa-phone me-1"></i> +91-8448211202</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php 
include ('includes/inquiry-form.php');
include 'includes/footer.php'; 
?>