remove from cart func start
<?php
session_start();
include '../config/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cartId = intval($_POST['cart_id']);

    $query = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $cartId);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}
//remove from cart func end