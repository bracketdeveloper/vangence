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
        // console.log(data)
        alert(data);
        if (redirectUrl && data == "Login successful.") {
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
        let product = data[0]; // get the first object from the array
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
        let lineCell = row.find(".line-total");
        let qty = parseInt(qtyCell.text()) + 1;
        // check stock limit
        if (qty > stockQty) {
            alert("Cannot add more than available stock");
            return;
        }
        qtyCell.text(qty);
        lineCell.text((qty * price));
    } else {
        let newRow = `
        <tr data-barcode="${barcode}" data-stock="${stockQty}">
            <td>${$("#cartTable tbody tr").length + 1}</td>
            <td class="product-id" hidden>${productId}</td>
            <td class="product-name">${productName}</td>
            <td class="price">${price}</td>
            <td class="qty">1</td>
            <td class="line-total">${price}</td>
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
        let lineTotal = parseInt($(this).find(".line-total").text());
        total += lineTotal;
    });
    $("#final-bill").text(total);
}

$(document).on("click", ".increase", function () {
    let row = $(this).closest("tr");
    let price = parseInt(row.find(".price").text());
    let qty = parseInt(row.find(".qty").text());
    let stock = parseInt(row.data("stock")); // available stock
    if (qty < stock) {
        qty++;
        row.find(".qty").text(qty);
        row.find(".line-total").text(qty * price);
        updateTotal();
    } else {
        alert("Cannot exceed available stock");
    }
});
$(document).on("click", ".decrease", function () {
    let row = $(this).closest("tr");
    let price = parseInt(row.find(".price").text());
    let qty = parseInt(row.find(".qty").text());
    if (qty > 1) {
        qty--;
        row.find(".qty").text(qty);
        row.find(".line-total").text((qty * price));
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
        alert("Enter at least 5 digits")
        barcodeEl.focus()
        return;
    }
    var barcode = barcodeEl.value;
    var formData = new FormData();
    if (barcode.length > 12) {
        barcode = barcode.substring(0, barcode.length - 1)
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

$('#save-bill').click(function () {
    let rows = getTableData();
    let finalBill = $('#final-bill').text();
    const productQtyMap = {};
    rows.forEach(item => {
        productQtyMap[item.product_id] = item.qty;
    });
    if (JSON.stringify(rows).length < 1) {
        alert("Nothing is in table");
        return;
    }
    if (!rows || rows.length === 0) {
        alert('No items in table');
        return;
    }
    var formData = new FormData();
    formData.append('rows', JSON.stringify(rows));
    formData.append('productQtyMap', JSON.stringify(productQtyMap));
    formData.append('final_bill', finalBill);
    sendAjaxRequest("model/ajax.php?action=save_bill", formData,);
});

function printBillHead() {
    return `<head>
      <title>Bill</title>
      <style>
      @media print {
        @page { 
          size: 80mm auto;  /* paper width fixed */
          margin: 5px;         /* remove default page margins */
        }
        body {
          margin: 5px;         /* remove body margin */
          padding: 0;        /* remove body padding */
        }
      }
      body {
        font-family: Arial, sans-serif;
        width: 80mm;
        margin: 0 auto;
        padding: 0;
      }
      .header, .footer {
        text-align: center;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0;       /* remove extra top margin */
      }
      th, td {
        border-bottom: 1px solid #000;
        padding: 4px;
        text-align: left;
        font-size: 12px;
      }
      .total-row td {
        font-weight: bold;
        border-top: 2px solid #000;
      }
      .footer{
      margin-bottom: 20px !important;
      }
    </style>
    </head>`
}

function printBill(items, finalBill) {
    let rows = "";
    items.forEach((item, i) => {
        rows += `
      <tr>
        <td>${i + 1}</td>
        <td>${item.product_name}</td>
        <td>${item.price}</td>
        <td>${item.qty}</td>
        <td>${item.total}</td>
      </tr>
    `;
    });
    var html = `
    <html>
    <body>
    ` + printBillHead() +
        `
      <div class="header">
        <h2>Mahna Vouge</h2>
        <div>Block B 48 Ext. Pak-Arab Housing Scheme Lahore Pakistan</div>
        <div>+92 317 4038 019</div>
      </div>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          ${rows}
          <tr class="total-row">
            <td colspan="4">Final Bill</td>
            <td>${finalBill}</td>
          </tr>
        </tbody>
      </table>
      <div class="footer">
        <div>Thank you for your purchase!</div>
        <div>Date: ${new Date().toLocaleString()}</div>
        <div>______________________</div>
      </div>
    </body>
    </html>
  `;
    const win = window.open("", "PRINT", "height=600,width=800");
    win.document.write(html);
    win.document.close();
    win.print();
    w.onafterprint = () => w.close();
}

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
    // Give it a split second to render the SVG before printing
    setTimeout(function() {
        printWindow.print();
    }, 500);
}

function printAnyLabel(items, finalBill) {
    const labelTextEl = document.getElementById('print-label-text');
    if (labelTextEl.value == "") {
        alert("Nothing to print")
        return;
    }
    const html = `
  <html>
    <head>
        <style>
          @page {
            size: 2in 1in;
            margin: 0;
          }
          body {
            font-family: Arial, sans-serif;
            width: 100%;
            margin: 0 auto;
            padding: 0;
          }
          .header, .footer {
            text-align: center;
          }
          .label {
            width: 2in;
            height: 1in;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 8px;
          }
        </style>
    </head>
    <body>
        <div class="label" style="font-size: small"><h1><b>${labelTextEl.value.toUpperCase()}</b></h1></div>
    </body>
    </html>
  `;
    const w = window.open("", "PRINT", "width=600,height=800");
    w.document.write(html);
    w.document.close();
    w.print();
    w.onafterprint = () => w.close();
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
