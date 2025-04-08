<!-- get product function start -->
<?php
include '../config/conn.php';

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); // Ensure JSON response

if (isset($_GET['product_id'])) {
    $productId = intval($_GET['product_id']);

    if (!$conn) {
        echo json_encode(["error" => "Database connection failed: " . mysqli_connect_error()]);
        exit();
    }

    // Fetch product details using category_id
    $query = "SELECT category_id, product_price AS fixed_price FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(["error" => "SQL prepare error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Product not found"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Invalid request"]);
}
// get profuct function end