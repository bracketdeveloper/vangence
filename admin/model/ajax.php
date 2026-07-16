<?php
require_once "functions.php";
if (!isset($_SESSION)) {
    session_start();
}

// Ensure output is clean for all responses
function sendResponse($message) {
    ob_clean();
    echo $message;
}

if (isset($_GET['action']) && $_GET['action'] == 'add_new_category') {
    ob_clean();
    $category = mysqli_real_escape_string($conn, $_POST['new_category']);
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] != ''
        ? intval($_POST['parent_id'])
        : "NULL";
    $checkExistingCategory = "
    SELECT * FROM `categories` WHERE `category` = '$category' AND " . ($parent_id === "NULL" ? "parent_id IS NULL" : "parent_id = $parent_id");
    $checkExistingCategoryResult = mysqli_query($conn, $checkExistingCategory);
    if (mysqli_num_rows($checkExistingCategoryResult) > 0) {
        echo "Category already exists.";
    } else {
        $newCategoryInsertQuery = "INSERT INTO `categories` (`category`, `parent_id`)
        VALUES ('$category', $parent_id)";
        if ($conn->query($newCategoryInsertQuery) === TRUE) {
            echo "Category has been added successfully.";
        } else {
            echo "An error occurred while adding new category.";
        }
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'edit_category') {
    ob_clean();
    $category = mysqli_real_escape_string($conn, $_POST['edit_category']);
    $categoryId = mysqli_real_escape_string($conn, $_POST['category_id']);
    $parentId = mysqli_real_escape_string($conn, $_POST['parent_id']);
    $checkExistingCategory = "SELECT * FROM `categories` WHERE `category` = '$category' AND `category_id` != '$categoryId'";
    $checkExistingCategoryResult = mysqli_query($conn, $checkExistingCategory);
    if (mysqli_num_rows($checkExistingCategoryResult) > 0) {
        echo "Category already exists.";
    } else {
        if($parentId == ""){
            $parentId = "NULL";
        } else {
            $parentId = "'$parentId'";
        }
        $categoryUpdateQuery = "UPDATE `categories` 
                                SET `category` = '$category',
                                    `parent_id` = $parentId
                                WHERE `category_id` = '$categoryId'";
        if ($conn->query($categoryUpdateQuery) === TRUE) {
            echo "Category has been edited successfully.";
        } else {
            echo "An error occurred while editing category.";
        }
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete_category') {
    ob_clean();
    $categoryId = mysqli_real_escape_string($conn, $_POST['category_id']);
    $deleteQuery = "DELETE FROM `categories` WHERE  `category_id` = '$categoryId'";
    if ($conn->query($deleteQuery) === TRUE) {
        echo "Category has been deleted successfully.";
    } else {
        echo "An error occurred while deleting category.";
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'add_new_product') {
    ob_clean();
    $prefix = "PROD";
    $productId = $prefix . '_' . time();

    $productName = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $details     = mysqli_real_escape_string($conn, $_POST['details']);
    $sellingPrice = mysqli_real_escape_string($conn, $_POST['selling_price']);
    $qty          = mysqli_real_escape_string($conn, $_POST['qty']);
    $categoryId   = mysqli_real_escape_string($conn, $_POST['category_id']);

    $sizesArray  = explode(',', $_POST['sizes']);
    $colorsArray = explode(',', $_POST['colors']);
    $sizesJson   = json_encode(array_map('trim', $sizesArray));
    $colorsJson  = json_encode(array_map('trim', $colorsArray));

    $productBarcode = generateProductBarcode();

    $checkExistingProduct = "SELECT * FROM `products` WHERE `product_name` = '$productName' OR `barcode` = '$productBarcode'";
    $checkExistingProductResult = mysqli_query($conn, $checkExistingProduct);

    if (mysqli_num_rows($checkExistingProductResult) > 0) {
        echo "Product with same name already exists.";
    } else {
        $imageNames = [];
        $uploadDir = "../uploads/";
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                $fileName = $productId . '_' . basename($_FILES['images']['name'][$key]);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $imageNames[] = $fileName;
                }
            }
        }
        $imageJson = json_encode($imageNames);

        $newProductInsertQuery = "INSERT INTO `products` 
            (`product_id`, `product_name`, `description`, `details`, `selling_price`, `image`, `qty`, `barcode`, `category_id`, `sizes`, `colors`) 
            VALUES 
            ('$productId', '$productName', '$description', '$details', '$sellingPrice', '$imageJson', '$qty', '$productBarcode', '$categoryId', '$sizesJson', '$colorsJson')";

        if ($conn->query($newProductInsertQuery) === TRUE) {
            $productDetails = getAllProducts($conn);
            $images = [];
            foreach ($productDetails as $product) {
                $decoded = json_decode($product['image'], true);
                if (is_array($decoded)) {
                    $images = array_merge($images, $decoded);
                }
            }
            deleteUnusedProductImages("../uploads/", $images);
            echo "Product has been added successfully.";
            generateBarcodeImage($productBarcode);
        } else {
            echo "An error occurred while adding the product: " . $conn->error;
        }
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'edit_product') {
    ob_clean();
    $productId = mysqli_real_escape_string($conn, $_POST['edit_product_id']);
    $productName = mysqli_real_escape_string($conn, $_POST['edit_product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['edit_description']);
    $details = mysqli_real_escape_string($conn, $_POST['edit_details']);
    $sellingPrice = mysqli_real_escape_string($conn, $_POST['edit_selling_price']);
    $qty = mysqli_real_escape_string($conn, $_POST['edit_qty']);
    $categoryId = mysqli_real_escape_string($conn, $_POST['edit_category_id']);

    $sizes = json_encode(array_map('trim', explode(',', $_POST['edit_sizes'])));
    $colors = json_encode(array_map('trim', explode(',', $_POST['edit_colors'])));

    $existingImagesJson = isset($_POST['existing_images']) ? $_POST['existing_images'] : '[]';
    $existingImages = json_decode($existingImagesJson, true);

    $existingProduct = getProductByName($conn, $productName);

    if (!empty($existingProduct) && $existingProduct[0]['product_id'] !== $productId) {
        echo "Product with same name already exists.";
    } else {
        $imageNames = [];
        $uploadDir = "../uploads/";
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                $fileName = time() . '_' . basename($_FILES['images']['name'][$key]);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $imageNames[] = $fileName;
                }
            }
        }

        $finalImages = array_unique(array_merge($existingImages, $imageNames));
        $finalImageJson = json_encode(array_values($finalImages));

        $productUpdateQuery = "UPDATE `products` SET 
                               `product_name` = '$productName', 
                               `description` = '$description',
                               `details` = '$details',
                               `selling_price` = '$sellingPrice', 
                               `qty` = '$qty', 
                               `category_id` = '$categoryId', 
                               `sizes` = '$sizes', 
                               `colors` = '$colors', 
                               `image` = '$finalImageJson' 
                               WHERE `product_id` = '$productId'";

        if ($conn->query($productUpdateQuery) === TRUE) {
            echo "Product has been edited successfully.";
        } else {
            echo "An error occurred while editing product.";
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete_product') {
    ob_clean();
    $productId = mysqli_real_escape_string($conn, $_POST['product_id']);
    $deleteQuery = "DELETE FROM `products` WHERE  `product_id` = '$productId'";
    if ($conn->query($deleteQuery) === TRUE) {
        getProductImagesToDelete($conn);
        echo "Product has been deleted successfully.";
    } else {
        echo "An error occurred while deleting product.";
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'add_to_bill') {
    ob_clean();
    header('Content-Type: application/json');
    $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
    $getProductQuery = "SELECT * FROM `products` WHERE `barcode` LIKE '%$barcode'";
    $product = runSelectQuery($conn, $getProductQuery);
    echo json_encode($product);
}
if (isset($_GET['action']) && $_GET['action'] == 'save_bill') {
    ob_clean();
    $productQtyMap = json_decode($_POST['productQtyMap'], true);
    $rows = json_decode($_POST['rows'], true);
    $rows = mysqli_real_escape_string($conn, json_encode($rows));
    $finalBill = mysqli_real_escape_string($conn, $_POST['final_bill']);
    $userName = $_SESSION['name'];
    $conn->begin_transaction();
    try {
        $newCategoryInsertQuery = "INSERT INTO `sales`(`items`, `final_bill`, `created_by`) 
                               VALUES ('$rows', '$finalBill', '$userName')";
        if (!$conn->query($newCategoryInsertQuery)) {
            throw new Exception("Error saving bill: " . $conn->error);
        }
        foreach ($productQtyMap as $product_id => $qty) {
            $product_id = mysqli_real_escape_string($conn, $product_id);
            $qty = (int)$qty;
            $productUpdateQuery = "UPDATE `products` 
                               SET `qty` = GREATEST(`qty` - $qty, 0) 
                               WHERE `product_id` = '$product_id'";
            if (!$conn->query($productUpdateQuery)) {
                throw new Exception("Error updating product: $product_id - " . $conn->error);
            }
        }
        $conn->commit();
        echo "Bill has been saved successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Some error occurred while saving bill.";
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'print_bill') {
    ob_clean();
    $rows = json_decode($_POST['rows'], true);
    $rows = mysqli_real_escape_string($conn, json_encode($rows));
    $finalBill = mysqli_real_escape_string($conn, $_POST['final_bill']);
    $userName = $_SESSION['name'];
    $newCategoryInsertQuery = "INSERT INTO `sales`(`items`, `final_bill`, `created_by`) 
                               VALUES ('$rows', '$finalBill', '$userName')";
    if ($conn->query($newCategoryInsertQuery) === TRUE) {
        echo "Bill has been save successfully.";
        echo " and now print bill";
    } else {
        echo "An error occurred while saving and printing bill.";
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'add_new_user') {
    ob_clean();
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $name = ucwords(strtolower($name));
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $password = password_hash($password, PASSWORD_BCRYPT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $checkExistingUser = "SELECT * FROM `users` WHERE `email` = '$email'";
    $checkExistingUserResult = mysqli_query($conn, $checkExistingUser);
    if (mysqli_num_rows($checkExistingUserResult) > 0) {
        echo "User already exists.";
    } else {
        $newUserInsertQuery = "INSERT INTO `users`(`user_name`, `email`, `password`, `role`) VALUES
                               ('$name', '$email', '$password', '$role')";
        if ($conn->query($newUserInsertQuery) === TRUE) {
            echo "User has been added successfully.";
        } else {
            echo "An error occurred while adding new user.";
        }
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'login_user') {
    ob_clean();
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $loginQuery = "SELECT * FROM `users` WHERE `email` = '$email'";
    $loginQueryResult = mysqli_query($conn, $loginQuery);
    if (mysqli_num_rows($loginQueryResult) === 1) {
        $row = mysqli_fetch_array($loginQueryResult, MYSQLI_ASSOC);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = 'True';
            $_SESSION["user_id"] = $row['user_id'];
            $_SESSION["email"] = $row['email'];
            $_SESSION["name"] = $row['user_name'];
            $_SESSION["role"] = $row['role'];
            $_SESSION["password"] = $password;
            echo "Login successful.";
        } else {
            echo "Incorrect login details.";
        }
    } else {
        echo "User not found.";
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'logout_user') {
    ob_clean();
    session_destroy();
    echo "Logged out successfully.";
}
if (isset($_GET['action']) && $_GET['action'] == 'change_password') {
    ob_clean();
    $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
    $currentPassword = mysqli_real_escape_string($conn, $_POST['current_password']);
    $userId = $_SESSION["user_id"];
    if ($currentPassword !== $_SESSION['password']) {
        echo "Current password is incorrect.";
        exit();
    }
    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $productUpdateQuery = "UPDATE `users` SET `password` = '$newPasswordHash' WHERE
                        `user_id` = '$userId'";
    if ($conn->query($productUpdateQuery) === TRUE) {
        $_SESSION["password"] = $newPassword;
        echo "Password has been changed successfully.";
    } else {
        echo "An error occurred while changing password.";
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'get_user_data_by_id') {
    ob_clean();
    $userId = mysqli_real_escape_string($conn, $_POST['user_id']);
    $specificUser = getSpecificUserById($conn, $userId);
    echo json_encode($specificUser);
}
// ===================== UPDATE HERO SECTION =====================
if (isset($_GET['action']) && $_GET['action'] == 'update_hero_section') {
    ob_clean();

    $checkQuery = "SELECT id, content_data FROM `site_content` WHERE `page_identifier` = 'home' AND `section_identifier` = 'hero_banner'";
    $result = mysqli_query($conn, $checkQuery);

    $existing_bg_image = "";
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $existing_data = json_decode($row['content_data'], true);
        $existing_bg_image = isset($existing_data['bg_image']) ? $existing_data['bg_image'] : "";
    }

    $bg_image_path = $existing_bg_image;
    if (isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] == 0) {
        $full_upload_path = "../img/" . basename($_FILES['bg_image']['name']);
        if (move_uploaded_file($_FILES['bg_image']['tmp_name'], $full_upload_path)) {
            $bg_image_path = "img/" . basename($_FILES['bg_image']['name']);
        }
    }

    $content_data = json_encode([
        'pre_title'   => $_POST['pre_title'],
        'title'       => $_POST['title'],
        'description' => $_POST['description'],
        'button_text' => $_POST['button_text'],
        'bg_image'    => $bg_image_path
    ]);

    if (mysqli_num_rows($result) > 0) {
        $updateQuery = "UPDATE `site_content` SET `content_data` = '" . mysqli_real_escape_string($conn, $content_data) . "' WHERE `page_identifier` = 'home' AND `section_identifier` = 'hero_banner'";
        echo ($conn->query($updateQuery) === TRUE) ? "Hero section updated successfully." : "An error occurred while updating the hero section.";
    } else {
        $insertQuery = "INSERT INTO `site_content` (`page_identifier`, `section_identifier`, `content_data`) VALUES ('home', 'hero_banner', '" . mysqli_real_escape_string($conn, $content_data) . "')";
        echo ($conn->query($insertQuery) === TRUE) ? "Hero section added successfully." : "An error occurred while adding the hero section.";
    }
}


// ===================== UPDATE COLLECTION SECTION =====================
if (isset($_GET['action']) && $_GET['action'] == 'update_collection_section') {
    ob_clean();

    $checkQuery = "SELECT id, content_data FROM `site_content` WHERE `page_identifier` = 'home' AND `section_identifier` = 'collection_section'";
    $result = mysqli_query($conn, $checkQuery);

    $existing_mens_image = "";
    $existing_womens_image = "";
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $existing_data = json_decode($row['content_data'], true);
        $existing_mens_image   = isset($existing_data['mens_image'])   ? $existing_data['mens_image']   : "";
        $existing_womens_image = isset($existing_data['womens_image']) ? $existing_data['womens_image'] : "";
    }

    $mens_image_path   = $existing_mens_image;
    $womens_image_path = $existing_womens_image;

    if (isset($_FILES['mens_image']) && $_FILES['mens_image']['error'] == 0) {
        $full_path = "../img/" . basename($_FILES['mens_image']['name']);
        if (move_uploaded_file($_FILES['mens_image']['tmp_name'], $full_path)) {
            $mens_image_path = "img/" . basename($_FILES['mens_image']['name']);
        }
    }

    if (isset($_FILES['womens_image']) && $_FILES['womens_image']['error'] == 0) {
        $full_path = "../img/" . basename($_FILES['womens_image']['name']);
        if (move_uploaded_file($_FILES['womens_image']['tmp_name'], $full_path)) {
            $womens_image_path = "img/" . basename($_FILES['womens_image']['name']);
        }
    }

    $content_data = json_encode([
        'mens_pre_title'   => $_POST['mens_pre_title'],
        'mens_title'       => $_POST['mens_title'],
        'mens_image'       => $mens_image_path,
        'womens_pre_title' => $_POST['womens_pre_title'],
        'womens_title'     => $_POST['womens_title'],
        'womens_image'     => $womens_image_path
    ]);

    if (mysqli_num_rows($result) > 0) {
        $updateQuery = "UPDATE `site_content` SET `content_data` = '" . mysqli_real_escape_string($conn, $content_data) . "' WHERE `page_identifier` = 'home' AND `section_identifier` = 'collection_section'";
        echo ($conn->query($updateQuery) === TRUE) ? "Collection section updated successfully." : "An error occurred while updating the collection section.";
    } else {
        $insertQuery = "INSERT INTO `site_content` (`page_identifier`, `section_identifier`, `content_data`) VALUES ('home', 'collection_section', '" . mysqli_real_escape_string($conn, $content_data) . "')";
        echo ($conn->query($insertQuery) === TRUE) ? "Collection section added successfully." : "An error occurred while adding the collection section.";
    }
}


// ===================== UPDATE PRODUCT SECTION =====================
if (isset($_GET['action']) && $_GET['action'] == 'update_product_section') {
    ob_clean();

    $checkQuery = "SELECT id, content_data FROM `site_content` WHERE `page_identifier` = 'home' AND `section_identifier` = 'product_section'";
    $result = mysqli_query($conn, $checkQuery);

    $content_data = json_encode([
        'pre_title'   => $_POST['pre_title'],
        'title'       => $_POST['title'],
        'button_text' => $_POST['button_text']
    ]);

    if (mysqli_num_rows($result) > 0) {
        $updateQuery = "UPDATE `site_content` SET `content_data` = '" . mysqli_real_escape_string($conn, $content_data) . "' WHERE `page_identifier` = 'home' AND `section_identifier` = 'product_section'";
        echo ($conn->query($updateQuery) === TRUE) ? "Product section updated successfully." : "An error occurred while updating the product section.";
    } else {
        $insertQuery = "INSERT INTO `site_content` (`page_identifier`, `section_identifier`, `content_data`) VALUES ('home', 'product_section', '" . mysqli_real_escape_string($conn, $content_data) . "')";
        echo ($conn->query($insertQuery) === TRUE) ? "Product section added successfully." : "An error occurred while adding the product section.";
    }
}


// ===================== UPDATE PHILOSOPHY SECTION =====================
if (isset($_GET['action']) && $_GET['action'] == 'update_philosophy_section') {
    ob_clean();

    $checkQuery = "SELECT id, content_data FROM `site_content` WHERE `page_identifier` = 'home' AND `section_identifier` = 'philosophy_section'";
    $result = mysqli_query($conn, $checkQuery);

    $existing_image = "";
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $existing_data  = json_decode($row['content_data'], true);
        $existing_image = isset($existing_data['image']) ? $existing_data['image'] : "";
    }

    $image_path = $existing_image;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $full_path = "../img/" . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $full_path)) {
            $image_path = "img/" . basename($_FILES['image']['name']);
        }
    }

    $content_data = json_encode([
        'pre_title'   => $_POST['pre_title'],
        'quote'       => $_POST['quote'],
        'description' => $_POST['description'],
        'button_text' => $_POST['button_text'],
        'image'       => $image_path
    ]);

    if (mysqli_num_rows($result) > 0) {
        $updateQuery = "UPDATE `site_content` SET `content_data` = '" . mysqli_real_escape_string($conn, $content_data) . "' WHERE `page_identifier` = 'home' AND `section_identifier` = 'philosophy_section'";
        echo ($conn->query($updateQuery) === TRUE) ? "Philosophy section updated successfully." : "An error occurred while updating the philosophy section.";
    } else {
        $insertQuery = "INSERT INTO `site_content` (`page_identifier`, `section_identifier`, `content_data`) VALUES ('home', 'philosophy_section', '" . mysqli_real_escape_string($conn, $content_data) . "')";
        echo ($conn->query($insertQuery) === TRUE) ? "Philosophy section added successfully." : "An error occurred while adding the philosophy section.";
    }
}


// ===================== UPDATE ABOUT SECTION =====================
if (isset($_GET['action']) && $_GET['action'] == 'update_about_section') {
    ob_clean();

    $checkQuery = "SELECT id, content_data FROM `site_content` WHERE `page_identifier` = 'home' AND `section_identifier` = 'about_section'";
    $result = mysqli_query($conn, $checkQuery);

    $existing_image_1 = "";
    $existing_image_2 = "";
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $existing_data    = json_decode($row['content_data'], true);
        $existing_image_1 = isset($existing_data['image_1']) ? $existing_data['image_1'] : "";
        $existing_image_2 = isset($existing_data['image_2']) ? $existing_data['image_2'] : "";
    }

    $image_1_path = $existing_image_1;
    $image_2_path = $existing_image_2;

    if (isset($_FILES['image_1']) && $_FILES['image_1']['error'] == 0) {
        $full_path = "../img/" . basename($_FILES['image_1']['name']);
        if (move_uploaded_file($_FILES['image_1']['tmp_name'], $full_path)) {
            $image_1_path = "img/" . basename($_FILES['image_1']['name']);
        }
    }

    if (isset($_FILES['image_2']) && $_FILES['image_2']['error'] == 0) {
        $full_path = "../img/" . basename($_FILES['image_2']['name']);
        if (move_uploaded_file($_FILES['image_2']['tmp_name'], $full_path)) {
            $image_2_path = "img/" . basename($_FILES['image_2']['name']);
        }
    }

    $content_data = json_encode([
        'pre_title'   => $_POST['pre_title'],
        'title'       => $_POST['title'],
        'description' => $_POST['description'],
        'button_text' => $_POST['button_text'],
        'image_1'     => $image_1_path,
        'image_2'     => $image_2_path
    ]);

    if (mysqli_num_rows($result) > 0) {
        $updateQuery = "UPDATE `site_content` SET `content_data` = '" . mysqli_real_escape_string($conn, $content_data) . "' WHERE `page_identifier` = 'home' AND `section_identifier` = 'about_section'";
        echo ($conn->query($updateQuery) === TRUE) ? "About section updated successfully." : "An error occurred while updating the about section.";
    } else {
        $insertQuery = "INSERT INTO `site_content` (`page_identifier`, `section_identifier`, `content_data`) VALUES ('home', 'about_section', '" . mysqli_real_escape_string($conn, $content_data) . "')";
        echo ($conn->query($insertQuery) === TRUE) ? "About section added successfully." : "An error occurred while adding the about section.";
    }
}


// ===================== UPDATE CONTACT SECTION =====================
if (isset($_GET['action']) && $_GET['action'] == 'update_contact_section') {
    ob_clean();

    $checkQuery = "SELECT id, content_data FROM `site_content` WHERE `page_identifier` = 'home' AND `section_identifier` = 'contact_section'";
    $result = mysqli_query($conn, $checkQuery);

    $content_data = json_encode([
        'pre_title'   => $_POST['pre_title'],
        'title'       => $_POST['title'],
        'description' => $_POST['description'],
        'address'     => $_POST['address'],
        'email'       => $_POST['email'],
        'contact'     => $_POST['contact']
    ]);

    if (mysqli_num_rows($result) > 0) {
        $updateQuery = "UPDATE `site_content` SET `content_data` = '" . mysqli_real_escape_string($conn, $content_data) . "' WHERE `page_identifier` = 'home' AND `section_identifier` = 'contact_section'";
        echo ($conn->query($updateQuery) === TRUE) ? "Contact section updated successfully." : "An error occurred while updating the contact section.";
    } else {
        $insertQuery = "INSERT INTO `site_content` (`page_identifier`, `section_identifier`, `content_data`) VALUES ('home', 'contact_section', '" . mysqli_real_escape_string($conn, $content_data) . "')";
        echo ($conn->query($insertQuery) === TRUE) ? "Contact section added successfully." : "An error occurred while adding the contact section.";
    }
}