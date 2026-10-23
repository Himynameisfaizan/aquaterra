<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: auth/login.php");
    exit();
}
?>

<nav class="sidebar vertical-scroll dark_sidebar ps-container ps-theme-default ps-active-y">

    <div class="admin-profile text-center py-3">
        <a href="index.php">
            <img src="assets/img/logo_icon.jpg" alt="Admin Avatar" class="rounded-circle" width="60">
            <h6 class="mt-2 text-white">Admin Name</h6>
        </a>
    </div>

    <div class="search-box px-3">
        <input type="text" id="sidebarSearch" class="form-control" placeholder="Search...">
    </div>

    <ul id="sidebar_menu" class="sidebar-menu mt-3">
        <li><a href="index.php"><i class="fas fa-tachometer-alt" style="color: #3498db;"></i> <span>Dashboard</span></a>
        </li>

        <li>
            <a class="has-arrow" href="#"><i class="fas fa-home" style="color: #e74c3c;"></i> <span>Home
                    Content</span></a>
            <ul>
                <li><a href="home-items.php">Add Logo</a></li>
                <li><a href="add-banner.php">Add Banners</a></li>
                <li><a href="about_us.php">Add About</a></li>
                <li><a href="add-about-us-section.php"> About sections</a></li>
                <li><a href="manage-meta.php"> Paage Meta</a></li>
                <li><a href="manage-schema.php"> Page Schema</a></li>
                 <li><a href="add_contact.php"><span>Contact Details</span>
                <li><a href="view-testimonials.php">View Testimonials</a></li>

            </a>
        </li>
            </ul>
        </li>

         <!-- <li>
            <a class="has-arrow" href="#"><i class="fas fa-info-circle" style="color: #d35400;"></i> <span>About Us
                    Page</span></a>
            <ul>
                <!-- <li><a href="about_us.php">Add About</a></li> 
            </ul>
        </li> -->

        <li>
            <a class="has-arrow" href="#"><i class="fas fa-layer-group" style="color: #2ecc71;"></i>
                <span>Categories</span></a>
            <ul>
                <li><a href="add-categories.php">Add Category</a></li>
                <li><a href="view-categories.php">View Categories</a></li>
                <li><a href="add-sub-category.php">Add Sub Category</a></li>
                <li><a href="view-sub-categories.php">View Sub Categories</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="#"><i class="fas fa-box-open" style="color: #f39c12;"></i>
                <span>Products</span></a>
            <ul>
                <li><a href="add-products.php">Add Products</a></li>
                <li><a href="show-products.php">Show Products</a></li>
                <li><a href="show-products-review.php">Products Reviews</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="#"><i class="fas fa-fire" style="color: #ff6b6b;"></i><span>Service</span></a>
            <ul>
                <li><a href="add-service.php">Add Service</a></li>
                <li><a href="view-service.php">View Service</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="#"><i class="fas fa-blog" style="color: #9b59b6;"></i><span>Blogs</span></a>
            <ul>
                <li><a href="blog.php">Show Blogs</a></li>
                <li><a href="add-blog.php">Add Blog</a></li>
            </ul>
        </li>

      

        <!-- <li>
            <a class="has-arrow" href="#"><i class="fas fa-award" style="color: #1abc9c;"></i> <span>Country Export</span></a>
            <ul>
                <li><a href="add-country.php">Add Country</a></li>
                <li><a href="view-country.php">View Country</a></li>
            </ul>
        </li> -->


        <!-- <li>
            <a class="has-arrow" href="#"><i class="fas fa-envelope" style="color: #27ae60;"></i> <span>Contact
                    Page</span></a>
            <ul>
                <li><a href="add_contact.php">Edit Contact Info</a></li>
                <li><a href="new-leads.php">Inquiries</a></li>
            </ul>
        </li> -->

        <!-- <li>
            <a href="about_us.php">
                <i class="fas fa-info-circle" style="color: #d35400;"></i>
                <span>About Us</span>
            </a>
        </li> -->

        <!-- <li>
            <a href="add_contact.php">
                <i class="fa-regular fa-address-book" style="color: #e4d72b;"></i>
                <span>Contact Details</span>
            </a>
        </li> -->

        <!-- <li>
            <a href="new-leads.php">
                <i class="fa-solid fa-inbox" style="color: #3cc008;"></i>
                <span>Inquiries</span>
            </a>
        </li> -->

         <li>
            <a class="has-arrow" href="#"><i class="fas fa-award" style="color: #1abc9c;"></i> <span>Custom Page</span></a>
            <ul>
                <li><a href="add-page.php">Add Page</a></li>
                <li><a href="view-pages.php">View Pages</a></li>
            </ul>
        </li>

        <li>
            <a href="add-gallery.php">
                <i class="fas fa-images" style="color: #8e44ad;"></i>
                <span>Gallery</span>
            </a>
        </li>

        <li>
            <a href="brands.php">
                <i class="fas fa-images" style="color: #8e44ad;"></i>
                <span>Brands</span>
            </a>
        </li>

        <!-- <li>
            <a class="has-arrow" href="#"><i class="fas fa-quote-left" style="color: #16a085;"></i>
                <span>Testimonials</span></a>
            <ul>
                <li><a href="add-testimonial.php">Add Testimonials</a></li>
                <li><a href="view-testimonials.php">View Testimonials</a></li>
            </ul>
        </li> -->

        <!-- <li>
            <a class="has-arrow" href="#"><i class="fas fa-users" style="color: #c0392b;"></i> <span>Customers</span></a>
            <ul>
                <li><a href="all-customers.php">All Customers</a></li>
            </ul>
        </li> -->

        <!-- <li><a href="orders.php"><i class="fas fa-shopping-cart" style="color: #e67e22;"></i> <span>Orders</span></a></li> -->
        <!-- <li>
            <a href="view-all-policies.php"><i class="fas fa-file-contract" style="color: #e67e22;"></i>
                <span>Policies</span>
            </a>
        </li>
        <li>
            <a class="has-arrow" href="#"><i class="fas fa-user-cog" style="color: #7f8c8d;"></i> <span>Users</span></a>
            <ul>
                <li><a href="all-admin.php">All Users</a></li>
                <li><a href="admin-create.php">Create Admin</a></li>
            </ul>
        </li> -->

        <li>
            <a href="auth/logout.php">
                <i class="fas fa-sign-out-alt" style="color: #e74c3c;"></i>
                <span>Log Out</span>
            </a>
        </li>
    </ul>
</nav>

<script>
    document.getElementById('sidebarSearch').addEventListener('input', function () {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('#sidebar_menu li').forEach(item => {
            let text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>