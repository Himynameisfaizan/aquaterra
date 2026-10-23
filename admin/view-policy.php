<?php
session_start();
include "db-conn.php";

// Get policy slug from URL
if (!isset($_GET['slug'])) {
    header("Location: 404.php");
    exit();
}

$slug = mysqli_real_escape_string($conn, $_GET['slug']);

// Fetch policy data
$sql = "SELECT * FROM policies WHERE slug = ? AND is_active = 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$policy = mysqli_fetch_assoc($result);

if (!$policy) {
    header("Location: 404.php");
    exit();
}

// Update view count (if you want to track views)
// You can add a 'views' column to the policies table
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>
        <?= htmlspecialchars($policy['title']) ?> | Policy
    </title>
    <meta name="description" content="<?= htmlspecialchars($policy['meta_description']) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($policy['meta_keywords']) ?>">
    <link rel="icon" href="assets/img/logo.png" type="image/png">

    <?php include "links.php"; ?>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part large_header_bg">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <?php include "top_nav.php"; ?>
                </div>
            </div>
        </div>

        <div class="main_content_iner">
            <div class="container-fluid p-0 sm_padding_15px">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h2 class="text-center">
                                            <?= htmlspecialchars($policy['title']) ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>

                            <div class="white_card_body">
                                <div class="card-body">
                                    <!-- Policy meta information -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-info">
                                                    <?= htmlspecialchars($policy['category']) ?>
                                                </span>
                                                <?php if ($policy['policy_code']): ?>
                                                    <span class="badge bg-secondary">
                                                        <?= htmlspecialchars($policy['policy_code']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="badge bg-primary">Version:
                                                    <?= htmlspecialchars($policy['version']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <small class="text-muted">
                                                Effective Date:
                                                <?= date('F j, Y', strtotime($policy['effective_date'])) ?>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Policy content -->
                                    <div class="policy-content">
                                        <?= $policy['content'] ?>
                                    </div>

                                    <hr>

                                    <!-- Last updated info -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                Last Updated:
                                                <?= date('F j, Y', strtotime($policy['updated_at'])) ?>
                                            </small>
                                        </div>
                                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                            <div class="col-md-6 text-end">
                                                <a href="edit-policy.php?id=<?= $policy['id'] ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit Policy
                                                </a>
                                                <a href="view-all-policies.php" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-list"></i> All Policies
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "footer.php"; ?>

        <style>
            .policy-content {
                line-height: 1.8;
                font-size: 16px;
            }

            .policy-content h1,
            .policy-content h2,
            .policy-content h3,
            .policy-content h4 {
                margin-top: 1.5em;
                margin-bottom: 0.5em;
            }

            .policy-content p {
                margin-bottom: 1em;
            }

            .policy-content ul,
            .policy-content ol {
                margin-bottom: 1em;
                padding-left: 2em;
            }
        </style>
    </section>
</body>

</html>