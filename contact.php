<?php 
include 'includes/header.php';
?>

<!-- Page Title Banner -->
<div class="bg-light py-5 border-bottom border-light">
    <div class="container px-lg-5 text-center">
        <span class="text-uppercase tracking-widest text-muted" style="font-size: 0.8rem; font-weight: 500;">Contact Us</span>
        <h1 class="h2 text-uppercase tracking-wider text-navy mt-2 mb-0 fw-bold">Get In Touch</h1>
        <div class="mx-auto mt-3" style="width: 50px; height: 1.5px; background-color: var(--color-navy);"></div>
    </div>
</div>

<!-- Contact Section -->
<section id="contact" class="py-5 my-5">
    <div class="container px-lg-5">
        <div class="row g-5">
            
            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="bg-navy text-white p-4 p-lg-5 h-100 d-flex flex-column justify-content-between" style="border: 1px solid var(--color-navy);">
                    <div>
                        <span class="text-uppercase tracking-widest text-white-50 mb-2 d-inline-block" style="font-size: 0.8rem;">Connect</span>
                        <h2 class="h3 text-uppercase tracking-wider mb-4 text-white">Vangence Atelier</h2>
                        <p class="text-white-50 mb-5" style="line-height: 1.8; font-size: 0.9rem;">
                            For showroom visits, bulk customization queries, or order support, contact our digital concierge team.
                        </p>
                    </div>
                    
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 text-white-50" style="font-size: 1.1rem;"></i>
                            <div>
                                <h4 class="h6 text-uppercase mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">Atelier Showroom</h4>
                                <p class="text-white-50 mb-0" style="font-size: 0.8rem; line-height: 1.6;">1024 Navy Boulevard, Design District, NY 10013</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-envelope mt-1 text-white-50" style="font-size: 1.1rem;"></i>
                            <div>
                                <h4 class="h6 text-uppercase mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">Email Correspondence</h4>
                                <p class="text-white-50 mb-0" style="font-size: 0.8rem;">concierge@vangence.com</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-phone mt-1 text-white-50" style="font-size: 1.1rem;"></i>
                            <div>
                                <h4 class="h6 text-uppercase mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">Customer Hotline</h4>
                                <p class="text-white-50 mb-0" style="font-size: 0.8rem;">+1 (800) 555-NAVY (6289)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="p-4 p-lg-5 border border-navy-light h-100">
                    <h3 class="h4 text-uppercase tracking-wider text-navy mb-4 fw-bold">Send A Message</h3>
                    <form onsubmit="event.preventDefault(); alert('Message sent successfully!'); this.reset();" class="row g-3">
                        <div class="col-md-6">
                            <label for="contact-name" class="form-label text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 500;">Full Name</label>
                            <input type="text" class="form-control bg-transparent text-navy border-navy-light shadow-none py-2" id="contact-name" required style="border-radius: 0;">
                        </div>
                        <div class="col-md-6">
                            <label for="contact-email" class="form-label text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 500;">Email Address</label>
                            <input type="email" class="form-control bg-transparent text-navy border-navy-light shadow-none py-2" id="contact-email" required style="border-radius: 0;">
                        </div>
                        <div class="col-12">
                            <label for="contact-subject" class="form-label text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 500;">Subject</label>
                            <input type="text" class="form-control bg-transparent text-navy border-navy-light shadow-none py-2" id="contact-subject" required style="border-radius: 0;">
                        </div>
                        <div class="col-12">
                            <label for="contact-message" class="form-label text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 500;">Message</label>
                            <textarea class="form-control bg-transparent text-navy border-navy-light shadow-none py-2" id="contact-message" rows="5" required style="border-radius: 0;"></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-navy py-3 w-100 text-uppercase">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
