<?php
require_once 'config/connect.php';
require_once 'includes/functions.php';
// Purane links: products.php?category=spices  ->  category/spices (301)
if (!empty($_GET['category'])) { header('Location: ' . $base . 'category/' . rawurlencode($_GET['category']), true, 301); exit; }
$cat = null; $side = 'left';   // sidebar LEFT, products RIGHT
$page_meta_title = 'Our Products | Aquaterra Organics';
$page_meta_desc  = 'Browse spices, pulses, seeds and dry fruits from Aquaterra Organics. Search, filter by category and request a quote.';
include 'includes/listing.php';