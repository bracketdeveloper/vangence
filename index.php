<?php
// 1. Include database connection and functions
require_once("admin/model/functions.php");
include 'includes/header.php';

$menId = getCategoryIdByName($conn, 'Men');
$womenId = getCategoryIdByName($conn, 'Women');
$allProducts = getAllProducts($conn);
?>
    <section class="hero-section d-flex align-items-center" style="background-image: url('assets/images/hero_bg.png');">
        <div class="hero-overlay"></div>
        <div class="container-fluid px-lg-5 position-relative" style="z-index: 2;">
            <div class="row">
                <div class="col-xl-6 col-lg-8">
                    <span class="text-uppercase tracking-widest text-navy fw-semibold mb-2 d-inline-block"
                          style="font-size: 0.85rem;">New Arrival Edit</span>
                    <h1 class="display-3 text-navy fw-bold text-uppercase mb-4"
                        style="line-height: 1.1; letter-spacing: -1px;">
                        The Navy &<br>White Edit
                    </h1>
                    <p class="fs-5 text-navy opacity-75 mb-5 max-width-500" style="line-height: 1.6; font-weight: 300;">
                        Uncompromising tailoring. Premium Egyptian cotton. A curated line of high-end daily essentials
                        crafted in our signature dual-tone palette.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="shop.php" class="btn btn-navy px-4 py-3">Shop Collection</a>
                        <a href="shop.php?category=stitched" class="btn btn-outline-navy px-4 py-3">Stitched Fabrics</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5 mt-5">
        <div class="container-fluid px-lg-5">
            <div class="row g-4">
                <div class="col-md-6">

                    <div class="category-card">
                        <img src="assets/images/prod_men_shirt.jpg" alt="Men's Collection" class="category-card-img">
                        <a href="shop.php?category=men&id=<?php echo $menId; ?>">
                            <div class="category-card-content">
                                <span class="category-card-subtitle text-uppercase tracking-widest mb-1 d-block"
                                      style="font-size: 0.75rem;">Discover</span>
                                <h2 class="h4 text-uppercase tracking-wider mb-3">Men's Collection</h2>
                                <a href="shop.php?category=men&id=<?php echo $menId; ?>"
                                   class="category-card-link text-uppercase fw-semibold text-decoration-none"
                                   style="font-size: 0.85rem;">
                                    View Shop <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="category-card">
                        <img src="assets/images/prod_women_shirt.jpg" alt="Women's Collection"
                             class="category-card-img">
                        <a href="shop.php?category=women&id=<?php echo $womenId; ?>">
                            <div class="category-card-content">
                            <span class="category-card-subtitle text-uppercase tracking-widest mb-1 d-block"
                                  style="font-size: 0.75rem;">Discover</span>
                                <h2 class="h4 text-uppercase tracking-wider mb-3">Women's Collection</h2>
                                <a href="shop.php?category=women&id=<?php echo $womenId; ?>"
                                   class="category-card-link text-uppercase fw-semibold text-decoration-none"
                                   style="font-size: 0.85rem;">
                                    View Shop <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5">
        <div class="container-fluid px-lg-5">
            <div class="text-center mb-5 pb-3">
                <span class="text-uppercase tracking-widest text-muted" style="font-size: 0.8rem; font-weight: 500;">Essential Pieces</span>
                <h2 class="h3 text-uppercase tracking-wider mt-2 mb-3">Curated Classics</h2>
                <div class="mx-auto" style="width: 50px; height: 1.5px; background-color: var(--color-navy);"></div>
            </div>
            <div class="row g-4 justify-content-center">
                <?php
                // 3. Render real products from database (limiting to first 4)
                if (!empty($allProducts)) {
                    $featuredProducts = array_slice($allProducts, 0, 4);
                    foreach ($featuredProducts as $prod) {
                        render_product_card($prod);
                    }
                } else {
                    echo "<p class='text-center'>No products available at the moment.</p>";
                }
                ?>
            </div>
            <div class="text-center mt-5">
                <a href="shop.php" class="btn btn-outline-navy px-4 py-3">Explore All Products</a>
            </div>
        </div>
    </section>
    <section class="py-5 mt-5 bg-light border-top border-bottom border-light">
        <div class="container-fluid px-lg-5">
            <div class="row align-items-center g-0">
                <div class="col-lg-6 bg-navy text-white p-5 p-lg-5 d-flex flex-column justify-content-center"
                     style="min-height: 480px;">
                    <span class="text-uppercase tracking-widest text-white-50 mb-3" style="font-size: 0.8rem;">Design Philosophy</span>
                    <blockquote class="blockquote mb-4">
                        <p class="fs-4 fw-light mb-0" style="line-height: 1.6; font-style: italic;">
                            "Minimalism is not the lack of something. It is the perfect amount of something."
                        </p>
                    </blockquote>
                    <p class="text-white-50 mb-5" style="line-height: 1.8; font-size: 0.9rem;">
                        Vangence designs products around the principles of restraint and utility. By stripping away
                        heavy logos and distracting colors, we emphasize the tactile quality of long-staple fabrics,
                        clean double-stitches, and precise fittings.
                    </p>
                    <div>
                        <a href="shop.php" class="btn btn-white text-navy bg-white border-white px-4 py-3">Shop the
                            Philosophy</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="height: 480px; overflow: hidden;">
                        <img src="assets/images/prod_stitched_latha.jpg" alt="Vangence Tailoring"
                             class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="about" class="py-5 my-5">
        <div class="container px-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-uppercase tracking-widest text-muted mb-2 d-inline-block"
                          style="font-size: 0.8rem;">Our Heritage</span>
                    <h2 class="h3 text-uppercase tracking-wider mb-4">Crafting the Dual-Tone Identity</h2>
                    <p class="text-muted mb-4" style="line-height: 1.8;">
                        Established in 2026, Vangence is born from a simple design experiment: what happens when you
                        constraint fashion to its purest states? Navy blue represents depth, structure, and formal
                        confidence. White represents light, breathability, and raw clarity. Together, they create a
                        harmonious canvas that never goes out of style.
                    </p>
                    <p class="text-muted mb-4" style="line-height: 1.8;">
                        Every single shirt, chino pant, or stitched Kurta we produce undergoes rigorous shrinkage tests,
                        seam strength audits, and fit trials. We partner with ethical family-owned spinning mills to
                        source 100% organic cotton, raw silk threads, and French flax linens.
                    </p>
                    <a href="about.php" class="btn btn-outline-navy mt-2 mb-4 text-uppercase"
                       style="font-size: 0.8rem; padding: 10px 20px;">Read Our Full Story</a>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div style="aspect-ratio: 1; overflow: hidden; border: 1px solid var(--color-navy-light);">
                                <img src="assets/images/prod_men_pants.jpg" alt="Minimal detail"
                                     class="w-100 h-100 object-fit-cover">
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="aspect-ratio: 1; overflow: hidden; border: 1px solid var(--color-navy-light);">
                                <img src="assets/images/prod_women_eastern.jpg" alt="Minimal detail"
                                     class="w-100 h-100 object-fit-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="contact" class="py-5 bg-navy text-white">
        <div class="container px-lg-5 py-4">
            <div class="row g-5">
                <div class="col-lg-5">
                    <span class="text-uppercase tracking-widest text-white-50 mb-2 d-inline-block"
                          style="font-size: 0.8rem;">Get In Touch</span>
                    <h2 class="h3 text-uppercase tracking-wider mb-4 text-white">Vangence Atelier</h2>
                    <p class="text-white-50 mb-5" style="line-height: 1.8;">
                        For showroom visits, bulk customization queries, or order support, contact our digital concierge
                        team.
                    </p>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 text-white-50"></i>
                            <div>
                                <h4 class="h6 text-uppercase mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                    Atelier Showroom</h4>
                                <p class="text-white-50 mb-0" style="font-size: 0.8rem;">1024 Navy Boulevard, Design
                                    District, NY 10013</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-envelope mt-1 text-white-50"></i>
                            <div>
                                <h4 class="h6 text-uppercase mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                    Email Correspondence</h4>
                                <p class="text-white-50 mb-0" style="font-size: 0.8rem;">concierge@vangence.com</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-phone mt-1 text-white-50"></i>
                            <div>
                                <h4 class="h6 text-uppercase mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                    Customer Hotline</h4>
                                <p class="text-white-50 mb-0" style="font-size: 0.8rem;">+1 (800) 555-NAVY (6289)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <form onsubmit="event.preventDefault(); alert('Message sent successfully!'); this.reset();"
                          class="row g-3">
                        <div class="col-md-6">
                            <label for="contact-name" class="form-label text-uppercase text-white-50"
                                   style="font-size: 0.75rem; font-weight: 500;">Full Name</label>
                            <input type="text" class="form-control bg-white text-navy border-0 shadow-none py-2"
                                   id="contact-name" required style="border-radius: 0;">
                        </div>
                        <div class="col-md-6">
                            <label for="contact-email" class="form-label text-uppercase text-white-50"
                                   style="font-size: 0.75rem; font-weight: 500;">Email Address</label>
                            <input type="email" class="form-control bg-white text-navy border-0 shadow-none py-2"
                                   id="contact-email" required style="border-radius: 0;">
                        </div>
                        <div class="col-12">
                            <label for="contact-subject" class="form-label text-uppercase text-white-50"
                                   style="font-size: 0.75rem; font-weight: 500;">Subject</label>
                            <input type="text" class="form-control bg-white text-navy border-0 shadow-none py-2"
                                   id="contact-subject" required style="border-radius: 0;">
                        </div>
                        <div class="col-12">
                            <label for="contact-message" class="form-label text-uppercase text-white-50"
                                   style="font-size: 0.75rem; font-weight: 500;">Message</label>
                            <textarea class="form-control bg-white text-navy border-0 shadow-none py-2"
                                      id="contact-message" rows="4" required style="border-radius: 0;"></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-white text-navy bg-white border-white px-4 py-3 w-100">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>