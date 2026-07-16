<?php
// 1. Include database connection and functions
require_once("admin/model/functions.php");
include 'includes/header.php';

$menId          = getCategoryIdByName($conn, 'Men');
$womenId        = getCategoryIdByName($conn, 'Women');
$allProducts    = getAllProducts($conn);
$heroData       = getHeroSection($conn);
$collectionData = getCollectionSection($conn);
$productData    = getProductSection($conn);
$philosophyData = getPhilosophySection($conn);
$aboutData      = getAboutSection($conn);
$contactData    = getContactSection($conn);
?>

    <!-- ======================== HERO ======================== -->
    <section class="hero-section d-flex align-items-center"
             style="background-image: url('<?php echo "admin/" . htmlspecialchars($heroData['bg_image']); ?>');">
        <div class="hero-overlay"></div>
        <div class="container-fluid px-lg-5 position-relative" style="z-index: 2;">
            <div class="row">
                <div class="col-xl-6 col-lg-8">
                    <span class="text-uppercase tracking-widest text-navy fw-semibold mb-2 d-inline-block"
                          style="font-size: 0.85rem;">
                        <?php echo htmlspecialchars($heroData['pre_title']); ?>
                    </span>
                    <h1 class="display-3 text-navy fw-bold text-uppercase mb-4"
                        style="line-height: 1.1; letter-spacing: -1px;">
                        <?php echo nl2br(htmlspecialchars($heroData['title'])); ?>
                    </h1>
                    <p class="fs-5 text-navy opacity-75 mb-5 max-width-500"
                       style="line-height: 1.6; font-weight: 300;">
                        <?php echo htmlspecialchars($heroData['description']); ?>
                    </p>
                    <div class="d-flex gap-3">
                        <a href="shop.php" class="btn btn-navy px-4 py-3">
                            <?php echo htmlspecialchars($heroData['button_text']); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================== COLLECTIONS ======================== -->
    <section class="py-5 mt-5">
        <div class="container-fluid px-lg-5">
            <div class="row g-4">

                <!-- Men's Collection -->
                <div class="col-md-6">
                    <div class="category-card">
                        <img src="admin/<?php echo htmlspecialchars($collectionData['mens_image']); ?>"
                             alt="Men's Collection" class="category-card-img">
                        <a href="shop.php?category=men&id=<?php echo $menId; ?>">
                            <div class="category-card-content">
                                <span class="category-card-subtitle text-uppercase tracking-widest mb-1 d-block"
                                      style="font-size: 0.75rem;">
                                    <?php echo htmlspecialchars($collectionData['mens_pre_title']); ?>
                                </span>
                                <h2 class="h4 text-uppercase tracking-wider mb-3">
                                    <?php echo htmlspecialchars($collectionData['mens_title']); ?>
                                </h2>
                                <a href="shop.php?category=men&id=<?php echo $menId; ?>"
                                   class="category-card-link text-uppercase fw-semibold text-decoration-none"
                                   style="font-size: 0.85rem;">
                                    View Shop <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Women's Collection -->
                <div class="col-md-6">
                    <div class="category-card">
                        <img src="admin/<?php echo htmlspecialchars($collectionData['womens_image']); ?>"
                             alt="Women's Collection" class="category-card-img">
                        <a href="shop.php?category=women&id=<?php echo $womenId; ?>">
                            <div class="category-card-content">
                                <span class="category-card-subtitle text-uppercase tracking-widest mb-1 d-block"
                                      style="font-size: 0.75rem;">
                                    <?php echo htmlspecialchars($collectionData['womens_pre_title']); ?>
                                </span>
                                <h2 class="h4 text-uppercase tracking-wider mb-3">
                                    <?php echo htmlspecialchars($collectionData['womens_title']); ?>
                                </h2>
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

    <!-- ======================== PRODUCTS ======================== -->
    <section class="py-5">
        <div class="container-fluid px-lg-5">
            <div class="text-center mb-5 pb-3">
                <span class="text-uppercase tracking-widest text-muted"
                      style="font-size: 0.8rem; font-weight: 500;">
                    <?php echo htmlspecialchars($productData['pre_title']); ?>
                </span>
                <h2 class="h3 text-uppercase tracking-wider mt-2 mb-3">
                    <?php echo htmlspecialchars($productData['title']); ?>
                </h2>
                <div class="mx-auto"
                     style="width: 50px; height: 1.5px; background-color: var(--color-navy);"></div>
            </div>
            <div class="row g-4 justify-content-center">
                <?php
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
                <a href="shop.php" class="btn btn-outline-navy px-4 py-3">
                    <?php echo htmlspecialchars($productData['button_text']); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- ======================== PHILOSOPHY ======================== -->
    <section class="py-5 mt-5 bg-light border-top border-bottom border-light">
        <div class="container-fluid px-lg-5">
            <div class="row align-items-center g-0">
                <div class="col-lg-6 bg-navy text-white p-5 p-lg-5 d-flex flex-column justify-content-center"
                     style="min-height: 480px;">
                    <span class="text-uppercase tracking-widest text-white-50 mb-3"
                          style="font-size: 0.8rem;">
                        <?php echo htmlspecialchars($philosophyData['pre_title']); ?>
                    </span>
                    <blockquote class="blockquote mb-4">
                        <p class="fs-4 fw-light mb-0" style="line-height: 1.6; font-style: italic;">
                            "<?php echo htmlspecialchars($philosophyData['quote']); ?>"
                        </p>
                    </blockquote>
                    <p class="text-white-50 mb-5" style="line-height: 1.8; font-size: 0.9rem;">
                        <?php echo nl2br(htmlspecialchars($philosophyData['description'])); ?>
                    </p>
                    <div>
                        <a href="shop.php" class="btn btn-white text-navy bg-white border-white px-4 py-3">
                            <?php echo htmlspecialchars($philosophyData['button_text']); ?>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="height: 480px; overflow: hidden;">
                        <img src="admin/<?php echo htmlspecialchars($philosophyData['image']); ?>"
                             alt="Philosophy" class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================== ABOUT ======================== -->
    <section id="about" class="py-5 my-5">
        <div class="container px-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-uppercase tracking-widest text-muted mb-2 d-inline-block"
                          style="font-size: 0.8rem;">
                        <?php echo htmlspecialchars($aboutData['pre_title']); ?>
                    </span>
                    <h2 class="h3 text-uppercase tracking-wider mb-4">
                        <?php echo htmlspecialchars($aboutData['title']); ?>
                    </h2>
                    <p class="text-muted mb-4" style="line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($aboutData['description'])); ?>
                    </p>
                    <a href="about.php"
                       class="btn btn-outline-navy mt-2 mb-4 text-uppercase"
                       style="font-size: 0.8rem; padding: 10px 20px;">
                        <?php echo htmlspecialchars($aboutData['button_text']); ?>
                    </a>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div style="aspect-ratio: 1; overflow: hidden; border: 1px solid var(--color-navy-light);">
                                <img src="admin/<?php echo htmlspecialchars($aboutData['image_1']); ?>"
                                     alt="About Image 1" class="w-100 h-100 object-fit-cover">
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="aspect-ratio: 1; overflow: hidden; border: 1px solid var(--color-navy-light);">
                                <img src="admin/<?php echo htmlspecialchars($aboutData['image_2']); ?>"
                                     alt="About Image 2" class="w-100 h-100 object-fit-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================== CONTACT ======================== -->
    <section id="contact" class="py-5 bg-navy text-white">
        <div class="container px-lg-5 py-4">
            <div class="row g-5">
                <div class="col-lg-5">
                    <span class="text-uppercase tracking-widest text-white-50 mb-2 d-inline-block"
                          style="font-size: 0.8rem;">
                        <?php echo htmlspecialchars($contactData['pre_title']); ?>
                    </span>
                    <h2 class="h3 text-uppercase tracking-wider mb-4 text-white">
                        <?php echo htmlspecialchars($contactData['title']); ?>
                    </h2>
                    <p class="text-white-50 mb-5" style="line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($contactData['description'])); ?>
                    </p>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 text-white-50"></i>
                            <div>
                                <h4 class="h6 text-uppercase mb-1"
                                    style="font-size: 0.85rem; letter-spacing: 0.5px;">Address</h4>
                                <p class="text-white-50 mb-0" style="font-size: 0.8rem;">
                                    <?php echo htmlspecialchars($contactData['address']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-envelope mt-1 text-white-50"></i>
                            <div>
                                <h4 class="h6 text-uppercase mb-1"
                                    style="font-size: 0.85rem; letter-spacing: 0.5px;">Email</h4>
                                <p class="text-white-50 mb-0" style="font-size: 0.8rem;">
                                    <?php echo htmlspecialchars($contactData['email']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-phone mt-1 text-white-50"></i>
                            <div>
                                <h4 class="h6 text-uppercase mb-1"
                                    style="font-size: 0.85rem; letter-spacing: 0.5px;">Contact</h4>
                                <p class="text-white-50 mb-0" style="font-size: 0.8rem;">
                                    <?php echo htmlspecialchars($contactData['contact']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <form onsubmit="event.preventDefault(); alert('Message sent successfully!'); this.reset();"
                          class="row g-3">
                        <div class="col-md-6">
                            <label for="contact-name"
                                   class="form-label text-uppercase text-white-50"
                                   style="font-size: 0.75rem; font-weight: 500;">Full Name</label>
                            <input type="text"
                                   class="form-control bg-white text-navy border-0 shadow-none py-2"
                                   id="contact-name" required style="border-radius: 0;">
                        </div>
                        <div class="col-md-6">
                            <label for="contact-email"
                                   class="form-label text-uppercase text-white-50"
                                   style="font-size: 0.75rem; font-weight: 500;">Email Address</label>
                            <input type="email"
                                   class="form-control bg-white text-navy border-0 shadow-none py-2"
                                   id="contact-email" required style="border-radius: 0;">
                        </div>
                        <div class="col-12">
                            <label for="contact-subject"
                                   class="form-label text-uppercase text-white-50"
                                   style="font-size: 0.75rem; font-weight: 500;">Subject</label>
                            <input type="text"
                                   class="form-control bg-white text-navy border-0 shadow-none py-2"
                                   id="contact-subject" required style="border-radius: 0;">
                        </div>
                        <div class="col-12">
                            <label for="contact-message"
                                   class="form-label text-uppercase text-white-50"
                                   style="font-size: 0.75rem; font-weight: 500;">Message</label>
                            <textarea class="form-control bg-white text-navy border-0 shadow-none py-2"
                                      id="contact-message" rows="4" required
                                      style="border-radius: 0;"></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit"
                                    class="btn btn-white text-navy bg-white border-white px-4 py-3 w-100">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>