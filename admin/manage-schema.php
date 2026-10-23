<?php
session_start();
include "db-conn.php";

// Update Logic
$msg = "";
$msg_class = "";

if (isset($_POST['update_schema'])) {
    $id = intval($_POST['schema_id']);
    // Schema code jaisa hai waisa hi aayega, HTML tags ke sath
    $schema_markup = mysqli_real_escape_string($conn, trim($_POST['schema_markup']));

    $update_query = "UPDATE page_schemas SET schema_markup='$schema_markup' WHERE id='$id'";
    
    if (mysqli_query($conn, $update_query)) {
        $msg = "Page Schema updated successfully!";
        $msg_class = "alert-success";
    } else {
        $msg = "Failed to update schema. Error: " . mysqli_error($conn);
        $msg_class = "alert-danger";
    }
}

// Fetch all pages schema
$schema_data = mysqli_query($conn, "SELECT * FROM page_schemas ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Schema Markup | Admin Panel</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <?php include "links.php"; ?>
</head>

<body class="crm_body_bg">
    <?php include "header.php"; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0">
            <div class="row"><div class="col-lg-12 p-0"><?php include "top_nav.php"; ?></div></div>
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
                                        <h3 class="m-0">Global Pages Schema Management</h3>
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
                                                <th>Schema Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $i = 1;
                                            while($row = mysqli_fetch_assoc($schema_data)): 
                                                $has_schema = !empty(trim($row['schema_markup'])) ? '<span class="badge bg-success">Added</span>' : '<span class="badge bg-danger">Empty</span>';
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><strong><?= htmlspecialchars($row['page_name']); ?></strong></td>
                                                <td><span class="badge bg-primary"><?= htmlspecialchars($row['page_url']); ?></span></td>
                                                <td><?= $has_schema; ?></td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-info text-white edit-schema-btn"
                                                        data-id="<?= $row['id']; ?>"
                                                        data-name="<?= htmlspecialchars($row['page_name']); ?>"
                                                        data-schema="<?= htmlspecialchars($row['schema_markup']); ?>">
                                                        <i class="fas fa-code"></i> Edit Schema
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

   <!-- EDIT SCHEMA MODAL -->
    <div class="modal fade" id="editSchemaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title text-white">Update Schema for: <span id="display_page_name" class="text-warning"></span></h5>
                    <!-- FIX: Yahan data-dismiss="modal" add kiya gaya hai aur purana close icon fallback diya hai -->
                    <button type="button" class="btn-close btn-close-white close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="schema_id" id="edit_schema_id">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Schema Markup (JSON-LD)</label>
                            <textarea class="form-control" name="schema_markup" id="edit_schema_markup" rows="12" placeholder="Paste full <script type='application/ld+json'>...</script> here..."></textarea>
                            <small class="text-muted mt-2 d-block">Note: Please include the <code>&lt;script&gt;</code> tags when pasting the schema.</small>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <!-- FIX: Yahan bhi data-dismiss="modal" add kiya gaya hai -->
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_schema" class="btn btn-primary">Save Schema</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <script>
        document.querySelectorAll('.edit-schema-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Populate modal fields
                document.getElementById('edit_schema_id').value = this.getAttribute('data-id');
                document.getElementById('display_page_name').textContent = this.getAttribute('data-name');
                document.getElementById('edit_schema_markup').value = this.getAttribute('data-schema');

                // Show Modal
                const schemaModal = new bootstrap.Modal(document.getElementById('editSchemaModal'));
                schemaModal.show();
            });
        });
    </script>
</body>
</html>