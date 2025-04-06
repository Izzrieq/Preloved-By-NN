<?php
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['product_desc']);
    $price = (float) $_POST['product_price'];
    $category_id = (int) $_POST['category_id'];

    $stocks = isset($_POST['product_stock']) && $_POST['product_stock'] !== '' ? (int) $_POST['product_stock'] : 0;

    echo "Stocks value after processing: " . $stocks;

    if (isset($_FILES['product_img']) && $_FILES['product_img']['tmp_name']) {
        $imageData = file_get_contents($_FILES['product_img']['tmp_name']);
    } else {
        $imageData = null;
    }

    $insertQuery = "INSERT INTO products (product_name, product_desc, product_price, category_id, product_img, stocks, order_count) 
VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insertQuery);

    $orderCount = 0;

    mysqli_stmt_bind_param($stmt, 'ssdissi', $name, $description, $price, $category_id, $imageData, $stocks, $orderCount);

    if ($imageData !== null) {
        mysqli_stmt_send_long_data($stmt, 4, $imageData);
    }


    if (mysqli_stmt_execute($stmt)) {
        header("Location: products.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="icon" href="../assets/logo2.png" type="image/png">
    <link rel="stylesheet" href="styles/style.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <h2>Add New Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="product_name">Product Name:</label>
                <input type="text" name="product_name" id="product_name" required>

                <label for="product_desc">Description:</label>
                <textarea name="product_desc" id="product_desc" required></textarea>

                <label for="product_price">Price (RM):</label>
                <input type="number" name="product_price" id="product_price" step="0.01" placeholder="120.00" required>

                <label for="category_id">Category:</label>
                <select name="category_id" id="category_id" required>
                    <option value="1">Unisex Perfume</option>
                    <option value="2">Mens Perfume</option>
                    <option value="3">Women Perfume</option>
                    <option value="4">Daily Wear Unisex</option>
                    <option value="5">Daily Wear Men</option>
                    <option value="6">Daily Wear Women</option>
                </select>

                <label for="product_img">Product Image:</label>
                <input type="file" name="product_img" id="product_img" required>

                <label for="product_stock">Stock Quantity:</label>
                <input type="number" name="product_stock" id="product_stock" min="0" value="0" required>

                <button type="submit">Add Product</button>
                <a href="products.php" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>
</body>

</html>