<?php
session_start();
include "db-conn.php";

// Update Logic
$msg = "";
$msg_class = "";

if (isset($_POST['update_meta'])) {
    $id = intval($_POST['meta_id']);
    $meta_title = mysqli_real_escape_string($conn, trim($_POST['meta_title']));
    $meta_key = mysqli_real_escape_string($conn, trim($_POST['meta_key']));
    $meta_desc = mysqli_real_escape_string($conn, trim($_POST['meta_desc']));

    $update_query = "UPDATE meta SET meta_title='$meta_title', meta_key='$meta_key', meta_desc='$meta_desc' WHERE id='$id'";

    if (mysqli_query($conn, $update_query)) {
        $msg = "SEO Meta details updated successfully!";
        $msg_class = "alert-success";
    } else {
        $msg = "Failed to update details. Error: " . mysqli_error($conn);
        $msg_class = "alert-danger";
    }
}

// Fetch all pages
$meta_data = mysqli_query($conn, "SELECT * FROM meta ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Pages SEO | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div>
            </div>
        </div>

        <div class="main_content_iner">
            <div class="container-fluid p-3">

                <?php if (!empty($msg)): ?>
                    <div class="alert <?= $msg_class ?> alert-dismissible fade show" role="alert">
                        <?= $msg ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="white_card card_height_100 mb_30 shadow-sm">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Global Pages SEO Management</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Page Name</th>
                                                <th>Page URL</th>
                                                <th>Meta Title</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            while ($row = mysqli_fetch_assoc($meta_data)):
                                            ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><strong><?= htmlspecialchars($row['page_name']); ?></strong></td>
                                                    <td><span class="badge bg-primary"><?= htmlspecialchars($row['page_url']); ?></span></td>
                                                    <td><?= htmlspecialchars($row['meta_title']); ?></td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-sm btn-success edit-meta-btn"
                                                            data-id="<?= $row['id']; ?>"
                                                            data-name="<?= htmlspecialchars($row['page_name']); ?>"
                                                            data-title="<?= htmlspecialchars($row['meta_title']); ?>"
                                                            data-key="<?= htmlspecialchars($row['meta_key']); ?>"
                                                            data-desc="<?= htmlspecialchars($row['meta_desc']); ?>">
                                                            <i class="fas fa-edit"></i> Edit SEO
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
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

    <!-- EDIT META MODAL -->
    <div class="modal fade" id="editMetaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title text-white">Update SEO for: <span id="display_page_name" class="text-warning"></span></h5>
                    <button type="button" class="btn-close btn-close-white close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="meta_id" id="edit_meta_id">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" id="edit_meta_title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Meta Keywords</label>
                            <input type="text" class="form-control" name="meta_key" id="edit_meta_key">
                            <small class="text-muted">Separate keywords with commas (e.g. keyword1, keyword2)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Meta Description</label>
                            <textarea class="form-control" name="meta_desc" id="edit_meta_desc" rows="4"></textarea>
                            <small class="text-muted">Keep between 150-160 characters for best Google results.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_meta" class="btn btn-primary">Save SEO Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <script>
        document.querySelectorAll('.edit-meta-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Fetch data from button attributes
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const title = this.getAttribute('data-title');
                const key = this.getAttribute('data-key');
                const desc = this.getAttribute('data-desc');

                // Populate modal fields
                document.getElementById('edit_meta_id').value = id;
                document.getElementById('display_page_name').textContent = name;
                document.getElementById('edit_meta_title').value = title;
                document.getElementById('edit_meta_key').value = key;
                document.getElementById('edit_meta_desc').value = desc;

                // Show Modal
                const metaModal = new bootstrap.Modal(document.getElementById('editMetaModal'));
                metaModal.show();
            });
        });
    </script>
</body>

</html>