<?php
ob_start();
session_start();
include 'config/conn.php';
include 'components/nav.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$product_id = $_GET['productId'];

$query = "SELECT * FROM products WHERE product_id = '$product_id'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

$category_id = $product['category_id'];

$variants = [];
if (in_array($category_id, [1, 2, 3])) {
    $queryVariants = "SELECT * FROM product_variants WHERE product_id = '$product_id'";
    $resultVariants = mysqli_query($conn, $queryVariants);

    if (!$resultVariants) {
        die("Error fetching variants: " . mysqli_error($conn));
    }

    $variants = [];
    while ($variant = mysqli_fetch_assoc($resultVariants)) {
        $variants[$variant['size']] = $variant['price'];
    }
}

$queryHotSelling = "SELECT * FROM products WHERE product_id != '$product_id' ORDER BY order_count DESC LIMIT 3";
$resultHotSelling = mysqli_query($conn, $queryHotSelling);

if (isset($_POST['add_to_cart']) || isset($_POST['buy_now'])) {
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];
    $product_price = in_array($category_id, [1, 2, 3]) ? $variants[$size] : $product['product_price']; // Get the price

    $insertQuery = "INSERT INTO cart (user_id, product_id, quantity, size, price) 
                    VALUES ('$user_id', '$product_id', '$quantity', '$size', '$product_price')";

    if (mysqli_query($conn, $insertQuery)) {
        if (isset($_POST['buy_now'])) {
            header('Location: checkout.php');
            exit();
        } else {
            $cartSuccessMessage = "Product successfully added to cart!";
        }
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
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Preloved by NN</title>
    <link rel="stylesheet" href="styles/style.css">
    <script>
        function updatePrice() {
            const sizeSelect = document.getElementById('size');
            const priceElement = document.getElementById('product-price');
            const prices = <?php echo json_encode($variants); ?>;

            if (prices[sizeSelect.value]) {
                priceElement.textContent = "RM " + parseFloat(prices[sizeSelect.value]).toFixed(2);
            }
        }
    </script>
</head>

<body>
    <section class="product-details">
        <div class="product-container">
            <div class="product-img">
                <?php
                $imageData = base64_encode($product['product_img']);
                $imageSrc = "data:image/jpeg;base64," . $imageData;
                ?>
                <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            </div>

            <div class="product-info">
                <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
                <p class="product-price" id="product-price">
                    RM <?php echo number_format(in_array($category_id, [1, 2, 3]) ? reset($variants) : $product['product_price'], 2); ?>
                </p>
                <p class="product-description"><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>

                <form action="product_detail.php?productId=<?php echo $product_id; ?>" method="POST">
                    <div class="product-size">
                        <label for="size">Size:</label>
                        <select id="size" name="size" required onchange="updatePrice()">
                            <?php if (in_array($category_id, [1, 2, 3])) { ?>
                                <?php foreach ($variants as $size => $price) { ?>
                                    <option value="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($size); ?></option>
                                <?php } ?>
                            <?php } else { ?>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="product-quantity">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                    </div>
                    <button type="submit" name="add_to_cart" class="add-to-cart"><i class="fas fa-shopping-cart"></i></button>
                    <button type="submit" name="buy_now" class="buy-now-details">Buy Now</button>
                </form>
            </div>
        </div>

        <?php if (!empty($cartSuccessMessage)) { ?>
            <script>
                alert("<?php echo $cartSuccessMessage; ?>");
            </script>
        <?php } ?>

        <section class="hot-selling">
            <h3>Hot Selling Products</h3>
            <div class="hot-selling-list">
                <?php while ($row = mysqli_fetch_assoc($resultHotSelling)) { ?>
                    <div class="hot-selling-item">
                        <?php
                        $imageData = base64_encode($row['product_img']);
                        $imageSrc = "data:image/jpeg;base64," . $imageData;
                        ?>
                        <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <h4><?php echo htmlspecialchars($row['product_name']); ?></h4>
                        <p>RM <?php echo number_format($row['product_price'], 2); ?></p>
                        <a href="product_detail.php?productId=<?php echo $row['product_id']; ?>" class="view-details">View Details</a>
                    </div>
                <?php } ?>
            </div>
        </section>
    </section>

    <?php include 'components/footer.php'; ?>
</body>

</html>