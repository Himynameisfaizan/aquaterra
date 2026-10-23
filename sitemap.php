<?php
header("Content-Type: application/xml; charset=utf-8");
include 'config/connect.php';

// config/connect.php se global $site variable utha liya
global $site;
$baseUrl = !empty($site) ? $site : "https://royalblue-gazelle-538620.hostingersite.com/";

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// 1. Static Pages
$staticPages = [
    ["url" => "", "priority" => "1.0", "changefreq" => "daily"],
    ["url" => "about.php", "priority" => "0.8", "changefreq" => "monthly"],
    ["url" => "products.php", "priority" => "0.9", "changefreq" => "daily"],
    ["url" => "blog.php", "priority" => "0.8", "changefreq" => "weekly"],
    ["url" => "contact.php", "priority" => "0.7", "changefreq" => "monthly"],
    ["url" => "terms-condition.php", "priority" => "0.3", "changefreq" => "yearly"],
    ["url" => "privacy-policy.php", "priority" => "0.3", "changefreq" => "yearly"],
    ["url" => "shipping-return.php", "priority" => "0.3", "changefreq" => "yearly"],
    ["url" => "refund-policy.php", "priority" => "0.3", "changefreq" => "yearly"]
];

foreach ($staticPages as $page) {
    echo '<url>';
    echo '<loc>' . $baseUrl . $page['url'] . '</loc>';
    echo '<changefreq>' . $page['changefreq'] . '</changefreq>';
    echo '<priority>' . $page['priority'] . '</priority>';
    echo '</url>';
}

if (isset($conn)) {
    // 2. Dynamic Categories
    $catQuery = mysqli_query($conn, "SELECT cate_id, slug_url FROM categories WHERE status = 1");
    if ($catQuery && mysqli_num_rows($catQuery) > 0) {
        while ($row = mysqli_fetch_assoc($catQuery)) {
            $catVal = !empty($row['slug_url']) ? $row['slug_url'] : $row['cate_id'];
            echo '<url>';
            echo '<loc>' . $baseUrl . 'products.php?category=' . urlencode($catVal) . '</loc>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.8</priority>';
            echo '</url>';
        }
    }

    // 3. Dynamic Products
    $prodQuery = mysqli_query($conn, "SELECT id, slug_url FROM products WHERE status = 1");
    if ($prodQuery && mysqli_num_rows($prodQuery) > 0) {
        while ($row = mysqli_fetch_assoc($prodQuery)) {
            $prodVal = !empty($row['slug_url']) ? $row['slug_url'] : $row['id'];
            echo '<url>';
            // Note: product details mein humne id ya slug jo update kiya tha uske hisaab se yahan parameter match kar lein
            echo '<loc>' . $baseUrl . 'product-details.php?id=' . $prodVal . '</loc>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.8</priority>';
            echo '</url>';
        }
    }

    // 4. Dynamic Blogs
    $blogQuery = mysqli_query($conn, "SELECT slug FROM blogs WHERE status = 1");
    if ($blogQuery && mysqli_num_rows($blogQuery) > 0) {
        while ($row = mysqli_fetch_assoc($blogQuery)) {
            if (!empty($row['slug'])) {
                echo '<url>';
                echo '<loc>' . $baseUrl . 'blog-details.php?slug=' . htmlspecialchars($row['slug']) . '</loc>';
                echo '<changefreq>weekly</changefreq>';
                echo '<priority>0.7</priority>';
                echo '</url>';
            }
        }
    }
}

echo '</urlset>';
?>