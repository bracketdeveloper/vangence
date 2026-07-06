<?php
// Mock Database of Products
$products = [
    [
        'id' => 1,
        'name' => 'Classic Oxford Navy Shirt',
        'category' => 'men',
        'subcategory' => 'Shirts',
        'price' => 65.00,
        'image' => 'assets/images/prod_men_shirt.jpg',
        'sizes' => ['S', 'M', 'L', 'XL'],
        'colors' => ['Navy', 'White'],
        'description' => 'A timeless wardrobe essential, this classic Oxford shirt is crafted from premium breathable cotton. It features a sharp button-down collar, structured buttoned cuffs, and a tailored silhouette that transitions effortlessly from day to night.',
        'details' => '100% Premium Cotton. Double-stitched seams. Minimalist navy branding on cuff. Machine wash cold, tumble dry low.'
    ],
    [
        'id' => 2,
        'name' => 'Tailored Chino Pants',
        'category' => 'men',
        'subcategory' => 'Pants',
        'price' => 80.00,
        'image' => 'assets/images/prod_men_pants.jpg',
        'sizes' => ['30', '32', '34', '36'],
        'colors' => ['Navy', 'White'],
        'description' => 'Engineered for comfort and modern style, these tailored chinos are cut from premium stretch-cotton twill. Featuring a mid-rise fit, clean-front design, and discrete pocketing, they offer a sleek look for any occasion.',
        'details' => '98% Cotton, 2% Elastane. Fitted waistband. Zip fly with button closure. Dry clean recommended or delicate machine wash.'
    ],
    [
        'id' => 3,
        'name' => 'Embellished Silk Kurta',
        'category' => 'women',
        'subcategory' => 'Eastern Wear',
        'price' => 120.00,
        'image' => 'assets/images/prod_women_eastern.jpg',
        'sizes' => ['XS', 'S', 'M', 'L'],
        'colors' => ['Navy', 'White'],
        'description' => 'Celebrate heritage with this luxurious silk kurta. Detailed with delicate tone-on-tone embroidery around the neckline and keyhole detail, this piece offers an elegant drape and a subtle, high-end sheen.',
        'details' => '100% Raw Silk. Intricate hand-embroidery. Straight-cut hemline. Dry clean only.'
    ],
    [
        'id' => 4,
        'name' => 'Minimalist Trench Coat',
        'category' => 'women',
        'subcategory' => 'Western Wear',
        'price' => 150.00,
        'image' => 'assets/images/prod_women_western.jpg',
        'sizes' => ['S', 'M', 'L'],
        'colors' => ['Navy', 'White'],
        'description' => 'A modern reimagining of the classic outerwear. This double-breasted trench coat features clean structural lines, wide lapels, and a removable waist tie belt to create a defined silhouette or an effortless drape.',
        'details' => 'Premium cotton-poly blend outer. Full interior lining. Deep welt pockets. Light water-resistant coating.'
    ],
    [
        'id' => 5,
        'name' => 'Signature Latha Suit',
        'category' => 'stitched',
        'subcategory' => 'Stitched Fabrics',
        'price' => 110.00,
        'image' => 'assets/images/prod_stitched_latha.jpg',
        'sizes' => ['S', 'M', 'L', 'XL'],
        'colors' => ['White', 'Navy'],
        'description' => 'Crafted from premium long-staple cotton fibers, this traditional stitched Latha suit offers unmatched fabric crispness and comfort. Features a classic band collar, hidden button placket, and matching straight trousers.',
        'details' => '100% Super Fine Egyptian Cotton Latha. Traditional styling. Medium fabric weight. Hand wash separately.'
    ],
    [
        'id' => 6,
        'name' => 'Structured Navy Blazer',
        'category' => 'men',
        'subcategory' => 'Western Wear',
        'price' => 180.00,
        'image' => 'assets/images/prod_men_blazer.jpg',
        'sizes' => ['M', 'L', 'XL'],
        'colors' => ['Navy'],
        'description' => 'Exude confidence in this structured blazer. Crafted with a soft shoulder padding, notched lapels, and a two-button front, it provides a sharp silhouette without sacrificing comfort or mobility.',
        'details' => 'Wool-blend outer. Navy satin inner lining. Internal utility pockets. Dry clean only.'
    ],
    [
        'id' => 7,
        'name' => 'Linen Resort Midi Dress',
        'category' => 'women',
        'subcategory' => 'Western Wear',
        'price' => 95.00,
        'image' => 'assets/images/prod_women_dress.jpg',
        'sizes' => ['XS', 'S', 'M', 'L'],
        'colors' => ['White', 'Navy'],
        'description' => 'Perfect for warm afternoons, this midi dress is woven from pure linen. Designed with a clean square neckline, thin adjustable shoulder straps, and subtle side slits for movement and ease.',
        'details' => '100% French Linen. Breathable design. Concealed side zip. Cold gentle wash, dry in shade.'
    ],
    [
        'id' => 8,
        'name' => 'Fine Cotton Jacquard Kurta',
        'category' => 'women',
        'subcategory' => 'Eastern Wear',
        'price' => 135.00,
        'image' => 'assets/images/prod_women_jacquard.jpg',
        'sizes' => ['S', 'M', 'L'],
        'colors' => ['White'],
        'description' => 'A beautiful textured cotton jacquard kurta featuring intricate self-weave floral motifs. The relaxed straight silhouette is accented by lace inserts on the cuffs and hem for a delicate touch.',
        'details' => '100% Premium Cotton Jacquard. Custom lace trims. Standard crew neck. Dry clean or hand wash.'
    ]
];

/**
 * Renders a single product card using Bootstrap 5 grid structures.
 * 
 * @param array $product The product array containing id, name, price, image, etc.
 * @param string $colClass Optional bootstrap column layout. Defaults to col-6 col-md-4 col-lg-3
 */
function render_product_card($product, $colClass = 'col-6 col-md-4 col-lg-3') {
    $categoryLabel = ucfirst($product['category'] === 'stitched' ? 'Stitched' : $product['category']);
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
                    <?php echo $categoryLabel; ?> &bull; <?php echo htmlspecialchars($product['subcategory']); ?>
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
