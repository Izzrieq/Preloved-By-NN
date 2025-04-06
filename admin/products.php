<?php
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$categories = [
    1 => "Unisex Perfume",
    2 => "Mens Perfume",
    3 => "Woman Perfume",
    4 => "Daily Wear Unisex",
    5 => "Daily Wear Men",
    6 => "Daily Wear Women"
];

$productQuery = "SELECT * FROM products ORDER BY category_id";
$productResult = mysqli_query($conn, $productQuery);
$productsByCategory = [];

while ($product = mysqli_fetch_assoc($productResult)) {
    $categoryId = $product['category_id'];
    $productsByCategory[$categoryId][] = $product;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" href="../assets/logo2.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <h2>Our Products</h2>

            <!-- Add Product Button -->
            <a href="product_add.php" class="btn-add">
                <i class="fa-solid fa-plus"></i> <!-- FontAwesome Plus Icon -->
            </a>
            <div class="dashboard-cards">
                <div class="card low-stock-product">
                    <h3>Total Low Stock Products</h3>
                    <p class="low-stock-count">
                        <?php
                        $lowStockQuery = "SELECT COUNT(*) AS total_low_stock FROM products WHERE stocks < 15";
                        $lowStockResult = mysqli_query($conn, $lowStockQuery);
                        $lowStockData = mysqli_fetch_assoc($lowStockResult);
                        echo $lowStockData['total_low_stock'];
                        ?>
                    </p>
                </div>

                <div class="card empty-card">
                    <h3>Upcoming Feature</h3>
                </div>
            </div>
            <?php foreach ($categories as $categoryId => $categoryName) { ?>
                <div class="category-section">
                    <div class="category-title"><?php echo $categoryName; ?></div>
                    <div class="product-list">
                        <?php
                        if (isset($productsByCategory[$categoryId])) {
                            foreach ($productsByCategory[$categoryId] as $product) {
                                $isLowStock = $product['stocks'] < 15 ? 'low-stock' : '';
                        ?>
                                <div class="product-card <?php echo $isLowStock; ?>">
                                    <?php
                                    $imageData = base64_encode($product['product_img']);
                                    $imageSrc = "data:image/jpeg;base64," . $imageData;
                                    ?>
                                    <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    <h3><?php echo $product['product_name']; ?></h3>
                                    <p><?php echo substr($product['product_desc'], 0, 100) . '...'; ?></p>
                                    <div class="price">RM <?php echo number_format($product['product_price'], 2); ?></div>
                                    <a href="product_edit.php?product_id=<?php echo $product['product_id']; ?>" class="btn-edit">Edit</a>
                                    <a href="functions/product_delete.php?product_id=<?php echo $product['product_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                </div>

                        <?php
                            }
                        } else {
                            echo "<p>No products available in this category.</p>";
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>