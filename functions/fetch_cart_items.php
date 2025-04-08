<!-- fetch cart func start -->
<?php
session_start();
include '../config/conn.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['cart' => []]);
    exit;
}

$userId = $_SESSION['user_id'];
$cartItems = [];

$query = "SELECT c.product_id, p.product_name, p.product_price, p.product_img 
          FROM cart c
          JOIN products p ON c.product_id = p.product_id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cartItems[] = [
        'name' => $row['product_name'],
        'price' => number_format($row['product_price'], 2),
        'image' => "data:image/jpeg;base64," . base64_encode($row['product_img'])
    ];
}

echo json_encode(['cart' => $cartItems]);
// fetch cart func end