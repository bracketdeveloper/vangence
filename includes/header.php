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
function renderDesktopMenu($items, $parentCategory = null)
{
    foreach ($items as $item) {
        $hasChildren = !empty($item['children']);
        $isSubmenu = ($parentCategory !== null);

        $currentParent = ($parentCategory === null) ? $item['category'] : $parentCategory;
        $url = 'shop.php?category=' . strtolower(urlencode($currentParent));
        if ($parentCategory !== null) {
            $url .= '&subcategory=' . strtolower(urlencode($item['category']));
        }

        echo '<li class="nav-item ' . ($hasChildren ? 'dropdown' : '') . ' ' . ($isSubmenu ? 'dropdown-submenu' : '') . '">';
        $arrow = $hasChildren ? '<span class="arrow">' . ($isSubmenu ? ' ▶' : ' ▼') . '</span>' : '';
        echo '<a class="nav-link ' . ($isSubmenu ? 'dropdown-item' : '') . '" href="' . $url . '">' . $item['category'] . $arrow . '</a>';

        if ($hasChildren) {
            echo '<ul class="dropdown-menu">';
            renderDesktopMenu($item['children'], $currentParent);
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

        if ($hasChildren) {
            echo '<div class="mb-2">';
            // Parent link
            echo '<a data-bs-toggle="collapse" href="#mobCat' . $item['category_id'] . '" class="d-block text-decoration-none"> ' . $item['category'] . ' ▾</a>';
            echo '<div class="collapse ps-3" id="mobCat' . $item['category_id'] . '">';

            // ADDED: The "All [Category]" link appears inside the collapsed menu
            echo '<a href="shop.php?category=' . strtolower(urlencode($item['category'])) . '" class="d-block text-decoration-none py-1"> All ' . $item['category'] . ' </a>';

            // Continue recursion
            renderMobileMenu($item['children']);

            echo '</div></div>';
        } else {
            // Leaf category link
            echo '<a href="shop.php?category=' . strtolower(urlencode($item['category'])) . '" class="d-block text-decoration-none py-1"> ' . $item['category'] . ' </a>';
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
    <style>
        .arrow { font-size: 0.7em; margin-left: 5px; opacity: 0.7; }
        .dropdown-menu .dropdown-menu { display: none !important; }
        .dropdown:hover > .dropdown-menu, .dropdown-submenu:hover > .dropdown-menu { display: block !important; }
        .dropdown-submenu { position: relative; }
        .dropdown-submenu > .dropdown-menu { top: 0 !important; left: 100% !important; margin-top: -5px; }

        /* Desktop styles */
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