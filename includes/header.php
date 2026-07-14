<?php
require_once("admin/model/functions.php");
$allCategories = getAllCategories($conn);
/* ===== BUILD TREE ===== */
$tree = [];
foreach ($allCategories as $cat) {
    $cat['children'] = [];
    $tree[$cat['category_id']] = $cat;
}
$menu = [];
foreach ($tree as $id => &$node) {
    if ($node['parent_id'] == NULL) {
        $menu[$id] = &$node;
    } else {
        if (isset($tree[$node['parent_id']])) {
            $tree[$node['parent_id']]['children'][] = &$node;
        }
    }
}
/**
 * RECURSIVE FUNCTION TO RENDER DESKTOP MENU
 */
function renderDesktopMenu($items)
{
    foreach ($items as $item) {
        $hasChildren = !empty($item['children']);
        // Updated URL to include both name and ID
        $url = 'shop.php?category=' . strtolower(urlencode($item['category'])) . '&id=' . $item['category_id'];
        echo '<li class="nav-item ' . ($hasChildren ? 'dropdown' : '') . '">';
        $arrow = $hasChildren ? '<span class="arrow"> ▶</span>' : '';
        echo '<a class="nav-link ' . ($hasChildren ? 'dropdown-toggle' : '') . '" href="' . $url . '">' . $item['category'] . $arrow . '</a>';
        if ($hasChildren) {
            echo '<ul class="dropdown-menu">';
            renderDesktopMenu($item['children']);
            echo '</ul>';
        }
        echo '</li>';
    }
}
/**
 * RECURSIVE FUNCTION TO RENDER MOBILE MENU
 */
function renderMobileMenu($items) {
    foreach ($items as $item) {
        $hasChildren = !empty($item['children']);
        // Updated URL to include both name and ID
        $url = 'shop.php?category=' . strtolower(urlencode($item['category'])) . '&id=' . $item['category_id'];
        if ($hasChildren) {
            echo '<div class="mb-2">';
            echo '<a data-bs-toggle="collapse" href="#mobCat' . $item['category_id'] . '" class="d-block text-decoration-none"> ' . $item['category'] . ' ▾</a>';
            echo '<div class="collapse ps-3" id="mobCat' . $item['category_id'] . '">';
            echo '<a href="' . $url . '" class="d-block text-decoration-none py-1"> All ' . $item['category'] . ' </a>';
            renderMobileMenu($item['children']);
            echo '</div></div>';
        } else {
            echo '<a href="' . $url . '" class="d-block text-decoration-none py-1"> ' . $item['category'] . ' </a>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vangence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.svg">
    <style>
        .arrow { font-size: 0.7em; margin-left: 5px; opacity: 0.7; }
        /* Dropdown Levels */
        .dropdown-menu .dropdown-menu { display: none !important; margin-top: -35px; margin-left: 100%; }
        .dropdown:hover > .dropdown-menu { display: block !important; }
        .dropdown-menu li:hover > .dropdown-menu { display: block !important; }
        .dropdown-item { display: block !important; }
        /* Mobile Menu Styles */
        .offcanvas-body a { display: block; padding: 10px 0; text-decoration: none; color: #333; }
        .offcanvas-body .ps-3 a { padding-left: 15px; font-size: 0.9em; }
        .nav-link { cursor: pointer; }
    </style>
</head>
<body>
<header class="sticky-top py-3">
    <div class="container-fluid px-lg-5">
        <nav class="navbar navbar-expand-lg p-0">
            <a class="navbar-brand" href="index.php">Vangence</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav">☰</button>
            <div class="collapse navbar-collapse justify-content-center">
                <ul class="navbar-nav">
                    <?php renderDesktopMenu($menu); ?>
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </nav>
    </div>
</header>
<div class="offcanvas offcanvas-start" id="mobileNav">
    <div class="offcanvas-body">
        <?php renderMobileMenu($menu); ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>