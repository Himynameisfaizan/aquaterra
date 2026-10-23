<?php
include('config/connect.php');

// SEO Meta Variables
$pageTitle = "Privacy Policy | Bhagirath Enterprises";
$meta_description = "Read the Privacy Policy of Bhagirath Enterprises regarding personal data protection for our premium spices, dry fruits, seeds, and kernels.";
$meta_key = "privacy policy, bhagirath enterprises, spices export, dry fruits, seeds and kernels";

include 'includes/header.php';
include 'includes/breadcrumb.php';
?>

<!-- Main Policy Section (Blog Details Style: Left Content + Right Sidebar) -->
<section class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="row g-5">
            
            <!-- LEFT COLUMN: Policy Content -->
            <div class="col-lg-8">
                <div class="bg-white p-4 p-md-5 rounded shadow-sm border">
                    <h1 class="mb-3" style="color: #2b5e2c; font-weight: 700;">Privacy Policy</h1>
                    <p class="text-muted mb-5"><strong>Last Updated:</strong> <?= date('F d, Y'); ?></p>

                    <p>Welcome to <strong>Bhagirath Enterprises</strong>. We respect your privacy and are committed to protecting your personal data. This Privacy Policy outlines how we collect, use, process, and safeguard your information when you visit our website, purchase our premium agricultural commodities—including <strong>spices, dry fruits, seeds, and kernels</strong>—or use our services.</p>

                    <h4 class="mt-5" style="color: #e65c00; font-size: 1.2rem;">1. Information We Collect</h4>
                    <p>To provide you with a seamless and secure shopping and wholesale inquiry experience, we collect the following types of information:</p>
                    <ul>
                        <li><strong>Personal Identification Information:</strong> Name, email address, phone number, and business/company details (if applicable).</li>
                        <li><strong>Shipping & Billing Information:</strong> Delivery address, billing address, postal code, and destination country for domestic or international freight.</li>
                        <li><strong>Technical Data:</strong> IP address, browser type, time zone setting, operating system, and platform details when browsing our catalog.</li>
                        <li><strong>Trade & Bulk Order Compliance Data:</strong> For bulk and wholesale orders of spices, dry fruits, or seeds, we may collect business credentials, GST/VAT numbers, or import-export licenses as required by trade regulations.</li>
                    </ul>

                    <h4 class="mt-5" style="color: #e65c00; font-size: 1.2rem;">2. How We Use Your Information</h4>
                    <p>We use the information we collect for the following purposes:</p>
                    <ul>
                        <li>To process, pack, and fulfill your orders for spices, dry fruits, seeds, and kernels, ensuring quality dispatch.</li>
                        <li>To communicate with you regarding order confirmations, shipping updates, tracking, and customer support.</li>
                        <li>To process online payments securely and prevent fraudulent activities.</li>
                        <li>To comply with food safety, agricultural export standards, and legal trade obligations.</li>
                        <li>To improve our website functionality, product display, and user experience.</li>
                    </ul>

                    <h4 class="mt-5" style="color: #e65c00; font-size: 1.2rem;">3. Secure Payment Processing</h4>
                    <p>We do not store your credit card, debit card, UPI, or net banking details on our servers. All financial transactions are handled through secure, PCI-DSS compliant third-party payment gateways (such as Razorpay). Your payment data is fully encrypted and protected under strict industry standards.</p>

                    <h4 class="mt-5" style="color: #e65c00; font-size: 1.2rem;">4. Third-Party Data Sharing</h4>
                    <p>We do not sell or trade your personal information. However, trusted third parties assist us in conducting our business operations, including:</p>
                    <ul>
                        <li><strong>Logistics & Shipping Partners:</strong> Courier companies, transport services, and freight forwarders required to deliver your shipments safely.</li>
                        <li><strong>Payment Gateways:</strong> Authorized financial processors for transaction management.</li>
                        <li><strong>Regulatory Authorities:</strong> Government or customs bodies when legally required for export compliance.</li>
                    </ul>

                    <h4 class="mt-5" style="color: #e65c00; font-size: 1.2rem;">5. Cookies and Tracking Technologies</h4>
                    <p>Our website uses cookies to enhance browsing efficiency, maintain session preferences, and analyze site traffic. You can choose to disable cookies through your browser, though certain features of the website may not function properly as a result.</p>

                    <h4 class="mt-5" style="color: #e65c00; font-size: 1.2rem;">6. Data Security</h4>
                    <p>We implement robust administrative, technical, and physical security measures, including SSL encryption and secure firewalls, to protect your personal data against unauthorized access, disclosure, or misuse.</p>

                    <h4 class="mt-5" style="color: #e65c00; font-size: 1.2rem;">7. Contact Us</h4>
                    <p>If you have any questions or concerns regarding this Privacy Policy, please feel free to contact us:</p>
                    <div class="p-3 mt-3 rounded" style="background-color: #f1f5f9; border-left: 4px solid var(--primary-green, #2b5e2c);">
                        <p class="mb-1"><strong>Bhagirath Enterprises</strong></p>
                        <p class="mb-1"><strong>Specialization:</strong> Exporters & Suppliers of Spices, Dry Fruits, Seeds & Kernels</p>
                        <p class="mb-1"><strong>Phone:</strong> +91-8448211202</p>
                        <p class="mb-0"><strong>Email:</strong> support@bhagirathenterprises.com</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Sidebar Widgets -->
            <div class="col-lg-4">
                <div class="sidebar-widgets position-sticky" style="top: 20px;">
                    
                    <!-- Search Widget -->
                    <div class="widget-box bg-white p-4 rounded shadow-sm border mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Search Products</h5>
                        <form action="products.php" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search spices, dry fruits..." required style="font-size: 0.9rem;">
                            <button type="submit" class="btn text-white px-3" style="background: var(--primary-green, #2b5e2c);"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <!-- Categories Widget -->
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

                   

                    <!-- Request Quote Banner -->
                    <div class="widget-box rounded shadow-sm text-center p-4 text-white" style="background: linear-gradient(135deg, var(--primary-green, #2b5e2c) 0%, #1a3f1b 100%);">
                        <div class="icon-wrap mb-3"><i class="fa-solid fa-headset fs-1 text-white opacity-75"></i></div>
                        <h4 class="fw-bold mb-2 text-white">Bulk Spices & Dry Fruits?</h4>
                        <p class="small mb-4 opacity-75">Get premium quality export-grade commodities at wholesale prices.</p>
                        <a href="contact.php" class="btn bg-white w-100 fw-bold shadow-sm mb-3" style="color: var(--primary-green, #2b5e2c);">Request Quote</a>
                        <a href="tel:+918448211202" class="text-white text-decoration-none small fw-bold"><i class="fa-solid fa-phone me-1"></i> +91-8448211202</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<?php 
include ('includes/inquiry-form.php');
include ('includes/footer.php'); 
?>