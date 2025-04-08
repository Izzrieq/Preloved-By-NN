<!-- get size func start -->
<?php
include '../config/conn.php';

if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    $query = "SELECT size, price FROM product_variants WHERE product_id = '$productId'";
    $result = mysqli_query($conn, $query);

    $variants = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $variants[] = [
            'size' => $row['size'],
            'price' => $row['price']
        ];
    }

    echo json_encode(['variants' => $variants]);
}
//get size func end