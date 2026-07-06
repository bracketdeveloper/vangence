<?php 
include 'includes/products_db.php'; 
include 'includes/header.php'; 
?>

<!-- Page Title Banner -->
<div class="bg-light py-5 border-bottom border-light">
    <div class="container px-lg-5 text-center">
        <span class="text-uppercase tracking-widest text-muted" style="font-size: 0.8rem; font-weight: 500;">About Us</span>
        <h1 class="h2 text-uppercase tracking-wider text-navy mt-2 mb-0 fw-bold">Our Story & Philosophy</h1>
        <div class="mx-auto mt-3" style="width: 50px; height: 1.5px; background-color: var(--color-navy);"></div>
    </div>
</div>

<!-- About Section -->
<section id="about" class="py-5 my-5">
    <div class="container px-lg-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="text-uppercase tracking-widest text-muted mb-2 d-inline-block" style="font-size: 0.8rem;">Our Heritage</span>
                <h2 class="h3 text-uppercase tracking-wider mb-4">Crafting the Dual-Tone Identity</h2>
                <p class="text-muted mb-4" style="line-height: 1.8;">
                    Established in 2026, Vangence is born from a simple design experiment: what happens when you constraint fashion to its purest states? Navy blue represents depth, structure, and formal confidence. White represents light, breathability, and raw clarity. Together, they create a harmonious canvas that never goes out of style.
                </p>
                <p class="text-muted mb-4" style="line-height: 1.8;">
                    Every single shirt, chino pant, or stitched Kurta we produce undergoes rigorous shrinkage tests, seam strength audits, and fit trials. We partner with ethical family-owned spinning mills to source 100% organic cotton, raw silk threads, and French flax linens.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div style="aspect-ratio: 1; overflow: hidden; border: 1px solid var(--color-navy-light);">
                            <img src="assets/images/prod_men_pants.jpg" alt="Minimal detail" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="aspect-ratio: 1; overflow: hidden; border: 1px solid var(--color-navy-light);">
                            <img src="assets/images/prod_women_eastern.jpg" alt="Minimal detail" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Brand Value Section (Premium Aesthetic Add-on) -->
<section class="py-5 bg-navy text-white mt-5">
    <div class="container px-lg-5 py-4">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-3">
                    <div class="mb-3"><i class="fa-solid fa-gem fs-2"></i></div>
                    <h3 class="h6 text-uppercase tracking-wider mb-3">Premium Tailoring</h3>
                    <p class="text-white-50 mb-0" style="font-size: 0.8rem; line-height: 1.6;">Double-stitched seams and structured finishes built to endure.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="mb-3"><i class="fa-solid fa-leaf fs-2"></i></div>
                    <h3 class="h6 text-uppercase tracking-wider mb-3">Sustainable Organic Fabrics</h3>
                    <p class="text-white-50 mb-0" style="font-size: 0.8rem; line-height: 1.6;">100% Egyptian long-staple cotton and premium organic fibers.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="mb-3"><i class="fa-solid fa-sliders fs-2"></i></div>
                    <h3 class="h6 text-uppercase tracking-wider mb-3">Minimalist Restricted Palette</h3>
                    <p class="text-white-50 mb-0" style="font-size: 0.8rem; line-height: 1.6;">A signature dual-tone concept designed for effortless pairing.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
