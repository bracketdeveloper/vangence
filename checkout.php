<?php
include 'includes/header.php';
?>

    <!-- Page Title Banner -->
    <div class="bg-light py-5 border-bottom border-light">
        <div class="container px-lg-5 text-center">
            <span class="text-uppercase tracking-widest text-muted" style="font-size: 0.8rem; font-weight: 500;">Checkout</span>
            <h1 class="h2 text-uppercase tracking-wider text-navy mt-2 mb-0 fw-bold">Secure Checkout</h1>
            <div class="mx-auto mt-3" style="width: 50px; height: 1.5px; background-color: var(--color-navy);"></div>
        </div>
    </div>

    <!-- Main Checkout Container -->
    <div class="container-fluid px-lg-5 py-5">
        <div id="checkout-main-content">
            <div class="row g-5">
                <!-- Left Side: Shipping & Payment Form -->
                <div class="col-lg-7">
                    <div class="p-4 p-lg-5 border border-navy-light">
                        <h3 class="h5 text-uppercase tracking-wider text-navy mb-4 fw-bold">Shipping Information</h3>

                        <!-- Added novalidate to handle custom Bootstrap validation -->
                        <form id="checkout-form" class="row g-3" novalidate>
                            <div class="col-md-6">
                                <label for="bill-fname" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">First Name</label>
                                <input type="text" class="form-control" id="bill-fname" required pattern=".*\S+.*">
                                <div class="invalid-feedback">Please enter a valid first name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="bill-lname" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">Last Name</label>
                                <input type="text" class="form-control" id="bill-lname" required pattern=".*\S+.*">
                                <div class="invalid-feedback">Please enter a valid last name.</div>
                            </div>
                            <div class="col-12">
                                <label for="bill-email" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">Email Address</label>
                                <input type="email" class="form-control" id="bill-email" required>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            <div class="col-12">
                                <label for="bill-address" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">Street Address</label>
                                <input type="text" class="form-control" id="bill-address" required pattern=".*\S+.*" placeholder="Apartment, suite, unit, etc.">
                                <div class="invalid-feedback">Please enter your address.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="bill-city" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">City</label>
                                <input type="text" class="form-control" id="bill-city" required pattern=".*\S+.*">
                                <div class="invalid-feedback">Please enter your city.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="bill-state" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">State</label>
                                <input type="text" class="form-control" id="bill-state" required pattern=".*\S+.*">
                                <div class="invalid-feedback">Please enter your state.</div>
                            </div>
                            <div class="col-12">
                                <label for="bill-phone" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">Phone Number</label>
                                <input type="tel" class="form-control" id="bill-phone" required pattern="[0-9]{10,}">
                                <div class="invalid-feedback">Please enter a valid phone number (minimum 10 digits).</div>
                            </div>

                            <h3 class="h5 text-uppercase tracking-wider text-navy mt-5 mb-4 fw-bold">Payment Details</h3>

                            <div class="col-12 mb-3">
                                <div class="form-check form-check-inline me-4">
                                    <input class="form-check-input" type="radio" name="payment-method" id="pay-card" value="card" checked>
                                    <label class="form-check-label text-uppercase" for="pay-card" style="font-size: 0.8rem; font-weight: 500; cursor: pointer;">
                                        Credit Card <i class="fa-solid fa-credit-card ms-1"></i>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment-method" id="pay-cod" value="cod">
                                    <label class="form-check-label text-uppercase" for="pay-cod" style="font-size: 0.8rem; font-weight: 500; cursor: pointer;">
                                        Cash on Delivery <i class="fa-solid fa-truck-ramp-box ms-1"></i>
                                    </label>
                                </div>
                            </div>

                            <div id="card-payment-fields" class="row g-3">
                                <div class="col-12">
                                    <label for="card-num" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">Card Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-navy-light text-navy"><i class="fa-solid fa-credit-card"></i></span>
                                        <input type="text" class="form-control border-start-0" id="card-num" pattern="\d{16}" placeholder="0000 0000 0000 0000">
                                    </div>
                                    <div class="invalid-feedback">Enter 16-digit card number.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="card-expiry" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">Expiration Date</label>
                                    <input type="text" class="form-control" id="card-expiry" pattern="(0[1-9]|1[0-2])\/\d{2}" placeholder="MM / YY">
                                    <div class="invalid-feedback">Format MM / YY.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="card-cvc" class="form-label text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 500;">CVC / CVV</label>
                                    <input type="text" class="form-control" id="card-cvc" pattern="\d{3,4}" placeholder="000">
                                    <div class="invalid-feedback">Enter 3 or 4 digits.</div>
                                </div>
                            </div>

                            <div class="col-12 mt-5 pt-3 border-top border-light">
                                <button type="submit" class="btn btn-navy w-100 py-3 text-uppercase fw-bold" id="btn-place-order">
                                    Place Order &bull; <span id="btn-total-val">PKR 0.00</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Side: Order Summary Panel -->
                <div class="col-lg-5">
                    <div class="cart-summary-box bg-light border-0 p-4 p-lg-5 h-100">
                        <h3 class="h5 pb-3 mb-4 border-bottom border-navy tracking-wider text-uppercase fw-bold">Order Summary</h3>

                        <div id="checkout-summary-items" class="d-flex flex-column gap-3 mb-4" style="max-height: 350px; overflow-y: auto;">
                        </div>

                        <hr class="border-navy-light my-4">

                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                            <span>Subtotal</span>
                            <span class="text-navy fw-medium" id="summary-subtotal">PKR 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                            <span>Shipping</span>
                            <span class="text-navy fw-medium" id="summary-shipping">PKR 0.00</span>
                        </div>

                        <div class="d-flex justify-content-between py-3 border-top border-navy border-2 mb-4">
                            <span class="fw-semibold">Total Cost</span>
                            <span class="text-navy fw-bold h5 mb-0" id="summary-total">PKR 0.00</span>
                        </div>

                        <div class="p-3 bg-white text-center border border-navy-light" style="font-size: 0.75rem;">
                            <span class="text-muted"><i class="fa-solid fa-shield-halved me-1 text-navy"></i> 256-bit SSL Encrypted Connection</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cart = JSON.parse(localStorage.getItem('vangence_cart')) || [];
            const container = document.getElementById('checkout-main-content');
            const checkoutForm = document.getElementById('checkout-form');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5 my-5">
                        <div class="mb-4"><i class="fa-solid fa-bag-shopping text-navy" style="font-size: 4rem; opacity: 0.2;"></i></div>
                        <h2 class="h4 mb-3 tracking-wide text-uppercase">No items to checkout</h2>
                        <a href="shop.php" class="btn btn-navy px-4 py-3">Explore Products</a>
                    </div>
                `;
                return;
            }

            // Payment Field Toggle
            const payCardRadio = document.getElementById('pay-card');
            const cardFields = document.getElementById('card-payment-fields');
            const cardInputs = cardFields.querySelectorAll('input');

            function togglePaymentFields() {
                if (payCardRadio.checked) {
                    cardFields.style.display = 'flex';
                    cardInputs.forEach(input => input.setAttribute('required', 'true'));
                } else {
                    cardFields.style.display = 'none';
                    cardInputs.forEach(input => input.removeAttribute('required'));
                }
            }
            document.querySelectorAll('input[name="payment-method"]').forEach(radio => {
                radio.addEventListener('change', togglePaymentFields);
            });
            togglePaymentFields();

            // Render Summary
            const summaryItemsContainer = document.getElementById('checkout-summary-items');
            let subtotal = 0;

            cart.forEach(item => {
                const totalItemPrice = item.price * item.qty;
                subtotal += totalItemPrice;
                summaryItemsContainer.insertAdjacentHTML('beforeend', `
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 50px; height: 65px; overflow: hidden; border: 1px solid var(--color-navy-light);">
                                <img src="admin/uploads/${item.image}" alt="${item.name}" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <h4 class="h6 mb-1 text-navy" style="font-size: 0.8rem; font-weight: 600; max-width: 180px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">${item.name}</h4>
                                <span class="text-muted d-block" style="font-size: 0.75rem;">Size: ${item.size} | Qty: ${item.qty}</span>
                            </div>
                        </div>
                        <span class="text-navy fw-semibold" style="font-size: 0.8rem;">${totalItemPrice.toFixed(2)}</span>
                    </div>
                `);
            });

            // Shipping Logic
            const shipping = subtotal > 10000 ? 0 : 250.00;
            const finalTotal = subtotal + shipping;

            document.getElementById('summary-subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('summary-shipping').textContent = shipping.toFixed(2);
            document.getElementById('summary-total').textContent = 'PKR ' + finalTotal.toFixed(2);
            document.getElementById('btn-total-val').textContent = 'PKR ' + finalTotal.toFixed(2);

            // Form Submit & Validation
            checkoutForm.addEventListener('submit', function(e) {
                if (!checkoutForm.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    e.preventDefault();
                    alert('Order Placed Successfully!');
                    localStorage.setItem('vangence_cart', JSON.stringify([]));
                    window.location.href = 'shop.php';
                }
                checkoutForm.classList.add('was-validated');
            });
        });
    </script>

<?php include 'includes/footer.php'; ?>