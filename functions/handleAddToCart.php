<!-- handle add to cart funct start -->
<?php
session_start();
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

// Get POST data
$product_id = isset($_POST['productId']) ? $_POST['productId'] : null;
$size = isset($_POST['size']) ? $_POST['size'] : null;
$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : null;
$user_id = $_SESSION['user_id'];

// Validate inputs
if (!$product_id || !$quantity) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    exit();
}

// Sanitize inputs
$product_id = mysqli_real_escape_string($conn, $product_id);
$quantity = intval($quantity);

$categoryQuery = "SELECT category_id, product_price FROM products WHERE product_id = '$product_id'";
$categoryResult = mysqli_query($conn, $categoryQuery);

if (!$categoryResult || mysqli_num_rows($categoryResult) == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit();
}

$categoryRow = mysqli_fetch_assoc($categoryResult);
$category_id = $categoryRow['category_id'];
$product_price = $categoryRow['product_price']; // Get price from `products` table

if (in_array($category_id, [4, 5, 6]) && empty($size)) {
    $size = "S";
}

// Sanitize size input
$size = mysqli_real_escape_string($conn, $size);

// ✅ Fetch the correct price
if (in_array($category_id, [4, 5, 6])) {
    $price = $product_price;  // Use price from `products` table
} else {
    // Get price from `product_variants` for other categories
    $priceQuery = "SELECT price FROM product_variants WHERE product_id = '$product_id' AND size = '$size'";
    $priceResult = mysqli_query($conn, $priceQuery);

    if (!$priceResult || mysqli_num_rows($priceResult) == 0) {
        echo json_encode(['success' => false, 'message' => 'Price not found for selected size.']);
        exit();
    }

    $row = mysqli_fetch_assoc($priceResult);
    $price = $row['price'];  // Store price from database
}

$checkCartQuery = "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id' AND size = '$size'";
$checkResult = mysqli_query($conn, $checkCartQuery);

if (!$checkResult) {
    echo json_encode(['success' => false, 'message' => 'SQL Error: ' . mysqli_error($conn)]);
    exit();
}

if (mysqli_num_rows($checkResult) > 0) {
    $updateQuery = "UPDATE cart SET quantity = quantity + $quantity, price = '$price' WHERE user_id = '$user_id' AND product_id = '$product_id' AND size = '$size'";
    if (mysqli_query($conn, $updateQuery)) {
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating cart: ' . mysqli_error($conn)]);
    }
} else {
    $added_at = date('Y-m-d H:i:s');
    $insertQuery = "INSERT INTO cart (user_id, product_id, quantity, size, price, added_at) VALUES ('$user_id', '$product_id', '$quantity', '$size', '$price', '$added_at')";

    if (mysqli_query($conn, $insertQuery)) {
        echo json_encode(['success' => true, 'message' => 'Item added to cart successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding item to cart: ' . mysqli_error($conn)]);
    }
}

// Close DB connection
mysqli_close($conn);
//handle add to cart funct end