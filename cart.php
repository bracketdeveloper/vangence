<?php
include 'includes/header.php'; 
?>

<!-- Page Title Banner -->
<div class="bg-light py-5 border-bottom border-light">
    <div class="container px-lg-5 text-center">
        <span class="text-uppercase tracking-widest text-muted" style="font-size: 0.8rem; font-weight: 500;">Your Cart</span>
        <h1 class="h2 text-uppercase tracking-wider text-navy mt-2 mb-0 fw-bold">Shopping Cart</h1>
        <div class="mx-auto mt-3" style="width: 50px; height: 1.5px; background-color: var(--color-navy);"></div>
    </div>
</div>

<!-- Cart Container -->
<div class="container-fluid px-lg-5 py-5 my-3">
    <div id="cart-page-container">
        <!-- Rendered Dynamically by assets/js/main.js -->
        <div class="text-center py-5">
            <div class="spinner-border text-navy" role="status">
                <span class="visually-hidden">Loading your cart...</span>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
