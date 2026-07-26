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
        let taxCell = row.find(".tax");
        let lineCell = row.find(".line-total");
        let qty = parseInt(qtyCell.text()) + 1;

        if (qty > stockQty) {
            alert("Cannot add more than available stock");
            return;
        }

        qtyCell.text(qty);

        let subtotal = price * qty;
        let tax = subtotal * 0.12;
        let lineTotal = subtotal + tax;

        taxCell.text(tax.toFixed(2));
        lineCell.text(lineTotal.toFixed(2));
    } else {
        let subtotal = price * 1;
        let tax = subtotal * 0.12;
        let lineTotal = subtotal + tax;

        let newRow = `
        <tr data-barcode="${barcode}" data-stock="${stockQty}">
            <td>${$("#bill-table tbody tr").length + 1}</td>
            <td class="product-id" hidden>${productId}</td>
            <td class="product-name">${productName}</td>
            <td class="price">${price}</td>
            <td class="qty">1</td>
            <td class="tax">${tax.toFixed(2)}</td>
            <td class="line-total">${lineTotal.toFixed(2)}</td>
            <td>
                <button class="increase">+</button>
                <button class="decrease">-</button>
                <button class="remove">Remove</button>
            </td>
        </tr>
        `;
        table.append(newRow);
    }
    updateTotal();
}

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
    let price = parseFloat(row.find(".price").text());
    let qty = parseInt(row.find(".qty").text());
    let stock = parseInt(row.data("stock"));

    if (qty < stock) {
        qty++;
        row.find(".qty").text(qty);

        let subtotal = price * qty;
        let tax = subtotal * 0.12;
        let lineTotal = subtotal + tax;

        row.find(".tax").text(tax.toFixed(2));
        row.find(".line-total").text(lineTotal.toFixed(2));

        updateTotal();
    } else {
        alert("Cannot exceed available stock");
    }
});

$(document).on("click", ".decrease", function () {
    let row = $(this).closest("tr");
    let price = parseFloat(row.find(".price").text());
    let qty = parseInt(row.find(".qty").text());

    if (qty > 1) {
        qty--;
        row.find(".qty").text(qty);

        let subtotal = price * qty;
        let tax = subtotal * 0.12;
        let lineTotal = subtotal + tax;

        row.find(".tax").text(tax.toFixed(2));
        row.find(".line-total").text(lineTotal.toFixed(2));
    }
    updateTotal();
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

function getTableData() {
    let items = [];
    $('#bill-table tbody tr').each(function () {
        items.push({
            product_name: $(this).find('.product-name').text(),
            product_id: $(this).find('.product-id').text(),
            price: parseFloat($(this).find('.price').text()),
            qty: parseInt($(this).find('.qty').text()),
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
    let win = window.open('', '_blank', 'width=800,height=1000');
    win.document.write('<html><head><title>Bill #' + data.sale_id + '</title>');
    win.document.write('<style>body{font-family:monospace;font-size:12px;} table{width:100%;} th,td{padding:2px;text-align:left;} .right{text-align:right;}</style>');
    win.document.write('</head><body>');
    win.document.write('<h3>Receipt</h3>');
    win.document.write('<p>Bill #: ' + data.sale_id + '</p>');
    win.document.write('<hr>');
    win.document.write('<table><thead><tr><th>Item</th><th class="right">Price</th><th class="right">Qty</th><th class="right">Tax</th><th class="right">Total</th></tr></thead><tbody>');
    let items = data.items || [];
    let html = '';
    items.forEach(function (r) {
        html += '<tr><td>' + r.product_name + '</td><td class="right">' + r.price + '</td><td class="right">' + r.qty + '</td><td class="right">' + (r.tax || '0.00') + '</td><td class="right">' + r.total + '</td></tr>';
    });
    win.document.write(html);
    win.document.write('</tbody></table><hr>');
    win.document.write('<p class="right">Total: ' + (data.final_bill || '-') + '</p>');
    win.document.write('<p class="right">Payment: ' + (data.payment_method || '-') + '</p>');
    win.document.write('<p class="right">Received: ' + (data.amount_received || '-') + '</p>');
    win.document.write('<p class="right">Change: ' + (data.change_given || '-') + '</p>');
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
    const barcodeInput = document.getElementById('barcode');
    const barcode = barcodeInput.value.trim();
    if (!barcode || barcode.length < 5) {
        alert('Please enter at least 5 digits.');
        return;
    }
    addProductEdit(barcode);
    barcodeInput.value = '';
};

window.addProductEdit = function (barcode) {
    fetch('model/ajax.php?action=get_product&barcode=' + encodeURIComponent(barcode))
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data || !data.success) {
                alert(data && data.message ? data.message : 'Product not found.');
                return;
            }
            const tbody = document.querySelector('#bill-table tbody');
            let exists = false;
            tbody.querySelectorAll('tr').forEach(function (row) {
                const pidEl = row.querySelector('.product-id');
                if (pidEl && pidEl.textContent.trim() == data.product_id) {
                    const qtyEl = row.querySelector('.qty');
                    const taxEl = row.querySelector('.tax');
                    const lineTotalEl = row.querySelector('.line-total');
                    const priceEl = row.querySelector('.price');

                    let qty = (parseFloat(qtyEl.textContent) || 0) + 1;
                    qtyEl.textContent = qty;

                    const price = parseFloat(priceEl.textContent) || 0;
                    const subtotal = price * qty;
                    const tax = subtotal * 0.12;
                    const lineTotal = subtotal + tax;

                    taxEl.textContent = tax.toFixed(2);
                    lineTotalEl.textContent = lineTotal.toFixed(2);

                    exists = true;
                }
            });
            if (!exists) {
                const rowCount = tbody.querySelectorAll('tr').length;
                const price = parseFloat(data.price) || 0;
                const subtotal = price * 1;
                const tax = subtotal * 0.12;
                const lineTotal = subtotal + tax;

                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td class="row-num">' + (rowCount + 1) + '</td>' +
                    '<td hidden class="product-id">' + data.product_id + '</td>' +
                    '<td class="product-name">' + data.product_name + '</td>' +
                    '<td class="price">' + data.price + '</td>' +
                    '<td class="qty">1</td>' +
                    '<td class="tax">' + tax.toFixed(2) + '</td>' +
                    '<td class="line-total">' + lineTotal.toFixed(2) + '</td>' +
                    '<td><button class="btn btn-danger btn-sm" onclick="removeRowEdit(this)">Remove</button></td>';
                tbody.appendChild(tr);
            }
            calculateFinalBillEdit();
            recalculateChangeEdit();
        })
        .catch(function (err) {
            alert('Error fetching product: No Product Found');
        });
};

window.saveSaleEdit = function () {
    const saleId = document.getElementById('sale-id').value;
    const rows = document.querySelectorAll('#bill-table tbody tr');
    if (rows.length === 0) {
        alert('Cannot update an empty bill.');
        return;
    }

    const items = [];
    const productQtyMap = {};
    rows.forEach(function (row) {
        const productId = row.querySelector('.product-id').textContent.trim();
        const productName = row.querySelector('.product-name').textContent.trim();
        const price = parseFloat(row.querySelector('.price').textContent) || 0;
        const qty = parseFloat(row.querySelector('.qty').textContent) || 0;
        const tax = parseFloat(row.querySelector('.tax').textContent) || 0;
        const total = parseFloat(row.querySelector('.line-total').textContent) || 0;
        items.push({
            product_id: productId,
            product_name: productName,
            price: price,
            qty: qty,
            tax: tax,
            total: total
        });
        productQtyMap[productId] = qty;
    });

    const finalBill = parseFloat(document.getElementById('final-bill').textContent) || 0;
    const paymentMethodEl = document.querySelector('input[name="payment-method"]:checked');
    const paymentMethod = paymentMethodEl ? paymentMethodEl.value : 'cash';
    const amountReceivedInput = document.getElementById('amount-received');
    const amountReceived = amountReceivedInput ? (parseFloat(amountReceivedInput.value) || 0) : 0;
    const changeGivenInput = document.getElementById('change-given');
    const changeGiven = changeGivenInput ? (parseFloat(changeGivenInput.value) || 0) : 0;

    const formData = new FormData();
    formData.append('sale_id', saleId);
    formData.append('rows', JSON.stringify(items));
    formData.append('productQtyMap', JSON.stringify(productQtyMap));
    formData.append('final_bill', finalBill);
    formData.append('payment_method', paymentMethod);
    formData.append('amount_received', amountReceived);
    formData.append('change_given', changeGiven);

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
            console.log(err.message)
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