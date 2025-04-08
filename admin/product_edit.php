<!-- product edit start -->
<?php
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$product_id = $_GET['product_id'];

$productQuery = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $productQuery);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$productResult = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($productResult);

$variantQuery = "SELECT * FROM product_variants WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $variantQuery);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$variantResult = mysqli_stmt_get_result($stmt);
$variants = [];
while ($row = mysqli_fetch_assoc($variantResult)) {
    $variants[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product_name'];
    $description = $_POST['product_desc'];
    $category_id = $_POST['category_id'];
    $newStock = $_POST['product_stock'];
    $updatedStock = $product['stocks'] + $newStock;
    $product_price = $product['product_price'];

    if ($_FILES['product_img']['tmp_name']) {
        $imageData = file_get_contents($_FILES['product_img']['tmp_name']);
        $updateQuery = "UPDATE products SET product_name = ?, product_desc = ?, category_id = ?, product_img = ?, stocks = ? WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ssissi", $name, $description, $category_id, $imageData, $updatedStock, $product_id);
        mysqli_stmt_execute($stmt);
    } else {
        $updateQuery = "UPDATE products SET product_name = ?, product_desc = ?, category_id = ?, stocks = ? WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ssiii", $name, $description, $category_id, $updatedStock, $product_id);
        mysqli_stmt_execute($stmt);
    }

    if (isset($_POST['sizes']) && isset($_POST['prices'])) {
        foreach ($_POST['sizes'] as $index => $size) {
            $variantPrice = $_POST['prices'][$index];

            if (!empty($size) && !empty($variantPrice)) {
                if (strtolower($size) === '60ml') {
                    $product_price = $variantPrice;
                }

                $variantExistsQuery = "SELECT * FROM product_variants WHERE product_id = ? AND size = ?";
                $stmt = mysqli_prepare($conn, $variantExistsQuery);
                mysqli_stmt_bind_param($stmt, "is", $product_id, $size);
                mysqli_stmt_execute($stmt);
                $variantExistsResult = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($variantExistsResult) > 0) {
                    $updateVariantQuery = "UPDATE product_variants SET price = ? WHERE product_id = ? AND size = ?";
                    $stmt = mysqli_prepare($conn, $updateVariantQuery);
                    mysqli_stmt_bind_param($stmt, "dis", $variantPrice, $product_id, $size);
                    mysqli_stmt_execute($stmt);
                } else {
                    $insertVariantQuery = "INSERT INTO product_variants (product_id, size, price) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $insertVariantQuery);
                    mysqli_stmt_bind_param($stmt, "isd", $product_id, $size, $variantPrice);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
    }

    $updatePriceQuery = "UPDATE products SET product_price = ? WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $updatePriceQuery);
    mysqli_stmt_bind_param($stmt, "di", $product_price, $product_id);
    mysqli_stmt_execute($stmt);

    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" href="../assets/logo2.png" type="image/png">
    <script>
        function addVariantField() {
            let variantContainer = document.getElementById("variant-container");
            let variantDiv = document.createElement("div");
            variantDiv.classList.add("variant-item");
            variantDiv.innerHTML = `
                <input type="text" name="sizes[]" placeholder="Size (e.g., 60ml)" required>
                <input type="number" name="prices[]" placeholder="Price (RM)" step="0.01" required>
                <button type="button" onclick="this.parentElement.remove()">Remove</button>
            `;
            variantContainer.appendChild(variantDiv);
        }
    </script>
</head>

<body>
    <div class="wrapper">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <h2>Edit Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="product_name">Product Name:</label>
                <input type="text" name="product_name" id="product_name" value="<?= htmlspecialchars($product['product_name']); ?>" required>

                <label for="product_desc">Description:</label>
                <textarea name="product_desc" id="product_desc" required><?= htmlspecialchars($product['product_desc']); ?></textarea>

                <label for="category_id">Category:</label>
                <select name="category_id" id="category_id">
                    <option value="1" <?= $product['category_id'] == 1 ? 'selected' : ''; ?>>Unisex Perfume</option>
                    <option value="2" <?= $product['category_id'] == 2 ? 'selected' : ''; ?>>Mens Perfume</option>
                    <option value="3" <?= $product['category_id'] == 3 ? 'selected' : ''; ?>>Women Perfume</option>
                    <option value="4" <?= $product['category_id'] == 4 ? 'selected' : ''; ?>>Daily Wear Unisex</option>
                    <option value="5" <?= $product['category_id'] == 5 ? 'selected' : ''; ?>>Daily Wear Men</option>
                    <option value="6" <?= $product['category_id'] == 6 ? 'selected' : ''; ?>>Daily Wear Women</option>
                </select>

                <label for="product_img">Product Image:</label>
                <input type="file" name="product_img" id="product_img">

                <label for="product_stock">Add Stocks:</label>
                <input type="number" name="product_stock" id="product_stock" value="0" required>
                <p>Current Stock: <strong><?= $product['stocks']; ?></strong></p>

                <h3>Product Variants (Size & Price)</h3>
                <div id="variant-container">
                    <?php foreach ($variants as $variant) { ?>
                        <div class="variant-item">
                            <input type="text" name="sizes[]" value="<?= htmlspecialchars($variant['size']); ?>" required>
                            <input type="number" name="prices[]" value="<?= htmlspecialchars($variant['price']); ?>" step="0.01" required>
                            <button type="button" onclick="this.parentElement.remove()">Remove</button>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" onclick="addVariantField()">+ Add Variant</button>

                <button type="submit">Update Product</button>
                <a href="products.php" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>
</body>

</html>
<!-- product edit end -->