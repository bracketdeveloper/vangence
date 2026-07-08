<?php
require __DIR__ . '/vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorSVG;

require_once "db_connections.php";

function getAllCategories($conn)
{
    $query = "
        SELECT 
            c.*,
            p.category AS parent_name
        FROM categories c
        LEFT JOIN categories p 
            ON c.parent_id = p.category_id
    ";

    return runSelectQuery($conn, $query);
}
function getCategoryById($conn, $categoryId)
{
    $query = "
        SELECT 
            c.*,
            p.category AS parent_name
        FROM categories c
        LEFT JOIN categories p 
            ON c.parent_id = p.category_id
        WHERE c.category_id = '$categoryId'
    ";

    return runSelectQuery($conn, $query);
}

function getAllProducts($conn)
{
  $query = "SELECT * FROM `products` Order by `created_at` DESC";
  return runSelectQuery($conn, $query);
}
function getProductById($conn, $productId)
{
  $query = "SELECT * FROM `products` WHERE `product_id` = '$productId'";
  return runSelectQuery($conn, $query);
}
function getProductByName($conn, $productName)
{
  $query = "SELECT * FROM `products` WHERE `product_name` = '$productName'";
  return runSelectQuery($conn, $query);
}

function getAllUsers($conn)
{
  $query = "SELECT * FROM `users`";
  return runSelectQuery($conn, $query);
}

function runSelectQuery($conn, $query)
{
    $result = mysqli_query($conn, $query);

    if(!$result){
        return [];
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}
function deleteUnusedProductImages($folder, $dbImages) {
  $allImages = glob($folder . "*.{jpg,jpeg,png,webp,gif}", GLOB_BRACE);

  foreach ($allImages as $filePath) {
    $fileName = basename($filePath);
    if (!in_array($fileName, $dbImages)) {
      unlink($filePath);
    }
  }
}
function getProductImagesToDelete($conn){
  $productDetails = getAllProducts($conn);
  $images = array();
  foreach ($productDetails as $product) {
    $decoded = json_decode($product['image'], true);
    if (is_array($decoded)) {
      $images = array_merge($images, $decoded);
    }
  }
  deleteUnusedProductImages("../uploads/", $images);
}

function showAlret($message)
{
  echo "<script>alert('$message');</script>";
}
function getSpecificUserById($conn, $userId)
{
  $query = "SELECT * FROM `users` WHERE `user_id` = '$userId'";
  $result = mysqli_query($conn, $query);
  echo mysqli_error($conn);
  $data = array();
  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    {
      $data[] = $row;
    }
  }
  return $data;
}

function getAllSales($conn)
{
    $query = "SELECT * FROM `sales` ORDER BY `sale_id` DESC ";
    return runSelectQuery($conn, $query);
}
function getSaleById($conn, $saleId)
{
    $query = "SELECT * FROM `sales` WHERE `sale_id` = '$saleId'";
    return runSelectQuery($conn, $query);
}

function generateProductBarcode()
{
    // 12 digits for EAN13 (last digit becomes checksum)
    return str_pad(rand(0, 999999999999), 12, "0", STR_PAD_LEFT);
}
function generateBarcodeImage($barcodeNumber){
    require 'vendor/autoload.php';

    $generator = new BarcodeGeneratorSVG();
    $barcode = $generator->getBarcode($barcodeNumber, $generator::TYPE_EAN_13, true);
    $path = "../barcodes/".$barcodeNumber.".svg";

    file_put_contents($path, $barcode);
}


