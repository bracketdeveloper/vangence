<?php
// Mock Database of Products updated with new categories
$products = [
        [
                'id' => 1,
                'name' => 'Classic Navy Shirt',
                'category' => 'Men',
                'subcategory' => 'Shirts',
                'price' => 65.00,
                'image' => 'assets/images/prod_men_shirt.jpg',
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Navy', 'White'],
                'description' => 'A timeless wardrobe essential, this classic Oxford shirt is crafted from premium breathable cotton.',
                'details' => '100% Premium Cotton. Double-stitched seams. Machine wash cold.'
        ],
        [
                'id' => 2,
                'name' => 'Tailored Chinos',
                'category' => 'Men',
                'subcategory' => 'Pants',
                'price' => 80.00,
                'image' => 'assets/images/prod_men_pants.jpg',
                'sizes' => ['30', '32', '34', '36'],
                'colors' => ['Navy', 'White'],
                'description' => 'Engineered for comfort and modern style, these tailored chinos are cut from premium stretch-cotton.',
                'details' => '98% Cotton, 2% Elastane. Fitted waistband. Dry clean recommended.'
        ],
        [
                'id' => 3,
                'name' => 'Silk Formal Dress',
                'category' => 'Women',
                'subcategory' => 'Dress',
                'price' => 120.00,
                'image' => 'assets/images/prod_women_eastern.jpg',
                'sizes' => ['XS', 'S', 'M', 'L'],
                'colors' => ['Navy', 'White'],
                'description' => 'Celebrate heritage with this luxurious silk dress, offering an elegant drape and high-end sheen.',
                'details' => '100% Raw Silk. Intricate hand-embroidery. Dry clean only.'
        ],
        [
                'id' => 4,
                'name' => 'Signature Women Suit',
                'category' => 'Women',
                'subcategory' => 'Suits',
                'price' => 150.00,
                'image' => 'assets/images/prod_women_western.jpg',
                'sizes' => ['S', 'M', 'L'],
                'colors' => ['Navy', 'White'],
                'description' => 'A modern reimagining of the classic outerwear with clean structural lines and a defined silhouette.',
                'details' => 'Premium cotton-poly blend. Full interior lining. Water-resistant.'
        ],
        [
                'id' => 5,
                'name' => 'Golden Neckless',
                'category' => 'Accessories',
                'subcategory' => 'neckless',
                'price' => 110.00,
                'image' => 'assets/images/prod_stitched_latha.jpg',
                'sizes' => ['One Size'],
                'colors' => ['Gold'],
                'description' => 'A beautifully crafted piece to elevate your look, featuring intricate metalwork.',
                'details' => 'Gold-plated. Lightweight. Hand wash separately.'
        ],
        [
                'id' => 6,
                'name' => 'Luxury Flora Perfume',
                'category' => 'Accessories',
                'subcategory' => 'Flora',
                'price' => 180.00,
                'image' => 'assets/images/prod_men_blazer.jpg',
                'sizes' => ['50ml', '100ml'],
                'colors' => ['Clear'],
                'description' => 'Exude confidence with this signature scent, featuring floral notes that last all day.',
                'details' => 'Premium essence. Long-lasting. Dry clean only.'
        ],
        [
                'id' => 7,
                'name' => 'Gucci Inspired Scent',
                'category' => 'Accessories',
                'subcategory' => 'Gucci',
                'price' => 95.00,
                'image' => 'assets/images/prod_women_dress.jpg',
                'sizes' => ['50ml', '100ml'],
                'colors' => ['Amber'],
                'description' => 'Perfect for warm afternoons, this scent provides a crisp, sophisticated aroma.',
                'details' => '100% French Essence. Breathable design. Cold gentle wash.'
        ],
        [
                'id' => 8,
                'name' => 'Flora 1 Special Edition',
                'category' => 'Accessories',
                'subcategory' => 'flora 1',
                'price' => 135.00,
                'image' => 'assets/images/prod_women_jacquard.jpg',
                'sizes' => ['100ml'],
                'colors' => ['White'],
                'description' => 'A beautiful textured perfume featuring floral motifs. A delicate touch for daily wear.',
                'details' => '100% Premium Essence. Standard spray bottle. Dry clean or hand wash.'
        ]
];

/**
 * Renders a single product card using Bootstrap 5 grid structures.
 */
function render_product_card($product, $colClass = 'col-6 col-md-4 col-lg-3') {
    // Note: Updated to display the new category structure
    ?>
    <div class="<?php echo $colClass; ?> mb-4 d-flex">
        <div class="product-card w-100 d-flex flex-column">
            <div class="product-card-img-wrapper position-relative overflow-hidden bg-light">
                <a href="product.php?id=<?php echo $product['id']; ?>" class="d-block w-100 h-100">
                    <img src="<?php echo $product['image']; ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="product-card-img img-fluid w-100 h-100 object-fit-cover"
                         loading="lazy">
                </a>
            </div>
            <div class="product-card-info p-3 d-flex flex-column flex-grow-1">
                <span class="product-card-category mb-1 text-uppercase tracking-widest text-muted" style="font-size: 0.7rem; font-weight: 500;">
                    <?php echo htmlspecialchars($product['category']); ?> &bull; <?php echo htmlspecialchars($product['subcategory']); ?>
                </span>
                <h3 class="product-card-title h6 mb-2 flex-grow-1">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-navy">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </a>
                </h3>
                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top border-light">
                    <span class="product-card-price text-navy fw-semibold">
                        $<?php echo number_format($product['price'], 2); ?>
                    </span>
                    <button class="btn btn-navy btn-sm btn-add-to-cart text-uppercase"
                            data-id="<?php echo $product['id']; ?>"
                            data-name="<?php echo htmlspecialchars($product['name']); ?>"
                            data-price="<?php echo $product['price']; ?>"
                            data-image="<?php echo $product['image']; ?>"
                            data-size="<?php echo $product['sizes'][0]; ?>"
                            data-color="<?php echo $product['colors'][0]; ?>"
                            style="font-size: 0.75rem; letter-spacing: 0.5px; padding: 6px 12px;">
                        Add To Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>