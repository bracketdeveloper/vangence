<?php
require_once("includes/head.php");
require_once("model/functions.php");

// Fetch section data
$heroData        = getHeroSection($conn);
$collectionData  = getCollectionSection($conn);
$productData     = getProductSection($conn);
$philosophyData  = getPhilosophySection($conn);
$aboutData       = getAboutSection($conn);
$contactData     = getContactSection($conn);
?>
<div id="wrapper">
    <?php require_once("includes/sidebar.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php require_once("includes/topbar.php"); ?>
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Edit Page Content</h1>
                </div>
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="card mb-4">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="pageTabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="hero-tab" data-toggle="tab"
                                                        href="#hero" role="tab">Hero Section</a></li>
                                <li class="nav-item"><a class="nav-link" id="collections-tab" data-toggle="tab"
                                                        href="#collections" role="tab">Collections</a></li>
                                <li class="nav-item"><a class="nav-link" id="products-tab" data-toggle="tab"
                                                        href="#products" role="tab">Products</a></li>
                                <li class="nav-item"><a class="nav-link" id="philosophy-tab" data-toggle="tab"
                                                        href="#philosophy" role="tab">Philosophy</a></li>
                                <li class="nav-item"><a class="nav-link" id="about-tab" data-toggle="tab" href="#about"
                                                        role="tab">About</a></li>
                                <li class="nav-item"><a class="nav-link" id="contact-tab" data-toggle="tab"
                                                        href="#contact" role="tab">Contact</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="pageTabsContent">

                                <!-- ======================== Hero Section (unchanged) ======================== -->
                                <div class="tab-pane fade show active" id="hero" role="tabpanel">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Pre-title</label>
                                            <input class="form-control" type="text" id="hero-pretitle"
                                                   value="<?php echo isset($heroData['pre_title']) ? htmlspecialchars($heroData['pre_title']) : ''; ?>"
                                                   placeholder="Enter pre-title"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Title</label>
                                            <input class="form-control" type="text" id="hero-title"
                                                   value="<?php echo isset($heroData['title']) ? htmlspecialchars($heroData['title']) : ''; ?>"
                                                   placeholder="Enter main title"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" id="hero-description" rows="2"
                                                      placeholder="Enter description"><?php echo isset($heroData['description']) ? htmlspecialchars($heroData['description']) : ''; ?></textarea>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Button Text</label>
                                            <input class="form-control" type="text" id="hero-button-text"
                                                   value="<?php echo isset($heroData['button_text']) ? htmlspecialchars($heroData['button_text']) : ''; ?>"
                                                   placeholder="Enter button label"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Background Image</label>
                                            <?php if (!empty($heroData['bg_image'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($heroData['bg_image']); ?>"
                                                         alt="Current Background"
                                                         style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:4px;display:block;margin-bottom:5px;">
                                                </div>
                                            <?php endif; ?>
                                            <input class="form-control" type="file" id="hero-bg-image" accept="image/*"/>
                                        </div>
                                    </div>
                                    <input type="button" class="btn btn-primary" onclick="return validateHeroSection()" value="Save Hero Section">
                                </div>

                                <!-- ======================== Collections ======================== -->
                                <div class="tab-pane fade" id="collections" role="tabpanel">

                                    <!-- Men's Collection -->
                                    <h5 class="mb-3 mt-2 text-gray-700">Men's Collection</h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Men's Pre-title</label>
                                            <input class="form-control" type="text" id="coll-mens-pretitle"
                                                   value="<?php echo isset($collectionData['mens_pre_title']) ? htmlspecialchars($collectionData['mens_pre_title']) : ''; ?>"
                                                   placeholder="Enter men's pre-title"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Men's Title</label>
                                            <input class="form-control" type="text" id="coll-mens-title"
                                                   value="<?php echo isset($collectionData['mens_title']) ? htmlspecialchars($collectionData['mens_title']) : ''; ?>"
                                                   placeholder="Enter men's title"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Men's Image</label>
                                            <?php if (!empty($collectionData['mens_image'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($collectionData['mens_image']); ?>"
                                                         alt="Men's Collection Image"
                                                         style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:4px;display:block;margin-bottom:5px;">
                                                </div>
                                            <?php endif; ?>
                                            <input class="form-control" type="file" id="coll-mens-image" accept="image/*"/>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Women's Collection -->
                                    <h5 class="mb-3 text-gray-700">Women's Collection</h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Women's Pre-title</label>
                                            <input class="form-control" type="text" id="coll-womens-pretitle"
                                                   value="<?php echo isset($collectionData['womens_pre_title']) ? htmlspecialchars($collectionData['womens_pre_title']) : ''; ?>"
                                                   placeholder="Enter women's pre-title"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Women's Title</label>
                                            <input class="form-control" type="text" id="coll-womens-title"
                                                   value="<?php echo isset($collectionData['womens_title']) ? htmlspecialchars($collectionData['womens_title']) : ''; ?>"
                                                   placeholder="Enter women's title"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Women's Image</label>
                                            <?php if (!empty($collectionData['womens_image'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($collectionData['womens_image']); ?>"
                                                         alt="Women's Collection Image"
                                                         style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:4px;display:block;margin-bottom:5px;">
                                                </div>
                                            <?php endif; ?>
                                            <input class="form-control" type="file" id="coll-womens-image" accept="image/*"/>
                                        </div>
                                    </div>

                                    <input type="button" class="btn btn-primary" onclick="return validateCollectionSection()" value="Save Collections">
                                </div>

                                <!-- ======================== Products ======================== -->
                                <div class="tab-pane fade" id="products" role="tabpanel">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Pre-title</label>
                                            <input class="form-control" type="text" id="prod-pretitle"
                                                   value="<?php echo isset($productData['pre_title']) ? htmlspecialchars($productData['pre_title']) : ''; ?>"
                                                   placeholder="Enter pre-title"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Title</label>
                                            <input class="form-control" type="text" id="prod-title"
                                                   value="<?php echo isset($productData['title']) ? htmlspecialchars($productData['title']) : ''; ?>"
                                                   placeholder="Enter product section title"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Button Text</label>
                                            <input class="form-control" type="text" id="prod-button-text"
                                                   value="<?php echo isset($productData['button_text']) ? htmlspecialchars($productData['button_text']) : ''; ?>"
                                                   placeholder="Enter button label"/>
                                        </div>
                                    </div>
                                    <input type="button" class="btn btn-primary" onclick="return validateProductSection()" value="Save Products">
                                </div>

                                <!-- ======================== Philosophy ======================== -->
                                <div class="tab-pane fade" id="philosophy" role="tabpanel">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Pre-title</label>
                                            <input class="form-control" type="text" id="phil-pretitle"
                                                   value="<?php echo isset($philosophyData['pre_title']) ? htmlspecialchars($philosophyData['pre_title']) : ''; ?>"
                                                   placeholder="Enter pre-title"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Quote</label>
                                            <input class="form-control" type="text" id="phil-quote"
                                                   value="<?php echo isset($philosophyData['quote']) ? htmlspecialchars($philosophyData['quote']) : ''; ?>"
                                                   placeholder="Enter quote"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" id="phil-description" rows="3"
                                                      placeholder="Enter description"><?php echo isset($philosophyData['description']) ? htmlspecialchars($philosophyData['description']) : ''; ?></textarea>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Button Text</label>
                                            <input class="form-control" type="text" id="phil-button-text"
                                                   value="<?php echo isset($philosophyData['button_text']) ? htmlspecialchars($philosophyData['button_text']) : ''; ?>"
                                                   placeholder="Enter button label"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Image</label>
                                            <?php if (!empty($philosophyData['image'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($philosophyData['image']); ?>"
                                                         alt="Philosophy Image"
                                                         style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:4px;display:block;margin-bottom:5px;">
                                                </div>
                                            <?php endif; ?>
                                            <input class="form-control" type="file" id="phil-image" accept="image/*"/>
                                        </div>
                                    </div>
                                    <input type="button" class="btn btn-primary" onclick="return validatePhilosophySection()" value="Save Philosophy">
                                </div>

                                <!-- ======================== About ======================== -->
                                <div class="tab-pane fade" id="about" role="tabpanel">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Pre-title</label>
                                            <input class="form-control" type="text" id="about-pretitle"
                                                   value="<?php echo isset($aboutData['pre_title']) ? htmlspecialchars($aboutData['pre_title']) : ''; ?>"
                                                   placeholder="Enter pre-title"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Title</label>
                                            <input class="form-control" type="text" id="about-title"
                                                   value="<?php echo isset($aboutData['title']) ? htmlspecialchars($aboutData['title']) : ''; ?>"
                                                   placeholder="Enter title"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" id="about-description" rows="3"
                                                      placeholder="Enter description"><?php echo isset($aboutData['description']) ? htmlspecialchars($aboutData['description']) : ''; ?></textarea>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Button Text</label>
                                            <input class="form-control" type="text" id="about-button-text"
                                                   value="<?php echo isset($aboutData['button_text']) ? htmlspecialchars($aboutData['button_text']) : ''; ?>"
                                                   placeholder="Enter button label"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Image 1</label>
                                            <?php if (!empty($aboutData['image_1'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($aboutData['image_1']); ?>"
                                                         alt="About Image 1"
                                                         style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:4px;display:block;margin-bottom:5px;">
                                                </div>
                                            <?php endif; ?>
                                            <input class="form-control" type="file" id="about-image-1" accept="image/*"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Image 2</label>
                                            <?php if (!empty($aboutData['image_2'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($aboutData['image_2']); ?>"
                                                         alt="About Image 2"
                                                         style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:4px;display:block;margin-bottom:5px;">
                                                </div>
                                            <?php endif; ?>
                                            <input class="form-control" type="file" id="about-image-2" accept="image/*"/>
                                        </div>
                                    </div>
                                    <input type="button" class="btn btn-primary" onclick="return validateAboutSection()" value="Save About">
                                </div>

                                <!-- ======================== Contact ======================== -->
                                <div class="tab-pane fade" id="contact" role="tabpanel">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Pre-title</label>
                                            <input class="form-control" type="text" id="contact-pretitle"
                                                   value="<?php echo isset($contactData['pre_title']) ? htmlspecialchars($contactData['pre_title']) : ''; ?>"
                                                   placeholder="Enter pre-title"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Title</label>
                                            <input class="form-control" type="text" id="contact-title"
                                                   value="<?php echo isset($contactData['title']) ? htmlspecialchars($contactData['title']) : ''; ?>"
                                                   placeholder="Enter title"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" id="contact-description" rows="2"
                                                      placeholder="Enter description"><?php echo isset($contactData['description']) ? htmlspecialchars($contactData['description']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Address</label>
                                            <input class="form-control" type="text" id="contact-address"
                                                   value="<?php echo isset($contactData['address']) ? htmlspecialchars($contactData['address']) : ''; ?>"
                                                   placeholder="Enter address"/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Email</label>
                                            <input class="form-control" type="email" id="contact-email"
                                                   value="<?php echo isset($contactData['email']) ? htmlspecialchars($contactData['email']) : ''; ?>"
                                                   placeholder="Enter email address"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Contact Number</label>
                                            <input class="form-control" type="number" id="contact-phone"
                                                   value="<?php echo isset($contactData['contact']) ? htmlspecialchars($contactData['contact']) : ''; ?>"
                                                   placeholder="Enter contact number"/>
                                        </div>
                                    </div>
                                    <input type="button" class="btn btn-primary" onclick="return validateContactSection()" value="Save Contact">
                                </div>

                            </div><!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
            </div>
        </div>
        <?php require_once("includes/footer.php"); ?>
    </div>
</div>