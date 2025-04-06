<?php
include '../config/conn.php';

if (!isset($_GET['productId']) || !isset($_GET['size'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID or size missing.']);
    exit();
}

$productId = intval($_GET['productId']);
$size = $_GET['size'];

$query = "SELECT size, price FROM product_variants WHERE product_id = ? AND size = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "is", $productId, $size);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'size' => $row['size'],
        'price' => $row['price']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => '60ml variant not found.']);
}
