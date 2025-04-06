<?php
include '../../config/conn.php';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $deleteQuery = "DELETE FROM products WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Product deleted successfully'); window.location.href='../products.php';</script>";
    } else {
        echo "<script>alert('Error deleting product'); window.location.href='../products.php';</script>";
    }
}

mysqli_close($conn);
