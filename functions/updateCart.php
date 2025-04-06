<?php
include '../config/conn.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cart_id = $_POST['cart_id'] ?? null;
    $size = $_POST['size'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $price = $_POST['price'] ?? null;

    if (!$cart_id || !$size || !$quantity || !$price) {
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields",
            "cart_id" => $cart_id,
            "size" => $size,
            "quantity" => $quantity,
            "price" => $price
        ]);
        exit();
    }

    // Check if the cart_id exists
    $checkCartQuery = "SELECT cart_id FROM cart WHERE cart_id = ?";
    $stmtCheck = $conn->prepare($checkCartQuery);
    $stmtCheck->bind_param("i", $cart_id);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Cart ID not found"]);
        exit();
    }

    // Prepare SQL query
    $query = "UPDATE cart SET size = ?, quantity = ?, price = ? WHERE cart_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "SQL Error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("sidi", $size, $quantity, $price, $cart_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Execution failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}
