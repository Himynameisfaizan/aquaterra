<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "db-conn.php";

$msg = "";
$msg_class = "";

// --- Blog Delete karne ka Logic ---
if (isset($_GET['delete_id'])) {$del_id = mysqli_real_escape_string($conn,$_GET['delete_id']);
    
    $img_res = mysqli_query($conn, "SELECT `image` FROM `blogs` WHERE `blog_id` = '$del_id'");
    if (mysqli_num_rows($img_res) > 0) {
        $img_row = mysqli_fetch_assoc($img_res);
        if (!empty($img_row['image']) && file_exists("assets/img/uploads/blogs/" . $img_row['image'])) {
            unlink("assets/img/uploads/blogs/" . $img_row['image']);
        }
    }

    $delete_query = "DELETE FROM `blogs` WHERE `blog_id` = '$del_id'";
    if (mysqli_query($conn, $delete_query)) {$msg = "Blog post deleted successfully!";
        $msg_class = "alert-success";
    }
}

if (isset($_GET['status'])) {
    if($_GET['status'] == 'added') {$msg = "New blog post published successfully!";
        $msg_class = "alert-success";
    } elseif($_GET['status'] == 'updated') {$msg = "Blog post updated successfully!";
        $msg_class = "alert-success";
    }
}

$all_blogs = mysqli_query($conn, "SELECT * FROM `blogs` ORDER BY `blog_id` DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Blogs | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .custom-card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        .blog-thumb { width: 55px; height: 55px; object-fit: cover; border-radius: 5px; }
    </style>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0"><div class="row"><div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div></div></div>

        <div class="main_content_iner">
            <div class="container-fluid p-3">
                
                <?php if (!empty($msg)): ?>
                    <div class="alert <?= $msg_class ?> alert-dismissible fade show" role="alert">
                        <?= $msg ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="white_card custom-card">
                            <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0 fw-bold">Published Blogs</h3>
                                <a href="add-blog.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add New Blog</a>
                            </div>
                            <div class="white_card_body py-3">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Title & URL</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (mysqli_num_rows($all_blogs) > 0): ?>
                                                <?php while($blog = mysqli_fetch_assoc($all_blogs)): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if (!empty($blog['image']) && file_exists("assets/img/uploads/blogs/" . $blog['image'])): ?>
                                                                <img class="blog-thumb" src="assets/img/uploads/blogs/<?= $blog['image']; ?>" alt="blog">
                                                            <?php else: ?>
                                                                <img class="blog-thumb" src="assets/img/uploads/blogs/default-blog.png" alt="default">
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($blog['title']); ?>">
                                                                <?= htmlspecialchars($blog['title']); ?>
                                                            </h6>
                                                            <small class="text-primary d-block text-truncate" style="max-width: 300px;">
                                                                <i class="fas fa-link me-1" style="font-size:0.75rem;"></i><?= htmlspecialchars($blog['slug']); ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?= $blog['status'] == 1 ? 'success' : 'warning'; ?>"><?= $blog['status'] == 1 ? 'Live' : 'Draft'; ?></span>
                                                        </td>
                                                        <td class="text-end">
                                                            <!-- 🔥 THE FIX: Modal hata kar link bana diya 🔥 -->
                                                            <a href="edit-blog.php?id=<?= $blog['blog_id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="blog.php?delete_id=<?= $blog['blog_id']; ?>" onclick="return confirm('Are you sure you want to delete this blog?');" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="4" class="text-center py-4 text-muted">No blog posts found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "footer.php"; ?>
</body>
</html>