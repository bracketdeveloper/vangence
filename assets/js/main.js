document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------
    // 1. STICKY HEADER
    // ----------------------------------------------------
    const header = document.querySelector('header');
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                header.classList.add('navbar-scrolled');
            } else {
                header.classList.remove('navbar-scrolled');
            }
        });
    }

    // ----------------------------------------------------
    // 2. HEADER SEARCH (inline toggle on existing icon)
    // ----------------------------------------------------
    function initHeaderSearch(wrapper) {
        if (!wrapper) return;

        const toggleBtn = wrapper.querySelector('.nav-icon, button[id*="SearchToggle"]');
        const form = wrapper.querySelector('.header-search-form');
        const input = wrapper.querySelector('.header-search-input');
        const closeBtn = wrapper.querySelector('.header-search-close');

        if (!toggleBtn || !form || !input) return;

        const existingSearch = new URLSearchParams(window.location.search).get('search');
        if (existingSearch) {
            input.value = existingSearch;
        }

        toggleBtn.addEventListener('click', function () {
            wrapper.classList.add('is-active');
            input.focus();
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                wrapper.classList.remove('is-active');
                input.value = '';
            });
        }

        form.addEventListener('submit', function (e) {
            if (!input.value.trim()) {
                e.preventDefault();
                input.focus();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && wrapper.classList.contains('is-active')) {
                wrapper.classList.remove('is-active');
            }
        });
    }

    initHeaderSearch(document.getElementById('desktopHeaderSearch'));
    initHeaderSearch(document.getElementById('mobileHeaderSearch'));

    // ----------------------------------------------------
    // 3. SHOP PAGE FILTERS INTERACTION
    // ----------------------------------------------------
    // Color selector active state trigger
    const colorDots = document.querySelectorAll('.color-dot');
    colorDots.forEach(dot => {
        dot.addEventListener('click', function () {
            const parent = this.closest('.color-selector');
            parent.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
            this.classList.add('active');
            
            // If we are on product page, update button attributes or hidden input
            if (this.dataset.colorName) {
                const addToCartBtn = document.querySelector('.btn-add-to-cart-detail');
                if (addToCartBtn) {
                    addToCartBtn.dataset.color = this.dataset.colorName;
                }
            }
        });
    });

    // Size button active state trigger
    const sizeBtns = document.querySelectorAll('.size-btn');
    sizeBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const parent = this.closest('.size-grid');
            parent.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // If we are on product page, update button attributes or hidden input
            if (this.dataset.sizeValue) {
                const addToCartBtn = document.querySelector('.btn-add-to-cart-detail');
                if (addToCartBtn) {
                    addToCartBtn.dataset.size = this.dataset.sizeValue;
                }
            }
        });
    });

    // Quantity selectors in Product details & Cart page
    const qtySelectors = document.querySelectorAll('.quantity-selector');
    qtySelectors.forEach(selector => {
        const minusBtn = selector.querySelector('.minus-btn');
        const plusBtn = selector.querySelector('.plus-btn');
        const input = selector.querySelector('.quantity-input');

        if (minusBtn && plusBtn && input) {
            minusBtn.addEventListener('click', function () {
                let value = parseInt(input.value) || 1;
                if (value > 1) {
                    input.value = value - 1;
                    triggerChange(input);
                }
            });

            plusBtn.addEventListener('click', function () {
                let value = parseInt(input.value) || 1;
                input.value = value + 1;
                triggerChange(input);
            });

            input.addEventListener('change', function () {
                let value = parseInt(this.value) || 1;
                if (value < 1) this.value = 1;
            });
        }
    });

    function triggerChange(element) {
        const event = new Event('change', { bubbles: true });
        element.dispatchEvent(event);
    }

    // ----------------------------------------------------
    // 4. CART SYSTEM (LOCALSTORAGE)
    // ----------------------------------------------------
    let cart = JSON.parse(localStorage.getItem('vangence_cart')) || [];

    // Update global cart badge
    function updateCartBadge() {
        const badges = document.querySelectorAll('.cart-badge');
        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        
        badges.forEach(badge => {
            if (totalItems > 0) {
                badge.textContent = totalItems;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    // Spawn minimalist Navy & White notification toast
    function showNotification(message) {
        // Remove existing toast if present
        const oldToast = document.querySelector('.vangence-toast');
        if (oldToast) oldToast.remove();

        const toast = document.createElement('div');
        toast.className = 'vangence-toast';
        toast.style.position = 'fixed';
        toast.style.bottom = '30px';
        toast.style.right = '30px';
        toast.style.backgroundColor = '#0B1F3A';
        toast.style.color = '#FFFFFF';
        toast.style.padding = '15px 25px';
        toast.style.fontSize = '0.85rem';
        toast.style.fontWeight = '500';
        toast.style.letterSpacing = '1px';
        toast.style.textTransform = 'uppercase';
        toast.style.border = '1px solid #FFFFFF';
        toast.style.zIndex = '9999';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '15px';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1)';
        
        toast.innerHTML = `
            <span>${message}</span>
            <a href="cart.php" style="color: #FFFFFF; text-decoration: underline; font-weight: 600;">View Cart</a>
        `;

        document.body.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 50);

        // Auto remove
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Add Item to Cart
    function addToCart(id, name, price, image, size, color, qty) {
        qty = parseInt(qty) || 1;
        price = parseFloat(price) || 0.00;

        // Check if item with same ID, Size, and Color already exists
        const existingItem = cart.find(item => item.id === id && item.size === size && item.color === color);

        if (existingItem) {
            existingItem.qty += qty;
        } else {
            cart.push({ id, name, price, image, size, color, qty });
        }

        localStorage.setItem('vangence_cart', JSON.stringify(cart));
        updateCartBadge();
        showNotification(`${name} Added To Cart`);
    }

    // Setup Event Listeners for "Add to Cart" Buttons on Home & Shop page
    document.body.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('btn-add-to-cart')) {
            e.preventDefault();
            const btn = e.target;
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const price = btn.dataset.price;
            const image = btn.dataset.image;
            const size = btn.dataset.size || 'M';
            const color = btn.dataset.color || 'Navy';
            
            addToCart(id, name, price, image, size, color, 1);
        }
    });

    // Setup Event Listener for Product Detail Add to Cart Button
    const detailAddToCartBtn = document.querySelector('.btn-add-to-cart-detail');
    if (detailAddToCartBtn) {
        detailAddToCartBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = this.dataset.price;
            const image = this.dataset.image;
            
            // Get selected size
            const activeSizeBtn = document.querySelector('.size-grid .size-btn.active');
            const size = activeSizeBtn ? activeSizeBtn.dataset.sizeValue : (this.dataset.size || 'M');

            // Get selected color
            const activeColorDot = document.querySelector('.color-selector .color-dot.active');
            const color = activeColorDot ? activeColorDot.dataset.colorName : (this.dataset.color || 'Navy');

            // Get quantity
            const qtyInput = document.querySelector('.quantity-input');
            const qty = qtyInput ? parseInt(qtyInput.value) : 1;

            addToCart(id, name, price, image, size, color, qty);
        });
    }

    // ----------------------------------------------------
    // 5. CART PAGE DYNAMIC RENDERER
    // ----------------------------------------------------
    const cartPageContainer = document.getElementById('cart-page-container');
    
    function renderCartPage() {
        if (!cartPageContainer) return;

        if (cart.length === 0) {
            // Render Empty Cart Layout
            cartPageContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fa-solid fa-bag-shopping text-navy" style="font-size: 4rem; opacity: 0.2;"></i>
                    </div>
                    <h2 class="h4 mb-3 tracking-wide uppercase">Your Shopping Cart is Empty</h2>
                    <p class="text-muted mb-4">You have no items in your shopping cart. Add some products to get started.</p>
                    <a href="shop.php" class="btn btn-navy px-4 py-3">Continue Shopping</a>
                </div>
            `;
            return;
        }

        // Render Cart with items
        let cartItemsHtml = '';
        let subtotal = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.qty;
            subtotal += itemTotal;

            cartItemsHtml += `
                <tr data-index="${index}">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="cart-img-wrapper me-3">
                                <img src="admin/uploads/${item.image}" alt="${item.name}" class="img-fluid w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <h4 class="cart-item-name h6 mb-1">${item.name}</h4>
                                <span class="cart-item-meta">Size: ${item.size} | Color: ${item.color}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-navy">${item.price.toFixed(2)}</td>
                    <td>
                        <div class="quantity-selector" data-index="${index}">
                            <button class="quantity-btn cart-qty-minus">-</button>
                            <input type="text" class="quantity-input cart-qty-input" value="${item.qty}" readonly>
                            <button class="quantity-btn cart-qty-plus">+</button>
                        </div>
                    </td>
                    <td class="text-navy fw-semibold">PKR ${itemTotal.toFixed(2)}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-navy cart-remove-btn" data-index="${index}" style="padding: 4px 8px;">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        const shipping = subtotal > 10000 ? 0 : 250.00;
        const finalTotal = subtotal + shipping;

        cartPageContainer.innerHTML = `
            <div class="row g-5">
                <!-- Cart Items Table -->
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table cart-table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 50%;">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                ${cartItemsHtml}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="shop.php" class="btn btn-outline-navy py-2 px-3">
                            <i class="fa-solid fa-arrow-left me-2"></i> Continue Shopping
                        </a>
                        <button id="btn-clear-cart" class="btn btn-outline-navy py-2 px-3">
                            Clear Shopping Cart
                        </button>
                    </div>
                </div>
                
                <!-- Order Summary Panel -->
                <div class="col-lg-4">
                    <div class="cart-summary-box">
                        <h3 class="h5 pb-3 mb-3 border-bottom border-navy tracking-wider text-uppercase">Order Summary</h3>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span class="text-navy fw-medium">${subtotal.toFixed(2)}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Flat Shipping</span>
                            <span class="text-navy fw-medium">${shipping.toFixed(2)}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between py-3 border-top border-navy border-2 mb-4">
                            <span class="fw-semibold">Estimated Total</span>
                            <span class="text-navy fw-bold h5 mb-0">PKR ${finalTotal.toFixed(2)}</span>
                        </div>
                        
                        <button class="btn btn-navy w-100 py-3 text-uppercase" id="btn-checkout">
                            Proceed to Checkout
                        </button>
                        
                        <div class="mt-4 pt-3 border-top border-light text-center">
                            <p class="text-muted mb-0" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-lock me-1"></i> Secure checkout powered by SSL.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Attach listeners for dynamic controls
        attachCartListeners();
    }

    function attachCartListeners() {
        // Minus Button
        document.querySelectorAll('.cart-qty-minus').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = this.closest('.quantity-selector').dataset.index;
                if (cart[idx].qty > 1) {
                    cart[idx].qty--;
                    saveAndRefreshCart();
                }
            });
        });

        // Plus Button
        document.querySelectorAll('.cart-qty-plus').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = this.closest('.quantity-selector').dataset.index;
                cart[idx].qty++;
                saveAndRefreshCart();
            });
        });

        // Remove Item Button
        document.querySelectorAll('.cart-remove-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = this.dataset.index;
                cart.splice(idx, 1);
                saveAndRefreshCart();
                showNotification('Item removed from cart');
            });
        });

        // Clear Cart Button
        const clearBtn = document.getElementById('btn-clear-cart');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (confirm('Are you sure you want to clear your entire cart?')) {
                    cart = [];
                    saveAndRefreshCart();
                    showNotification('Cart cleared');
                }
            });
        }

        // Checkout Button Click
        const checkoutBtn = document.getElementById('btn-checkout');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function () {
                window.location.href = 'checkout.php';
            });
        }
    }

    function saveAndRefreshCart() {
        localStorage.setItem('vangence_cart', JSON.stringify(cart));
        updateCartBadge();
        renderCartPage();
    }

    // Initialize UI
    updateCartBadge();
    renderCartPage();
});
