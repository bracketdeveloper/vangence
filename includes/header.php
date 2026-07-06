<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Vangence - Premium Minimalist Clothing eCommerce Store. High-end Navy and White collections. Ready to wear and stitched fabrics.">
    <meta name="author" content="Vangence">
    <title>Vangence | Premium Minimalist Clothing</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Header & Navigation -->
    <header class="sticky-top py-3">
        <div class="container-fluid px-lg-5 position-relative">
            <nav class="navbar navbar-expand-lg p-0">
                <div class="container-fluid p-0">
                    
                    <!-- Logo (Left) -->
                    <a class="navbar-brand text-uppercase" href="index.php">Vangence</a>
                    
                    <!-- Mobile Hamburger Toggle (Right) -->
                    <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Toggle navigation">
                        <i class="fa-solid fa-bars text-navy" style="font-size: 1.5rem;"></i>
                    </button>
                    
                    <!-- Desktop Nav Links (Center/Right) -->
                    <div class="collapse navbar-collapse justify-content-center" id="desktopNav">
                        <ul class="navbar-nav mb-2 mb-lg-0 gap-lg-2">
                            
                            <!-- Men Menu (Hover Dropdown) -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="shop.php?category=men" id="menDropdown" aria-expanded="false">
                                    Men
                                </a>
                                <ul class="dropdown-menu border-navy" aria-labelledby="menDropdown">
                                    <li><a class="dropdown-item" href="shop.php?category=men&subcategory=Shirts">Shirts</a></li>
                                    <li><a class="dropdown-item" href="shop.php?category=men&subcategory=Pants">Pants</a></li>
                                    <li><a class="dropdown-item" href="shop.php?category=men&subcategory=Eastern+Wear">Eastern Wear</a></li>
                                    <li><a class="dropdown-item" href="shop.php?category=men&subcategory=Western+Wear">Western Wear</a></li>
                                </ul>
                            </li>
                            
                            <!-- Women Menu (Hover Dropdown) -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="shop.php?category=women" id="womenDropdown" aria-expanded="false">
                                    Women
                                </a>
                                <ul class="dropdown-menu border-navy" aria-labelledby="womenDropdown">
                                    <li><a class="dropdown-item" href="shop.php?category=women&subcategory=Shirts">Shirts</a></li>
                                    <li><a class="dropdown-item" href="shop.php?category=women&subcategory=Pants">Pants</a></li>
                                    <li><a class="dropdown-item" href="shop.php?category=women&subcategory=Eastern+Wear">Eastern Wear</a></li>
                                    <li><a class="dropdown-item" href="shop.php?category=women&subcategory=Western+Wear">Western Wear</a></li>
                                </ul>
                            </li>
                            
                            <!-- Stitched Fabrics -->
                            <li class="nav-item">
                                <a class="nav-link" href="shop.php?category=stitched">Stitched Fabrics</a>
                            </li>
                            
                            <!-- Shop -->
                            <li class="nav-item">
                                <a class="nav-link" href="shop.php">Shop</a>
                            </li>
                            
                            <!-- About -->
                            <li class="nav-item">
                                <a class="nav-link" href="about.php">About</a>
                            </li>
                            
                            <!-- Contact -->
                            <li class="nav-item">
                                <a class="nav-link" href="contact.php">Contact</a>
                            </li>
                            
                        </ul>
                    </div>
                    
                    <!-- Search & Cart Icons (Right) -->
                    <div class="d-none d-lg-flex align-items-center gap-3">
                        <div class="header-search-inline" id="desktopHeaderSearch">
                            <button type="button" class="nav-icon text-navy border-0 bg-transparent shadow-none" id="searchToggleBtn" title="Search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <form action="shop.php" method="GET" class="header-search-form">
                                <input type="text" name="search" class="header-search-input" placeholder="Search..." autocomplete="off" aria-label="Search products">
                                <button type="submit" class="header-search-submit" title="Search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <button type="button" class="header-search-close" title="Close search">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                        <a href="cart.php" class="nav-icon text-navy position-relative" title="Shopping Cart">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span class="cart-badge" style="display: none;">0</span>
                        </a>
                    </div>
                    
                    <!-- Mobile Search & Cart Icons (Always visible on mobile next to hamburger) -->
                    <div class="d-flex d-lg-none align-items-center me-2">
                        <div class="header-search-inline" id="mobileHeaderSearch">
                            <button type="button" class="nav-icon text-navy border-0 bg-transparent shadow-none me-1" id="mobileSearchToggleBtn" title="Search">
                                <i class="fa-solid fa-magnifying-glass" style="font-size: 1.3rem;"></i>
                            </button>
                            <form action="shop.php" method="GET" class="header-search-form">
                                <input type="text" name="search" class="header-search-input" placeholder="Search..." autocomplete="off" aria-label="Search products">
                                <button type="submit" class="header-search-submit" title="Search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <button type="button" class="header-search-close" title="Close search">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                        <a href="cart.php" class="nav-icon text-navy position-relative me-2" title="Shopping Cart">
                            <i class="fa-solid fa-bag-shopping" style="font-size: 1.3rem;"></i>
                            <span class="cart-badge" style="display: none;">0</span>
                        </a>
                    </div>
                    
                </div>
            </nav>
        </div>
    </header>

    <!-- Mobile Navigation Drawer (Offcanvas) -->
    <div class="offcanvas offcanvas-start border-0" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel" style="width: 280px;">
        <div class="offcanvas-header d-flex justify-content-between align-items-center py-4 px-3">
            <span class="navbar-brand text-uppercase m-0" id="mobileNavLabel">Vangence</span>
            <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="d-flex flex-column gap-2">
                
                <!-- Men Collapse -->
                <div>
                    <a class="offcanvas-nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#mobileMenCollapse" role="button" aria-expanded="false" aria-controls="mobileMenCollapse">
                        Men <i class="fa-solid fa-chevron-down" style="font-size: 0.8rem;"></i>
                    </a>
                    <div class="collapse ps-3" id="mobileMenCollapse">
                        <a href="shop.php?category=men" class="offcanvas-submenu-link py-2 d-block">All Men's Wear</a>
                        <a href="shop.php?category=men&subcategory=Shirts" class="offcanvas-submenu-link py-2 d-block">Shirts</a>
                        <a href="shop.php?category=men&subcategory=Pants" class="offcanvas-submenu-link py-2 d-block">Pants</a>
                        <a href="shop.php?category=men&subcategory=Eastern+Wear" class="offcanvas-submenu-link py-2 d-block">Eastern Wear</a>
                        <a href="shop.php?category=men&subcategory=Western+Wear" class="offcanvas-submenu-link py-2 d-block">Western Wear</a>
                    </div>
                </div>
                
                <!-- Women Collapse -->
                <div>
                    <a class="offcanvas-nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#mobileWomenCollapse" role="button" aria-expanded="false" aria-controls="mobileWomenCollapse">
                        Women <i class="fa-solid fa-chevron-down" style="font-size: 0.8rem;"></i>
                    </a>
                    <div class="collapse ps-3" id="mobileWomenCollapse">
                        <a href="shop.php?category=women" class="offcanvas-submenu-link py-2 d-block">All Women's Wear</a>
                        <a href="shop.php?category=women&subcategory=Shirts" class="offcanvas-submenu-link py-2 d-block">Shirts</a>
                        <a href="shop.php?category=women&subcategory=Pants" class="offcanvas-submenu-link py-2 d-block">Pants</a>
                        <a href="shop.php?category=women&subcategory=Eastern+Wear" class="offcanvas-submenu-link py-2 d-block">Eastern Wear</a>
                        <a href="shop.php?category=women&subcategory=Western+Wear" class="offcanvas-submenu-link py-2 d-block">Western Wear</a>
                    </div>
                </div>
                
                <a href="shop.php?category=stitched" class="offcanvas-nav-link">Stitched Fabrics</a>
                <a href="shop.php" class="offcanvas-nav-link">Shop All</a>
                <a href="about.php" class="offcanvas-nav-link">About</a>
                <a href="contact.php" class="offcanvas-nav-link">Contact</a>
            </div>
            
            <div class="mt-5 pt-4 border-top border-light">
                <div class="d-flex gap-3 justify-content-center">
                    <a href="#" class="text-navy fs-5"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="text-navy fs-5"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-navy fs-5"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
        </div>
    </div>
