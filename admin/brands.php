<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Database central core mapping connection pipeline
include "db-conn.php"; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Brands Management | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
    <style>
        .brand-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .brand-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .search-box {
            position: relative;
            max-width: 400px;
        }

        .search-box input {
            padding-left: 40px;
            border-radius: 20px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .brand-logo-preview {
            width: 65px;
            height: 65px;
            object-fit: contain;
            background-color: #fff;
            border: 1px solid #eee;
            padding: 4px;
            border-radius: 8px;
        }

        .table thead th {
            background-color: #2c3e50;
            color: white;
            font-weight: 500;
        }

        .pagination .page-item.active .page-link {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }
    </style>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <?php include "top_nav.php"; ?>
                </div>
            </div>
        </div>

        <div class="main_content_iner">
            <div class="container-fluid p-3">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="white_card mb_30">
                            <div class="card-header bg-white border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="mb-3 mb-md-0">
                                        <h2 class="mb-0 fw-bold">Brands Management</h2>
                                        <p class="text-muted mb-0 small">Manage your dynamic client logos and industrial associations</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <form method="GET" class="position-relative me-3 search-box">
                                            <i class="fas fa-search"></i>
                                            <input type="text" class="form-control" name="search"
                                                placeholder="Search brands..."
                                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                        </form>
                                        <a href="add-brand.php" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Add New Brand
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="white_card_body">
                                <div class="QA_section">
                                    <div class="QA_table mb_30">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered align-middle text-center">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>ID</th>
                                                        <th>Brand Logo</th>
                                                        <th>Brand Name</th>
                                                        <th>Created At</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sno = 1;
                                                    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                                                    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                                                    $perPage = 10;
                                                    $offset = ($page - 1) * $perPage;

                                                    // 🎯 SCHEMA-BASED TARGETING: Mapping against brands database schema
                                                    $sql = "SELECT * FROM `brands`";
                                                    $countSql = "SELECT COUNT(*) as total FROM `brands`";

                                                    if (!empty($search)) {
                                                        $searchTerm = mysqli_real_escape_string($conn, $search);
                                                        $sql .= " WHERE `brand_name` LIKE '%$searchTerm%' OR `id` LIKE '%$searchTerm%'";
                                                        $countSql .= " WHERE `brand_name` LIKE '%$searchTerm%' OR `id` LIKE '%$searchTerm%'";
                                                    }

                                                    $sql .= " ORDER BY `id` DESC LIMIT $offset, $perPage";

                                                    $result = mysqli_query($conn, $sql);
                                                    $countResult = mysqli_query($conn, $countSql);
                                                    $totalRows = mysqli_fetch_assoc($countResult)['total'];
                                                    $totalPages = ceil($totalRows / $perPage);

                                                    if ($result && mysqli_num_rows($result) > 0) {
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            $brand_id   = $row['id'];
                                                            $brand_name = htmlspecialchars($row['brand_name']);
                                                            $logo_path  = htmlspecialchars($row['logo_path']);
                                                            $created_at = htmlspecialchars($row['created_at']);

                                                            // Image validation path mapping architecture
                                                            $final_logo_src = (!empty($logo_path) && file_exists($logo_path)) ? $logo_path : 'uploads/default-brand.png';
                                                            ?>
                                                            <tr>
                                                                <td><?= $offset + ($sno++) ?></td>
                                                                <td class="fw-bold text-secondary">#<?= $brand_id ?></td>
                                                                <td>
                                                                    <img src="<?= $final_logo_src ?>"
                                                                        alt="<?= $brand_name ?>"
                                                                        class="brand-logo-preview"
                                                                        onerror="this.src='uploads/default-brand.png'">
                                                                </td>
                                                                <td class="fw-bold"><?= $brand_name ?></td>
                                                                <td class="text-muted small"><?= $created_at ?></td>
                                                                <td>
                                                                    <div class="d-flex justify-content-center">
                                                                        <a href="edit-brand.php?id=<?= $brand_id ?>"
                                                                            class="btn btn-outline-info btn-sm me-2" data-bs-toggle="tooltip" title="Edit Brand">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <a href="brand-delete.php?id=<?= $brand_id ?>" 
                                                                           onclick="return confirm('Are you sure you want to permanently delete this client logo?');" 
                                                                           class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete Brand">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="6" class="text-center text-muted py-4">No associated brand logos found</td></tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <?php if ($totalPages > 1): ?>
                                            <div class="d-flex justify-content-between align-items-center mt-4">
                                                <div class="text-muted small">
                                                    Showing <?= $offset + 1 ?> to <?= min($offset + $perPage, $totalRows) ?> of <?= $totalRows ?> entries
                                                </div>
                                                <nav>
                                                    <ul class="pagination mb-0">
                                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
                                                        </li>
                                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                                            </li>
                                                        <?php endfor; ?>
                                                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
                                                        </li>
                                                    </ul>
                                                </nav>
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
    </section>

    <?php include "footer.php"; ?>

    <script>
        // Initialize structural tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>

</html>