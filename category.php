<?php
require_once 'config/connect.php';
require_once 'includes/functions.php';
// URL: category/{slug}  (.htaccess is ko category.php?slug= par bhejta hai)
$slug = trim(rawurldecode((string)($_GET['slug'] ?? '')), '/');
$st = $conn->prepare("SELECT * FROM categories WHERE slug_url = ? AND status = 1 LIMIT 1");
$st->bind_param('s', $slug); $st->execute();
$cat = $st->get_result()->fetch_assoc() ?: false;
$side = 'right';               // sidebar RIGHT, products LEFT
if ($cat) {
    $page_meta_title = $cat['meta_title'] ?: $cat['categories'] . ' | Aquaterra Organics';
    $page_meta_desc  = $cat['meta_desc'];
    $page_meta_key   = $cat['meta_key'];
} else { http_response_code(404); $page_meta_title = 'Category not found | Aquaterra Organics'; }
include 'includes/listing.php';