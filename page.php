<?php
include('config/connect.php');

$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';

$pageQuery = mysqli_query($conn, "SELECT * FROM custom_pages WHERE slug_url = '$slug' AND status = 1");
$pageData = mysqli_fetch_assoc($pageQuery);

if (!$pageData) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';

// Database se specific blog fetch karein
$blogQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE slug = '$slug' AND status = 1");
$blog = mysqli_fetch_assoc($blogQuery);


$cate_id = $category['cate_id'] ?? '';
$cat_primary_id = $category['id'] ?? ''; 
$categoryName = $category['categories'] ?? '';


$pageTitle = !empty($pageData['meta_title']) ? $pageData['meta_title'] : $pageData['page_title'];
$meta_description = !empty($pageData['meta_desc']) ? $pageData['meta_desc'] : strip_tags(substr($pageData['content'], 0, 160));
$meta_keywords = $pageData['meta_key'] ?? '';

// Breadcrumb ke liye variable (Breadcrumb include isko use karega)
$breadcrumb_name = $pageData['breadcrumb_title']; 

// Smart Schema Logic
$raw_schema = trim($pageData['schema_markup'] ?? '');
if (!empty($raw_schema)) {
    if (stripos($raw_schema, '<script') === false) {
        $page_schema = "<script type=\"application/ld+json\">\n" . $raw_schema . "\n</script>";
    } else {
        $page_schema = $raw_schema;
    }
} else {
    $page_schema = "";
}

// Phone Number fetch for Call-to-action
$contactQuery = mysqli_query($conn, "SELECT phone FROM contacts LIMIT 1");
$contactInfo = mysqli_fetch_assoc($contactQuery);
$sitePhone = !empty($contactInfo['phone']) ? $contactInfo['phone'] : '+91-8448211202';

include 'includes/header.php';
// Custom Breadcrumb Header
$pageTitle = $pageData['page_title'] ?? 'Dynamic Page';
include 'includes/breadcrumb.php';
?>

<!-- Main Page Content -->
<section class="custom-page-section py-5 bg-light">
    <div class="container">
        <div class="row g-5">
            
            <!-- LEFT COLUMN: Content Area -->
            <div class="col-lg-8">
                <div class="bg-white p-4 p-md-5 rounded shadow-sm border">
                    <h2 class="fw-bold mb-4" style="color:#222; font-size:2rem;"><?= htmlspecialchars($pageData['page_title']); ?></h2>
                    
                    <div class="page-content-body" style="line-height: 1.8; color: #444; font-size: 1.05rem;">
                        <?= $pageData['content']; ?>
                    </div>

                    <!-- Social Share Links -->
                    <div class="social-share mt-5 pt-4 border-top d-flex align-items-center">
                        <span class="fw-bold me-3 text-dark">Share this:</span>
                        <?php $current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($current_url) ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2 rounded-circle" style="width:35px;height:35px;display:flex;align-items:center;justify-content:center;"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode($current_url) ?>" target="_blank" class="btn btn-sm btn-outline-info me-2 rounded-circle" style="width:35px;height:35px;display:flex;align-items:center;justify-content:center;"><i class="fa-brands fa-twitter"></i></a>
                        <a href="https://wa.me/?text=<?= urlencode($current_url) ?>" target="_blank" class="btn btn-sm btn-outline-success me-2 rounded-circle" style="width:35px;height:35px;display:flex;align-items:center;justify-content:center;"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($current_url) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:35px;height:35px;display:flex;align-items:center;justify-content:center;"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Sidebar (Similar to Blog/Category Details) -->
            <div class="col-lg-4">
                <div class="sidebar-widgets position-sticky" style="top: 20px;">
                    
                    <!-- Search Widget -->
                    <div class="widget-box bg-white p-4 rounded shadow-sm border mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Search</h5>
                        <form action="blog.php" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search..." required style="font-size: 0.9rem;">
                            <button type="submit" class="btn text-white px-3" style="background: var(--primary-green, #2b5e2c);"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <!-- Dynamic Categories (Blogs se link) -->
                     <div class="widget-box bg-white p-4 rounded shadow-sm border mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2" style="font-size: 1.1rem; color: #222;">Categories</h5>
                        <ul class="list-unstyled mb-0 category-list">
                            <?php
                            $sideCatQuery = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1 ORDER BY id DESC");
                            while($sideCat = mysqli_fetch_assoc($sideCatQuery)):
                                $isActive = ($sideCat['cate_id'] == $cate_id || $sideCat['slug_url'] == $slug) ? 'fw-bold active-cat' : 'text-muted';
                            ?>
                            <li class="mb-2 pb-2 border-bottom" style="border-color: #f8f9fa !important;">
                                <a href="category.php?slug=<?= htmlspecialchars($sideCat['slug_url']) ?>" class="text-decoration-none d-flex justify-content-between align-items-center category-link <?= $isActive ?>" style="font-size: 0.95rem;">
                                    <span><i class="fa-solid fa-angle-right me-2" style="font-size: 0.75rem;"></i> <?= htmlspecialchars($sideCat['categories']) ?></span>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <!-- Recent Posts Widget -->
                     <div class="sidebar-widget">
                        <h4 class="sidebar-title">Recent Posts</h4>

                        <?php
                        // Fetch 3 Recent Blogs excluding the current one
                        $recentQuery = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 1 AND blog_id != '{$blog['blog_id']}' ORDER BY created_at DESC LIMIT 3");

                        if (mysqli_num_rows($recentQuery) > 0) {
                            while ($recentBlog = mysqli_fetch_assoc($recentQuery)):
                                $r_date = date('M d, Y', strtotime($recentBlog['created_at']));
                                $r_img = !empty($recentBlog['image']) ? 'admin/assets/img/uploads/blogs/' . $recentBlog['image'] : 'https://images.unsplash.com/photo-1615486171448-4228965f7c32?q=80&w=200';
                        ?>
                                <div class="recent-post-item">
                                    <img src="<?php echo $r_img; ?>" alt="<?php echo htmlspecialchars($recentBlog['title']); ?>">
                                    <div class="recent-post-info">
                                        <h4><a href="blog-details.php?slug=<?php echo $recentBlog['slug']; ?>"><?php echo htmlspecialchars($recentBlog['title']); ?></a></h4>
                                        <span><?php echo $r_date; ?></span>
                                    </div>
                                </div>
                        <?php
                            endwhile;
                        } else {
                            echo "<p style='color: #666; font-size: 13px;'>No recent posts available.</p>";
                        }
                        ?>
                    </div>

                    <!-- Request Quote Banner -->
                    <div class="widget-box rounded shadow-sm text-center p-4 text-white" style="background: linear-gradient(135deg, var(--primary-green, #2b5e2c) 0%, #1a3f1b 100%);">
                        <div class="icon-wrap mb-3"><i class="fa-solid fa-headset fs-1 text-white opacity-75"></i></div>
                        <h4 class="fw-bold mb-2 text-white">Need Consultation?</h4>
                        <p class="small mb-4 opacity-75">Reach out to our experts today for customized solutions and pricing.</p>
                        <a href="contact.php" class="btn bg-white w-100 fw-bold shadow-sm mb-3" style="color: var(--primary-green, #2b5e2c);">Request Quote</a>
                        <a href="tel:<?= htmlspecialchars($sitePhone) ?>" class="text-white text-decoration-none small fw-bold"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($sitePhone) ?></a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<style>
    .page-content-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0; }
    .page-content-body h1, .page-content-body h2, .page-content-body h3 { color: #222; margin-top: 25px; margin-bottom: 15px; font-weight: 700; }
    .category-list li:last-child { border-bottom: none !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
    .category-list a:hover { color: var(--primary-green, #2b5e2c) !important; padding-left: 5px; transition: 0.2s; }
</style>

<!-- Footer Section with Inquiry Form (Already in Footer/Inquiry include) -->
<?php include ('includes/inquiry-form.php'); ?>
<?php include 'includes/footer.php'; ?>