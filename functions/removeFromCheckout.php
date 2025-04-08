<!-- remove from checkout func start -->
<?php
session_start();
include '../config/conn.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['cart_id'])) {
    $cart_id = intval($_GET['cart_id']);

    $query = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $cart_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../checkout.php?success=true");
        exit();
    } else {
        header("Location: ../checkout.php?error=delete_failed");
        exit();
    }
} else {
    header("Location: ../checkout.php?error=invalid_request");
    exit();
}
//remove from checkout func end