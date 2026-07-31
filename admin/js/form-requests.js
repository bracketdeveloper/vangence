function checkEmptyInput(inputEl, message) {
    if (!inputEl || inputEl.value.trim() === "") {
        alert(message);
        if (inputEl) inputEl.focus();
        return false;
    }
    return true;
}

$(document).on('click', '.remove-image-btn', function () {
    $(this).parent().remove();
});

function sendAjaxRequest(url, formData, redirectUrl) {
    $.ajax({
        url: url,
        type: 'POST',
        contentType: false,
        processData: false,
        data: formData
    }).done(function (data) {
        console.log(data)
        alert(data);
        let isData = redirectUrl && data !== "Product with same name already exists.";
        if (isData) {
            window.location.href = redirectUrl;
        }
        clearCartTable();
    });
}

function sendAjaxLoginRequest(url, formData, redirectUrl) {
    $.ajax({
        url: url,
        type: 'POST',
        contentType: false,
        processData: false,
        data: formData
    }).done(function (data) {
        var response = data.trim();
        alert(response);
        if (redirectUrl && response === "Login successful.") {
            window.location.href = redirectUrl;
        }
    });
}

function sendAjaxBillRequest(url, formData) {
    $.ajax({
        url: url,
        type: 'POST',
        contentType: false,
        processData: false,
        data: formData
    }).done(function (data) {
        if (data.length === 0) {
            alert("No product found")
            return;
        }
        let product = data[0];
        let productId = product.product_id;
        let productName = product.product_name;
        let sellingPrice = product.selling_price;
        let barcode = product.barcode;
        let stockQty = product.qty;
        addProduct(barcode, productName, sellingPrice, productId, stockQty);
    });
}

function addProduct(barcode, productName, price, productId, stockQty) {
    let table = $("#bill-table tbody");
    let row = $(`tr[data-barcode="${barcode}"]`);

    if (row.length > 0) {
        let qtyCell = row.find(".qty");
        let qty = parseInt(qtyCell.text()) + 1;

        if (qty > stockQty) {
            alert("Cannot add more than available stock");
            return;
        }

        qtyCell.text(qty);
        recalcRow(row);
    } else {
        let subtotal = price * 1;
        let tax = subtotal * 0.12;
        let lineTotal = subtotal + tax;

        let newRow = `
        <tr data-barcode="${barcode}" data-stock="${stockQty}" data-discount="0">
            <td>${$("#bill-table tbody tr").length + 1}</td>
            <td class="product-id" hidden>${productId}</td>
            <td class="product-name">${productName}</td>
            <td class="price">${price}</td>
            <td class="qty">1</td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control discount-percent" min="1" max="25" placeholder="0">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary apply-discount" type="button">Apply</button>
                    </div>
                </div>
            </td>
            <td class="discount-amt">0.00</td>
            <td class="tax">${tax.toFixed(2)}</td>
            <td class="line-total">${lineTotal.toFixed(2)}</td>
            <td>
                <button class="increase">+</button>
                <button class="decrease">-</button>
                <button class="remove">Removed</button>
            </td>
        </tr>
        `;
        table.append(newRow);
    }
    updateTotal();
}

// Recalculates discount amount, tax, and line total for a single row
// based on its current price, qty, and stored discount percentage.
function recalcRow(row) {
    let price = parseFloat(row.find(".price").text()) || 0;
    let qty = parseInt(row.find(".qty").text()) || 0;
    let discountPercent = parseFloat(row.attr("data-discount")) || 0;

    let subtotal = price * qty;
    let discountAmt = subtotal * (discountPercent / 100);
    let taxable = subtotal - discountAmt;
    let tax = taxable * 0.12;
    let lineTotal = taxable + tax;

    row.find(".discount-amt").text(discountAmt.toFixed(2));
    row.find(".tax").text(tax.toFixed(2));
    row.find(".line-total").text(lineTotal.toFixed(2));

    updateTotal();
}

// Applies the discount percentage entered for a row as soon as
// the user presses the row's Apply button, validating 1-25.
$(document).on("click", ".apply-discount", function () {
    let row = $(this).closest("tr");
    let input = row.find(".discount-percent");
    let value = parseFloat(input.val());

    if (isNaN(value) || input.val().trim() === "") {
        alert("Enter a discount percentage");
        input.focus();
        return;
    }
    if (value < 1) {
        alert("Discount must be at least 1%");
        input.focus();
        return;
    }
    if (value > 25) {
        alert("Discount cannot exceed 25%");
        input.focus();
        return;
    }

    row.attr("data-discount", value);
    recalcRow(row);
});

function updateTotal() {
    let total = 0;
    $("#bill-table tbody tr").each(function () {
        let lineTotal = parseFloat($(this).find(".line-total").text()) || 0;
        total += lineTotal;
    });
    $("#final-bill").text(total.toFixed(2));
}

$(document).on("click", ".increase", function () {
    let row = $(this).closest("tr");
    let qty = parseInt(row.find(".qty").text());
    let stock = parseInt(row.data("stock"));

    if (qty < stock) {
        qty++;
        row.find(".qty").text(qty);
        recalcRow(row);
    } else {
        alert("Cannot exceed available stock");
    }
});

$(document).on("click", ".decrease", function () {
    let row = $(this).closest("tr");
    let qty = parseInt(row.find(".qty").text());

    if (qty > 1) {
        qty--;
        row.find(".qty").text(qty);
        recalcRow(row);
    } else {
        updateTotal();
    }
});

$(document).on("click", ".remove", function () {
    $(this).closest("tr").remove();
    updateRowNumbers();
    updateTotal();
});

function updateRowNumbers() {
    $("#cartTable tbody tr").each(function (index) {
        $(this).find("td:first").text(index + 1);
    });
}

function checkValidEmail(inputEl) {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!pattern.test(inputEl.value.trim())) {
        alert("Please enter a valid email address.");
        inputEl.focus()
        return false;
    }
    return true;
}

function validateNewCategory() {
    const newCategoryEl = document.getElementById('new-category');
    const parentCategoryEl = document.getElementById('parent-category');
    if (!checkEmptyInput(newCategoryEl, "Enter the category")) {
        return;
    }
    var formData = new FormData();
    formData.append('new_category', newCategoryEl.value);
    formData.append('parent_id', parentCategoryEl.value);
    sendAjaxRequest(
        "model/ajax.php?action=add_new_category",
        formData,
        "add-new-category.php"
    );
}

function validateEditCategory(categoryId) {
    const editCategoryEl = document.getElementById('edit-category');
    const parentCategoryEl = document.getElementById('edit-parent-category');
    if (!checkEmptyInput(editCategoryEl, "Enter the category")) {
        return;
    }
    var formData = new FormData();
    formData.append('edit_category', editCategoryEl.value);
    formData.append('parent_id', parentCategoryEl.value);
    formData.append('category_id', categoryId);
    sendAjaxRequest("model/ajax.php?action=edit_category", formData, `edit-category.php?category_id=${categoryId}`);
}

function validateDeleteCategory(categoryId) {
    if (confirm("Do you want to delete this category?") == true) {
        var formData = new FormData();
        formData.append('category_id', categoryId);
        sendAjaxRequest("model/ajax.php?action=delete_category", formData, "categories.php");
    }
}

function validateNewProduct() {
    const elements = {
        name: document.getElementById('product-name'),
        cat: document.getElementById('category-id'),
        price: document.getElementById('selling-price'),
        qty: document.getElementById('qty'),
        sizes: document.getElementById('sizes'),
        colors: document.getElementById('colors'),
        desc: document.getElementById('description'),
        details: document.getElementById('details'),
        imgs: document.getElementById('images')
    };
    if (!checkEmptyInput(elements.name, "Enter name")) return false;
    if (!checkEmptyInput(elements.cat, "Select category")) return false;
    if (!checkEmptyInput(elements.price, "Enter price")) return false;
    if (!checkEmptyInput(elements.qty, "Enter qty")) return false;
    if (!checkEmptyInput(elements.sizes, "Enter sizes")) return false;
    if (!checkEmptyInput(elements.colors, "Enter colors")) return false;
    if (elements.imgs.files.length === 0) {
        alert("Select at least one image");
        elements.imgs.focus();
        return false;
    }
    if (!checkEmptyInput(elements.desc, "Enter description")) return false;
    if (!checkEmptyInput(elements.details, "Enter details")) return false;
    var formData = new FormData();
    formData.append('product_name', elements.name.value);
    formData.append('category_id', elements.cat.value);
    formData.append('selling_price', elements.price.value);
    formData.append('qty', elements.qty.value);
    formData.append('sizes', elements.sizes.value);
    formData.append('colors', elements.colors.value);
    formData.append('description', elements.desc.value);
    formData.append('details', elements.details.value);
    for (let i = 0; i < elements.imgs.files.length; i++) {
        formData.append('images[]', elements.imgs.files[i]);
    }
    sendAjaxRequest("model/ajax.php?action=add_new_product", formData, "add-new-product.php");
    return true;
}

function validateEditProduct(productId) {
    const productNameEl = document.getElementById('edit-product-name');
    const descriptionEl = document.getElementById('edit-description');
    const detailsEl = document.getElementById('edit-details');
    const sellingPriceEl = document.getElementById('edit-selling-price');
    const imagesEl = document.getElementById('edit-images').files;
    const qtyEl = document.getElementById('edit-qty');
    const categoryIdEl = document.getElementById('edit-category-id');
    const sizesEl = document.getElementById('edit-sizes');
    const colorsEl = document.getElementById('edit-colors');
    const existingImages = [];
    $('#existing-images img').each(function () {
        existingImages.push($(this).attr('src').split('/').pop());
    });
    if (!checkEmptyInput(productNameEl, "Enter the product name")) {
        return;
    }
    if (!checkEmptyInput(categoryIdEl, "Select the category")) {
        return;
    }
    if (!checkEmptyInput(sellingPriceEl, "Enter the selling price")) {
        return;
    }
    if (!checkEmptyInput(qtyEl, "Enter the product quantity")) {
        return;
    }
    if (!checkEmptyInput(sizesEl, "Enter the sizes")) {
        return;
    }
    if (!checkEmptyInput(colorsEl, "Enter the colors")) {
        return;
    }
    if (!checkEmptyInput(descriptionEl, "Enter the description")) {
        return;
    }
    if (!checkEmptyInput(detailsEl, "Enter the product details")) {
        return;
    }
    if (imagesEl.length === 0 && existingImages.length === 0) {
        alert("Select at least one image");
        return;
    }
    var formData = new FormData();
    formData.append('edit_product_id', productId);
    formData.append('edit_product_name', productNameEl.value);
    formData.append('edit_category_id', categoryIdEl.value);
    formData.append('edit_selling_price', sellingPriceEl.value);
    formData.append('edit_qty', qtyEl.value);
    formData.append('edit_sizes', sizesEl.value);
    formData.append('edit_colors', colorsEl.value);
    formData.append('edit_description', descriptionEl.value);
    formData.append('edit_details', detailsEl.value);
    for (let i = 0; i < imagesEl.length; i++) {
        formData.append('images[]', imagesEl[i]);
    }
    formData.append('existing_images', JSON.stringify(existingImages));
    sendAjaxRequest("model/ajax.php?action=edit_product", formData, `edit-product.php?product_id=${productId}`);
}

function validateDeleteProduct(productId) {
    if (confirm("Do you want to delete this product?") == true) {
        var formData = new FormData();
        formData.append('product_id', productId);
        sendAjaxRequest("model/ajax.php?action=delete_product", formData, "products.php");
    }
}

function toggleProductStatus(checkbox, productId) {
    let newStatus = checkbox.checked ? 1 : 0;
    let formData = new FormData();
    formData.append('product_id', productId);
    formData.append('is_active', newStatus);

    $.ajax({
        url: 'model/ajax.php?action=toggle_product_status',
        type: 'POST',
        contentType: false,
        processData: false,
        data: formData
    }).done(function (response) {
        console.log(response);
        if (response.trim() !== "Product status updated successfully.") {
            alert(response);
            checkbox.checked = !checkbox.checked;
        }
    }).fail(function () {
        alert("Something went wrong while updating product status.");
        checkbox.checked = !checkbox.checked;
    });
}

function validateNewUser() {
    const nameEl = document.getElementById('name');
    const emailEl = document.getElementById('email');
    const roleEl = document.getElementById('role');
    if (!checkEmptyInput(nameEl, "Enter the name")) {
        return;
    }
    if (!checkEmptyInput(emailEl, "Enter the email")) {
        return;
    }
    if (!checkValidEmail(emailEl)) {
        return;
    }
    if (!checkEmptyInput(roleEl, "Select the role")) {
        return;
    }
    var formData = new FormData();
    formData.append('name', nameEl.value);
    formData.append('email', emailEl.value);
    formData.append('password', 123);
    formData.append('role', roleEl.value);
    sendAjaxRequest("model/ajax.php?action=add_new_user", formData, "add-new-user.php");
}

function validateChangePassword() {
    const newPasswordEl = document.getElementById('new-password');
    const confirmPasswordEl = document.getElementById('confirm-password');
    const currentPasswordEl = document.getElementById('current-password');
    if (!checkEmptyInput(newPasswordEl, "Enter the new password")) {
        return;
    }
    if (newPasswordEl.value !== confirmPasswordEl.value) {
        alert("New password does not match to confirm password")
        confirmPasswordEl.focus();
        return;
    }
    if (!checkEmptyInput(currentPasswordEl, "Enter current password")) {
        return;
    }
    var formData = new FormData();
    formData.append('new_password', newPasswordEl.value);
    formData.append('current_password', currentPasswordEl.value);
    sendAjaxRequest("model/ajax.php?action=change_password", formData, "settings.php");
}

function validateProductForBill() {
    const barcodeEl = document.getElementById('barcode');
    if (!checkEmptyInput(barcodeEl, "Enter the barcode")) {
        return;
    }
    if (barcodeEl.value.length < 5) {
        alert("Enter at least 5 digits");
        barcodeEl.focus();
        return;
    }
    var barcode = barcodeEl.value;
    var formData = new FormData();
    if (barcode.length > 12) {
        barcode = barcode.substring(0, barcode.length - 1);
    }
    formData.append('barcode', barcode);
    sendAjaxBillRequest("model/ajax.php?action=add_to_bill", formData);
    barcodeEl.value = "";
}

$("#barcode").on("keydown", function (e) {
    if (e.key === "Enter") {
        validateProductForBill();
    }
});
$("#edit-barcode").on("keydown", function (e) {
    if (e.key === "Enter") {
        validateProductForBillEdit();
    }
});

function getTableData() {
    let items = [];
    $('#bill-table tbody tr').each(function () {
        items.push({
            product_name: $(this).find('.product-name').text(),
            product_id: $(this).find('.product-id').text(),
            price: parseFloat($(this).find('.price').text()),
            qty: parseInt($(this).find('.qty').text()),
            discount_percent: parseFloat($(this).attr('data-discount')) || 0,
            discount_amount: parseFloat($(this).find('.discount-amt').text()) || 0,
            tax: parseFloat($(this).find('.tax').text()),
            total: parseFloat($(this).find('.line-total').text())
        });
    });
    return items;
}

function clearCartTable() {
    let tbody = document.querySelector('#bill-table tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    document.getElementById('final-bill').textContent = '0';
    document.getElementById("barcode").value = "";
}

$('input[name="payment-method"]').on('change', function () {
    let method = $(this).val();
    if (method === 'cash') {
        $('#cash-section').show();
        $('#action-buttons').show();
    } else if (method === 'card') {
        $('#cash-section').hide();
        $('#amount-received').val('');
        $('#change-given').val('');
        $('#action-buttons').show();
    }
});

$('#amount-received').on('input', function () {
    let finalBill = parseFloat($('#final-bill').text()) || 0;
    let received = parseFloat($(this).val()) || 0;
    let change = received - finalBill;
    $('#change-given').val(change > 0 ? change.toFixed(2) : '0.00');
});

function saveBill(printAfter) {
    let rows = getTableData();
    if (!rows || rows.length === 0) {
        alert('No items in table');
        return;
    }
    let finalBill = parseFloat($('#final-bill').text()) || 0;
    let paymentMethod = $('input[name="payment-method"]:checked').val();
    if (!paymentMethod) {
        alert('Select a payment method');
        return;
    }
    let amountReceived = 0;
    let changeGiven = 0;

    if (paymentMethod === 'cash') {
        amountReceived = parseFloat($('#amount-received').val()) || 0;
        if (amountReceived <= 0) {
            alert('Enter amount received');
            $('#amount-received').focus()
            return;
        }
        if (amountReceived < finalBill) {
            alert('Received amount is less than final bill');
            return;
        }
        changeGiven = amountReceived - finalBill;
    } else {
        amountReceived = finalBill;
    }

    let productQtyMap = {};
    rows.forEach(item => {
        productQtyMap[item.product_id] = item.qty;
    });

    var formData = new FormData();
    formData.append('rows', JSON.stringify(rows));
    formData.append('productQtyMap', JSON.stringify(productQtyMap));
    formData.append('final_bill', finalBill);
    formData.append('payment_method', paymentMethod);
    formData.append('amount_received', amountReceived);
    formData.append('change_given', changeGiven);
    formData.append('print_after', printAfter ? '1' : '0');

    $.ajax({
        url: "model/ajax.php?action=save_bill",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            try {
                let res = (typeof response === 'string') ? JSON.parse(response) : response;
                console.log(res)
                if (res.status === 'success') {
                    alert(res.message);
                    if (res.print) {
                        printBill(res);
                    }
                    clearCartTable();
                    $('input[name="payment-method"]').prop('checked', false);
                    $('#cash-section').hide();
                    $('#action-buttons').hide();
                } else {
                    alert(res.message || 'Error saving bill');
                }
            } catch (e) {
                alert(response);
                clearCartTable();
                $('input[name="payment-method"]').prop('checked', false);
                $('#cash-section').hide();
                $('#action-buttons').hide();
            }
        },
        error: function () {
            alert('Error saving bill');
        }
    });
}

function printBill(data) {
    console.log(data)

    // Edit these to match your actual store details
    const SHOP_NAME = "Vangence";
    const SHOP_ADDRESS = "123 Main Street, Lahore, Punjab";
    const SHOP_PHONE = "+92 300 1234567";

    const TERMS = [
        "No cash refunds.",
        "Unwashed and unworn items with the original price tag attached can be exchanged within 15 days of the purchase date if a sales receipt is provided.",
        "We do not accept returns or exchanges for fabrics that have already been washed or shrunk by the customer.",
        "Items bought on sale can't be exchanged or returned.",
        "For reasons of hygiene, intimates, jewelry, fragrances, and accessories are not eligible for a refund or exchange."
    ];

    let win = window.open('', '_blank', 'width=900,height=1100');
    win.document.write('<html><head><title>Bill #' + data.sale_id + '</title>');
    win.document.write('<style>');
    win.document.write('body{font-family:monospace;font-size:12px;margin:20px;color:#111;}');
    win.document.write('.shop-name{text-align:center;font-size:18px;font-weight:bold;letter-spacing:1px;margin:0;}');
    win.document.write('.shop-info{text-align:center;font-size:11px;margin:2px 0;color:#333;}');
    win.document.write('h3{text-align:center;margin:10px 0 4px;}');
    win.document.write('.center{text-align:center;}');
    win.document.write('.right{text-align:right;}');
    win.document.write('table{width:100%;border-collapse:collapse;margin-top:8px;}');
    win.document.write('th{border-bottom:2px solid #000;padding:4px 6px;text-align:left;}');
    win.document.write('th.right{text-align:right;}');
    win.document.write('td{padding:3px 6px;border-bottom:1px dotted #999;}');
    win.document.write('td.right{text-align:right;}');
    win.document.write('.summary{margin-top:12px;width:100%;}');
    win.document.write('.summary td{border:none;padding:2px 6px;}');
    win.document.write('.summary .label{font-weight:bold;}');
    win.document.write('hr{border:none;border-top:1px solid #000;margin:8px 0;}');
    win.document.write('.dhr{border-top:2px solid #000;}');
    win.document.write('.terms{margin-top:14px;font-size:10.5px;line-height:1.5;}');
    win.document.write('.terms h4{margin:0 0 4px;text-align:center;text-transform:uppercase;letter-spacing:0.5px;font-size:11px;}');
    win.document.write('.terms ul{margin:0;padding-left:16px;}');
    win.document.write('.terms li{margin-bottom:3px;}');
    win.document.write('.footer{text-align:center;margin-top:14px;font-size:11px;}');
    win.document.write('</style>');
    win.document.write('</head><body>');

    // Shop header
    win.document.write('<p class="shop-name">' + SHOP_NAME + '</p>');
    win.document.write('<p class="shop-info">' + SHOP_ADDRESS + '</p>');
    win.document.write('<p class="shop-info">Tel: ' + SHOP_PHONE + '</p>');

    win.document.write('<hr class="dhr">');
    win.document.write('<h3>Receipt</h3>');
    win.document.write('<p class="center">Bill #: ' + data.sale_id + '</p>');
    win.document.write('<hr class="dhr">');
    win.document.write('<table>');
    win.document.write('<thead><tr>');
    win.document.write('<th>#</th>');
    win.document.write('<th>Item</th>');
    win.document.write('<th class="right">Price</th>');
    win.document.write('<th class="right">Qty</th>');
    win.document.write('<th class="right">Disc%</th>');
    win.document.write('<th class="right">Disc Amt</th>');
    win.document.write('<th class="right">Tax(12%)</th>');
    win.document.write('<th class="right">Total</th>');
    win.document.write('</tr></thead><tbody>');
    let items = data.items || [];
    let html = '';
    items.forEach(function (r, i) {
        let discPercent = parseFloat(r.discount_percent) || 0;
        let discAmt     = parseFloat(r.discount_amount)  || 0;
        let tax         = parseFloat(r.tax)               || 0;
        let total       = parseFloat(r.total)             || 0;
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + r.product_name + '</td>';
        html += '<td class="right">' + parseFloat(r.price).toFixed(2) + '</td>';
        html += '<td class="right">' + r.qty + '</td>';
        html += '<td class="right">' + (discPercent > 0 ? discPercent + '%' : '-') + '</td>';
        html += '<td class="right">' + (discAmt > 0 ? discAmt.toFixed(2) : '-') + '</td>';
        html += '<td class="right">' + tax.toFixed(2) + '</td>';
        html += '<td class="right">' + total.toFixed(2) + '</td>';
        html += '</tr>';
    });
    win.document.write(html);
    win.document.write('</tbody></table>');
    win.document.write('<hr class="dhr">');
    win.document.write('<table class="summary">');
    win.document.write('<tr><td class="label">Final Bill:</td><td class="right">' + parseFloat(data.final_bill || 0).toFixed(2) + '</td></tr>');
    win.document.write('<tr><td class="label">Payment Method:</td><td class="right">' + (data.payment_method || '-') + '</td></tr>');
    win.document.write('<tr><td class="label">Amount Received:</td><td class="right">' + parseFloat(data.amount_received || 0).toFixed(2) + '</td></tr>');
    win.document.write('<tr><td class="label">Change Given:</td><td class="right">' + parseFloat(data.change_given || 0).toFixed(2) + '</td></tr>');
    win.document.write('</table>');

    // Terms & Conditions
    win.document.write('<div class="terms">');
    win.document.write('<hr>');
    win.document.write('<h4>Terms &amp; Conditions</h4>');
    win.document.write('<ul>');
    TERMS.forEach(function (t) {
        win.document.write('<li>' + t + '</li>');
    });
    win.document.write('</ul>');
    win.document.write('</div>');

    win.document.write('<p class="footer">Thank you for your purchase!</p>');
    win.document.write('</body></html>');
    win.document.close();
    win.focus();
    win.print();
}

$('#save-bill').click(function () {
    saveBill(false);
});

$('#print-bill').click(function () {
    saveBill(true);
});

window.removeRowEdit = function (btn) {
    const row = btn.closest('tr');
    row.remove();
    renumberRowsEdit();
    calculateFinalBillEdit();
    recalculateChangeEdit();
};

window.renumberRowsEdit = function () {
    document.querySelectorAll('#bill-table tbody tr').forEach(function (row, i) {
        const numCell = row.querySelector('.row-num') || row.cells[0];
        if (numCell) numCell.textContent = i + 1;
    });
};

window.calculateFinalBillEdit = function () {
    let total = 0;
    document.querySelectorAll('#bill-table tbody tr').forEach(function (row) {
        const lineTotalEl = row.querySelector('.line-total');
        const taxEl = row.querySelector('.tax');
        const priceEl = row.querySelector('.price');
        const qtyEl = row.querySelector('.qty');

        if (lineTotalEl && lineTotalEl.textContent) {
            total += parseFloat(lineTotalEl.textContent) || 0;
        } else if (priceEl && qtyEl) {
            const price = parseFloat(priceEl.textContent) || 0;
            const qty = parseFloat(qtyEl.textContent) || 0;
            const subtotal = price * qty;
            const tax = subtotal * 0.12;
            const lineTotal = subtotal + tax;

            if (taxEl) taxEl.textContent = tax.toFixed(2);
            if (lineTotalEl) lineTotalEl.textContent = lineTotal.toFixed(2);

            total += lineTotal;
        }
    });
    const finalBillSpan = document.getElementById('final-bill');
    if (finalBillSpan) {
        finalBillSpan.textContent = total.toFixed(2);
    }
    return total;
};

window.recalculateChangeEdit = function () {
    const finalBillSpan = document.getElementById('final-bill');
    const amountReceived = document.getElementById('amount-received');
    const changeGiven = document.getElementById('change-given');
    if (!finalBillSpan || !amountReceived || !changeGiven) return;

    const total = parseFloat(finalBillSpan.textContent) || 0;
    const received = parseFloat(amountReceived.value) || 0;
    changeGiven.value = (received - total).toFixed(2);
};

window.validateProductForBillEdit = function () {
    const barcodeInput = document.getElementById('edit-barcode');
    const barcode = barcodeInput.value.trim();
    if (!barcode || barcode.length < 5) {
        alert('Please enter at least 5 digits.');
        return;
    }
    addProductEdit(barcode);
    barcodeInput.value = '';
};

window.addProductEdit = function (barcode) {
    var formData = new FormData();
    if (barcode.length > 12) {
        barcode = barcode.substring(0, barcode.length - 1);
    }
    formData.append('barcode', barcode);

    $.ajax({
        url: 'model/ajax.php?action=add_to_bill',
        type: 'POST',
        contentType: false,
        processData: false,
        data: formData
    }).done(function (data) {
        if (!data || data.length === 0) {
            alert('Product not found.');
            return;
        }
        var product    = data[0];
        var productId   = product.product_id;
        var productName = product.product_name;
        var price       = parseFloat(product.selling_price) || 0;
        var stockQty    = parseInt(product.qty) || 9999;

        var tbody = document.querySelector('#bill-table tbody');
        var exists = false;
        tbody.querySelectorAll('tr').forEach(function (row) {
            var pidEl = row.querySelector('.product-id');
            if (pidEl && pidEl.textContent.trim() === productId) {
                var qtyEl  = row.querySelector('.qty');
                var stock  = parseInt(row.getAttribute('data-stock')) || 9999;
                var qty    = parseInt(qtyEl.textContent) + 1;
                if (qty > stock) { alert('Cannot exceed available stock'); return; }
                qtyEl.textContent = qty;
                recalcRowEdit($(row));
                exists = true;
            }
        });

        if (!exists) {
            var rowCount  = tbody.querySelectorAll('tr').length;
            var subtotal  = price;
            var tax       = subtotal * 0.12;
            var lineTotal = subtotal + tax;

            var tr = document.createElement('tr');
            tr.setAttribute('data-barcode', product.barcode);
            tr.setAttribute('data-stock', stockQty);
            tr.setAttribute('data-discount', '0');
            tr.innerHTML =
                '<td class="row-num">' + (rowCount + 1) + '</td>' +
                '<td hidden class="product-id">' + productId + '</td>' +
                '<td class="product-name">' + productName + '</td>' +
                '<td class="price">' + price.toFixed(2) + '</td>' +
                '<td class="qty">1</td>' +
                '<td>' +
                '<div class="input-group input-group-sm">' +
                '<input type="number" class="form-control discount-percent" min="1" max="25" placeholder="0">' +
                '<div class="input-group-append">' +
                '<button class="btn btn-outline-secondary apply-discount-edit" type="button">Apply</button>' +
                '</div>' +
                '</div>' +
                '</td>' +
                '<td class="discount-amt">0.00</td>' +
                '<td class="tax">' + tax.toFixed(2) + '</td>' +
                '<td class="line-total">' + lineTotal.toFixed(2) + '</td>' +
                '<td>' +
                '<button class="btn btn-sm btn-secondary increase-edit">+</button> ' +
                '<button class="btn btn-sm btn-secondary decrease-edit">-</button> ' +
                '<button class="btn btn-sm btn-danger remove-edit">Remove</button>' +
                '</td>';
            tbody.appendChild(tr);
        }

        calculateFinalBillEdit();
        recalculateChangeEdit();
    });
};

window.saveSaleEdit = function () {
    var saleId = document.getElementById('sale-id').value;
    var rows = document.querySelectorAll('#bill-table tbody tr');
    if (rows.length === 0) {
        alert('Cannot update an empty bill.');
        return;
    }

    var paymentMethodEl = document.querySelector('input[name="payment-method"]:checked');
    if (!paymentMethodEl) {
        alert('Select a payment method.');
        return;
    }
    var paymentMethod = paymentMethodEl.value;

    var finalBill = parseFloat(document.getElementById('final-bill').textContent) || 0;
    var amountReceived = finalBill; // default for card
    var changeGiven    = 0;

    if (paymentMethod === 'cash') {
        var amtEl = document.getElementById('amount-received');
        amountReceived = amtEl ? (parseFloat(amtEl.value) || 0) : 0;
        if (amountReceived <= 0) {
            alert('Enter amount received.');
            if (amtEl) amtEl.focus();
            return;
        }
        if (amountReceived < finalBill) {
            alert('Received amount is less than final bill.');
            return;
        }
        changeGiven = amountReceived - finalBill;
    }

    var items = [];
    var productQtyMap = {};
    rows.forEach(function (row) {
        var productId      = row.querySelector('.product-id').textContent.trim();
        var productName    = row.querySelector('.product-name').textContent.trim();
        var price          = parseFloat(row.querySelector('.price').textContent)       || 0;
        var qty            = parseInt(row.querySelector('.qty').textContent)            || 0;
        var discountPercent = parseFloat(row.getAttribute('data-discount'))            || 0;
        var discountAmount  = parseFloat(row.querySelector('.discount-amt').textContent) || 0;
        var tax            = parseFloat(row.querySelector('.tax').textContent)         || 0;
        var total          = parseFloat(row.querySelector('.line-total').textContent)  || 0;
        items.push({
            product_id:       productId,
            product_name:     productName,
            price:            price,
            qty:              qty,
            discount_percent: discountPercent,
            discount_amount:  discountAmount,
            tax:              tax,
            total:            total
        });
        productQtyMap[productId] = qty;
    });

    var formData = new FormData();
    formData.append('sale_id',        saleId);
    formData.append('rows',           JSON.stringify(items));
    formData.append('productQtyMap',  JSON.stringify(productQtyMap));
    formData.append('finalBill',      finalBill);
    formData.append('paymentMethod',  paymentMethod);
    formData.append('amountReceived', amountReceived);
    formData.append('changeGiven',    changeGiven);

    fetch('model/ajax.php?action=update_bill', {
        method: 'POST',
        body: formData
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                alert('Sale updated successfully.');
                window.location.href = 'sales.php';
            } else {
                alert(data.message || 'Failed to update sale.');
            }
        })
        .catch(function (err) {
            alert('Error: ' + err.message);
        });
};


document.addEventListener('DOMContentLoaded', function () {
    calculateFinalBillEdit();
    recalculateChangeEdit();
});

document.addEventListener('DOMContentLoaded', function () {
    function toggleCashSection() {
        const checked = document.querySelector('input[name="payment-method"]:checked');
        const cashSection = document.getElementById('cash-section');
        const actionButtons = document.getElementById('action-buttons');
        if (!cashSection || !actionButtons) return; // not on POS page
        if (checked && checked.value === 'cash') {
            cashSection.style.display = 'flex';
        } else {
            cashSection.style.display = 'none';
        }
        if (checked) {
            actionButtons.style.display = 'flex';
        }
    }

    document.querySelectorAll('input[name="payment-method"]').forEach(function (radio) {
        radio.addEventListener('change', toggleCashSection);
    });

    const amountReceivedInput = document.getElementById('amount-received');
    if (amountReceivedInput) {
        amountReceivedInput.addEventListener('input', recalculateChangeEdit);
    }

    toggleCashSection();
    calculateFinalBillEdit();
});

function printBarcode(barcode, name, price) {
    var printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write('<html><head><title>Print Barcode</title>');
    printWindow.document.write('<style>body { text-align: center; font-family: sans-serif; } svg { width: 80%; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h3>' + name + '</h3>');
    printWindow.document.write('<p>Price: ' + price + '</p>');
    printWindow.document.write('<svg id="barcode"></svg>');
    printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>');
    printWindow.document.write('<script>JsBarcode("#barcode", "' + barcode + '", { format: "CODE128", displayValue: true });</script>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    setTimeout(function() {
        printWindow.print();
    }, 500);
}

function validateUserLogin() {
    const emailEl = document.getElementById('email');
    const passwordEl = document.getElementById('password');
    if (!checkEmptyInput(emailEl, "Enter the email")) {
        return;
    }
    if (!checkValidEmail(emailEl)) {
        return;
    }
    if (!checkEmptyInput(passwordEl, "Enter the password")) {
        return;
    }
    var formData = new FormData();
    formData.append('email', emailEl.value);
    formData.append('password', passwordEl.value);
    sendAjaxLoginRequest("model/ajax.php?action=login_user", formData, "index.php");
}

function validateUserLogout() {
    sendAjaxRequest("model/ajax.php?action=logout_user", null, "login.php");
}

function validateHeroSection() {
    const heroPretitle = document.getElementById('hero-pretitle');
    const heroTitle = document.getElementById('hero-title');
    const heroDescription = document.getElementById('hero-description');
    const heroButtonText = document.getElementById('hero-button-text');
    const heroBgImage = document.getElementById('hero-bg-image');

    const hasExistingImage = document.querySelector('img[alt="Current Background"]') !== null;

    if (!checkEmptyInput(heroPretitle, "Enter the pre-title")) return;
    if (!checkEmptyInput(heroTitle, "Enter the title")) return;
    if (!checkEmptyInput(heroDescription, "Enter the description")) return;
    if (!checkEmptyInput(heroButtonText, "Enter the button text")) return;

    if (!hasExistingImage && heroBgImage.files.length === 0) {
        alert("Select a background image");
        return;
    }

    var formData = new FormData();
    formData.append('pre_title', heroPretitle.value);
    formData.append('title', heroTitle.value);
    formData.append('description', heroDescription.value);
    formData.append('button_text', heroButtonText.value);

    if (heroBgImage.files.length > 0) {
        formData.append('bg_image', heroBgImage.files[0]);
    }

    sendAjaxRequest(
        "model/ajax.php?action=update_hero_section",
        formData,
        "edit_home_page.php"
    );
}

function validateCollectionSection() {
    const mensPretitle   = document.getElementById('coll-mens-pretitle');
    const mensTitle      = document.getElementById('coll-mens-title');
    const mensImage      = document.getElementById('coll-mens-image');
    const womensPretitle = document.getElementById('coll-womens-pretitle');
    const womensTitle    = document.getElementById('coll-womens-title');
    const womensImage    = document.getElementById('coll-womens-image');

    const hasExistingMensImage   = document.querySelector('img[alt="Men\'s Collection Image"]') !== null;
    const hasExistingWomensImage = document.querySelector('img[alt="Women\'s Collection Image"]') !== null;

    if (!checkEmptyInput(mensPretitle,   "Enter the men's pre-title")) return;
    if (!checkEmptyInput(mensTitle,      "Enter the men's title")) return;
    if (!hasExistingMensImage && mensImage.files.length === 0) {
        alert("Select a men's collection image");
        return;
    }

    if (!checkEmptyInput(womensPretitle, "Enter the women's pre-title")) return;
    if (!checkEmptyInput(womensTitle,    "Enter the women's title")) return;
    if (!hasExistingWomensImage && womensImage.files.length === 0) {
        alert("Select a women's collection image");
        return;
    }

    var formData = new FormData();
    formData.append('mens_pre_title',   mensPretitle.value);
    formData.append('mens_title',       mensTitle.value);
    formData.append('womens_pre_title', womensPretitle.value);
    formData.append('womens_title',     womensTitle.value);

    if (mensImage.files.length > 0)   formData.append('mens_image',   mensImage.files[0]);
    if (womensImage.files.length > 0) formData.append('womens_image', womensImage.files[0]);

    sendAjaxRequest(
        "model/ajax.php?action=update_collection_section",
        formData,
        "edit_home_page.php"
    );
}

function validateProductSection() {
    const prodPretitle   = document.getElementById('prod-pretitle');
    const prodTitle      = document.getElementById('prod-title');
    const prodButtonText = document.getElementById('prod-button-text');

    if (!checkEmptyInput(prodPretitle,   "Enter the pre-title")) return;
    if (!checkEmptyInput(prodTitle,      "Enter the title")) return;
    if (!checkEmptyInput(prodButtonText, "Enter the button text")) return;

    var formData = new FormData();
    formData.append('pre_title',   prodPretitle.value);
    formData.append('title',       prodTitle.value);
    formData.append('button_text', prodButtonText.value);

    sendAjaxRequest(
        "model/ajax.php?action=update_product_section",
        formData,
        "edit_home_page.php"
    );
}

function validatePhilosophySection() {
    const philPretitle   = document.getElementById('phil-pretitle');
    const philQuote      = document.getElementById('phil-quote');
    const philDesc       = document.getElementById('phil-description');
    const philButtonText = document.getElementById('phil-button-text');
    const philImage      = document.getElementById('phil-image');

    const hasExistingImage = document.querySelector('img[alt="Philosophy Image"]') !== null;

    if (!checkEmptyInput(philPretitle,   "Enter the pre-title")) return;
    if (!checkEmptyInput(philQuote,      "Enter the quote")) return;
    if (!checkEmptyInput(philDesc,       "Enter the description")) return;
    if (!checkEmptyInput(philButtonText, "Enter the button text")) return;
    if (!hasExistingImage && philImage.files.length === 0) {
        alert("Select a philosophy image");
        return;
    }

    var formData = new FormData();
    formData.append('pre_title',   philPretitle.value);
    formData.append('quote',       philQuote.value);
    formData.append('description', philDesc.value);
    formData.append('button_text', philButtonText.value);

    if (philImage.files.length > 0) formData.append('image', philImage.files[0]);

    sendAjaxRequest(
        "model/ajax.php?action=update_philosophy_section",
        formData,
        "edit_home_page.php"
    );
}

function validateAboutSection() {
    const aboutPretitle   = document.getElementById('about-pretitle');
    const aboutTitle      = document.getElementById('about-title');
    const aboutDesc       = document.getElementById('about-description');
    const aboutButtonText = document.getElementById('about-button-text');
    const aboutImage1     = document.getElementById('about-image-1');
    const aboutImage2     = document.getElementById('about-image-2');

    const hasExistingImage1 = document.querySelector('img[alt="About Image 1"]') !== null;
    const hasExistingImage2 = document.querySelector('img[alt="About Image 2"]') !== null;

    if (!checkEmptyInput(aboutPretitle,   "Enter the pre-title")) return;
    if (!checkEmptyInput(aboutTitle,      "Enter the title")) return;
    if (!checkEmptyInput(aboutDesc,       "Enter the description")) return;
    if (!checkEmptyInput(aboutButtonText, "Enter the button text")) return;
    if (!hasExistingImage1 && aboutImage1.files.length === 0) {
        alert("Select Image 1");
        return;
    }
    if (!hasExistingImage2 && aboutImage2.files.length === 0) {
        alert("Select Image 2");
        return;
    }

    var formData = new FormData();
    formData.append('pre_title',   aboutPretitle.value);
    formData.append('title',       aboutTitle.value);
    formData.append('description', aboutDesc.value);
    formData.append('button_text', aboutButtonText.value);

    if (aboutImage1.files.length > 0) formData.append('image_1', aboutImage1.files[0]);
    if (aboutImage2.files.length > 0) formData.append('image_2', aboutImage2.files[0]);

    sendAjaxRequest(
        "model/ajax.php?action=update_about_section",
        formData,
        "edit_home_page.php"
    );
}

function validateContactSection() {
    const contactPretitle = document.getElementById('contact-pretitle');
    const contactTitle    = document.getElementById('contact-title');
    const contactDesc     = document.getElementById('contact-description');
    const contactAddress  = document.getElementById('contact-address');
    const contactEmail    = document.getElementById('contact-email');
    const contactPhone    = document.getElementById('contact-phone');

    if (!checkEmptyInput(contactPretitle, "Enter the pre-title")) return;
    if (!checkEmptyInput(contactTitle,    "Enter the title")) return;
    if (!checkEmptyInput(contactDesc,     "Enter the description")) return;
    if (!checkEmptyInput(contactAddress,  "Enter the address")) return;
    if (!checkEmptyInput(contactEmail,    "Enter the email address")) return;
    if (!checkEmptyInput(contactPhone,    "Enter the contact number")) return;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(contactEmail.value.trim())) {
        alert("Enter a valid email address");
        contactEmail.focus();
        return;
    }

    var formData = new FormData();
    formData.append('pre_title',   contactPretitle.value);
    formData.append('title',       contactTitle.value);
    formData.append('description', contactDesc.value);
    formData.append('address',     contactAddress.value);
    formData.append('email',       contactEmail.value);
    formData.append('contact',     contactPhone.value);

    sendAjaxRequest(
        "model/ajax.php?action=update_contact_section",
        formData,
        "edit_home_page.php"
    );
}

// ===================== SALE EDIT PAGE FUNCTIONS =====================

// Recalculates discount amount, tax, and line total for a single edit-page row.
function recalcRowEdit(row) {
    var price           = parseFloat(row.find('.price').text())       || 0;
    var qty             = parseInt(row.find('.qty').text())            || 0;
    var discountPercent = parseFloat(row.attr('data-discount'))        || 0;

    var subtotal    = price * qty;
    var discountAmt = subtotal * (discountPercent / 100);
    var taxable     = subtotal - discountAmt;
    var tax         = taxable * 0.12;
    var lineTotal   = taxable + tax;

    row.find('.discount-amt').text(discountAmt.toFixed(2));
    row.find('.tax').text(tax.toFixed(2));
    row.find('.line-total').text(lineTotal.toFixed(2));

    calculateFinalBillEdit();
    recalculateChangeEdit();
}

// Apply discount button on the edit page.
$(document).on('click', '.apply-discount-edit', function () {
    var row   = $(this).closest('tr');
    var input = row.find('.discount-percent');
    var value = parseFloat(input.val());

    if (isNaN(value) || input.val().trim() === '') {
        alert('Enter a discount percentage');
        input.focus();
        return;
    }
    if (value < 1) { alert('Discount must be at least 1%'); input.focus(); return; }
    if (value > 25) { alert('Discount cannot exceed 25%'); input.focus(); return; }

    row.attr('data-discount', value);
    recalcRowEdit(row);
});

// Increase qty on the edit page.
$(document).on('click', '.increase-edit', function () {
    var row   = $(this).closest('tr');
    var qty   = parseInt(row.find('.qty').text());
    var stock = parseInt(row.data('stock')) || 9999;

    if (qty < stock) {
        row.find('.qty').text(qty + 1);
        recalcRowEdit(row);
    } else {
        alert('Cannot exceed available stock');
    }
});

// Decrease qty on the edit page.
$(document).on('click', '.decrease-edit', function () {
    var row = $(this).closest('tr');
    var qty = parseInt(row.find('.qty').text());

    if (qty > 1) {
        row.find('.qty').text(qty - 1);
        recalcRowEdit(row);
    }
});

// Remove row on the edit page.
$(document).on('click', '.remove-edit', function () {
    $(this).closest('tr').remove();
    renumberRowsEdit();
    calculateFinalBillEdit();
    recalculateChangeEdit();
});

// Recalculate change for the EDIT page (uses -edit suffixed IDs).
function recalculateChangeEditPage() {
    // edit-sale.php reuses the same #amount-received / #change-given IDs as pos.php
    recalculateChangeEdit();
}

// Print the bill from the current state of the edit table.
function printCurrentBill() {
    var saleId    = document.getElementById('sale-id') ? document.getElementById('sale-id').value : '-';
    var finalBill = parseFloat(document.getElementById('final-bill').textContent) || 0;
    var pmEl      = document.querySelector('input[name="payment-method"]:checked');
    var pm        = pmEl ? pmEl.value : '-';
    var amtEl     = document.getElementById('amount-received');
    var chgEl     = document.getElementById('change-given');
    var amountReceived = amtEl ? (parseFloat(amtEl.value) || 0) : finalBill;
    var changeGiven    = chgEl ? (parseFloat(chgEl.value) || 0) : 0;

    var items = [];
    document.querySelectorAll('#bill-table tbody tr').forEach(function (row) {
        items.push({
            product_name:    row.querySelector('.product-name').textContent.trim(),
            price:           parseFloat(row.querySelector('.price').textContent) || 0,
            qty:             parseInt(row.querySelector('.qty').textContent) || 0,
            discount_percent: parseFloat(row.getAttribute('data-discount')) || 0,
            discount_amount: parseFloat(row.querySelector('.discount-amt').textContent) || 0,
            tax:             parseFloat(row.querySelector('.tax').textContent) || 0,
            total:           parseFloat(row.querySelector('.line-total').textContent) || 0
        });
    });

    printBill({
        sale_id:         saleId,
        items:           items,
        final_bill:      finalBill,
        payment_method:  pm,
        amount_received: amountReceived,
        change_given:    changeGiven
    });
}