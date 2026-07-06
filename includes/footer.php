    <!-- Footer -->
    <footer class="py-5 mt-5">
        <div class="container-fluid px-lg-5">
            <div class="row g-4">
                
                <!-- Brand Profile -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-uppercase tracking-widest text-white fw-bold mb-4">Vangence</h5>
                    <p class="text-white-50 mb-4" style="line-height: 1.8; font-size: 0.8rem;">
                        Crafting minimalist daily essentials with an unwavering commitment to premium tailoring, sustainable cotton fabrics, and timeless design.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="social-icon" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://instagram.com" class="social-icon" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="social-icon" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-3 col-md-6 ps-lg-5">
                    <h5 class="text-white">Quick Links</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2" style="font-size: 0.8rem;">
                        <li><a href="shop.php" class="text-decoration-none">Shop Collection</a></li>
                        <li><a href="about.php" class="text-decoration-none">Our Story</a></li>
                        <li><a href="contact.php" class="text-decoration-none">Contact Us</a></li>
                        <li><a href="shipping-returns.php" class="text-decoration-none">Shipping & Returns</a></li>
                        <li><a href="privacy-policy.php" class="text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <!-- Categories -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white">Categories</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2" style="font-size: 0.8rem;">
                        <li><a href="shop.php?category=men" class="text-decoration-none">Men's Collection</a></li>
                        <li><a href="shop.php?category=women" class="text-decoration-none">Women's Collection</a></li>
                        <li><a href="shop.php?category=stitched" class="text-decoration-none">Stitched Fabrics</a></li>
                        <li><a href="shop.php?category=men&subcategory=Shirts" class="text-decoration-none">Formal & Casual Shirts</a></li>
                        <li><a href="shop.php?category=women&subcategory=Eastern+Wear" class="text-decoration-none">Eastern Traditional Wear</a></li>
                    </ul>
                </div>
                
                <!-- Newsletter Signup -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white">Newsletter</h5>
                    <p class="text-white-50 mb-3" style="font-size: 0.8rem;">
                        Subscribe to get notified about product launches and editorial edits.
                    </p>
                    <form onsubmit="event.preventDefault(); alert('Subscribed successfully!'); this.reset();" class="d-flex flex-column gap-2">
                        <div class="input-group">
                            <input type="email" class="form-control bg-transparent text-white border-white-50 py-2 shadow-none" 
                                   placeholder="Enter your email" aria-label="Email address" required 
                                   style="border-radius: 0; font-size: 0.8rem;">
                            <button class="btn btn-white text-navy bg-white border-white px-3" type="submit" style="border-radius: 0;">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom pt-4 mt-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <p class="mb-0">
                    &copy; <?php echo date('Y'); ?> Vangence. All rights reserved. Built with precision and minimalism.
                </p>
                <div class="d-flex gap-4">
                    <a href="#" class="text-white-50 text-decoration-none" style="font-size: 0.75rem;">Terms of Service</a>
                    <a href="#" class="text-white-50 text-decoration-none" style="font-size: 0.75rem;">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS (Popper included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Main JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
