<!-- product detail start -->
<?php
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$product_id = $_GET['product_id'];

$productQuery = "SELECT * FROM products WHERE product_id = $product_id";
$productResult = mysqli_query($conn, $productQuery);
$product = mysqli_fetch_assoc($productResult);

if (!$product) {
    echo "Product not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" href="../assets/logo2.png" type="image/png">
    <style>
        .product-detail {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            margin-top: 50px;
        }

        .product-detail img {
            max-width: 400px;
            height: auto;
            margin-right: 20px;
        }

        .product-info {
            max-width: 600px;
        }

        .product-info h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .product-info p {
            font-size: 16px;
            color: #555;
        }

        .product-info .price {
            font-size: 24px;
            color: #000;
            margin-top: 10px;
        }

        .btn-buy {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 50px;
        }

        .btn-buy:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <div class="product-detail">
                <?php
                $imageData = base64_encode($product['product_img']);
                $imageSrc = "data:image/jpeg;base64," . $imageData;
                ?>
                <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                <div class="product-info">
                    <h2><?php echo $product['product_name']; ?></h2>
                    <p><?php echo $product['product_desc']; ?></p>
                    <div class="price">RM <?php echo number_format($product['product_price'], 2); ?></div>
                    <a href="cart.php?product_id=<?php echo $product['product_id']; ?>" class="btn-buy">Add to Cart</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<!-- product detail end -->