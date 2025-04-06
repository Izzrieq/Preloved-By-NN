<?php
session_start();
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$isLoggedIn = isset($_SESSION['user_id']);

if (!$isLoggedIn) {
    header('Location: auth/login_signup.php');
    exit();
}

$userId = $_SESSION['user_id'];

$queryCart = "SELECT c.cart_id, p.product_id, p.product_name, c.price, c.size, c.quantity, p.product_img
              FROM cart c
              JOIN products p ON c.product_id = p.product_id
              WHERE c.user_id = '$userId'";

$resultCart = mysqli_query($conn, $queryCart);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Preloved by NN</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<body>

    <?php include 'components/nav.php'; ?>

    <div class="cart-container">
        <h2>Your Cart</h2>

        <?php if (mysqli_num_rows($resultCart) > 0) { ?>
            <div class="cart-items">
                <?php while ($row = mysqli_fetch_assoc($resultCart)) { ?>
                    <div class="cart-item">
                        <?php
                        $imageData = base64_encode($row['product_img']);
                        $imageSrc = "data:image/jpeg;base64," . $imageData;
                        $size = ($row['size'] == 'N' || empty($row['size'])) ? 'N' : $row['size']; // Handle "N" or NULL
                        ?>
                        <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" class="cart-item-img">
                        <div class="cart-item-details">
                            <p class="cart-item-name"><?php echo htmlspecialchars($row['product_name']); ?></p>
                            <p class="cart-item-price">RM <?php echo number_format($row['price'], 2); ?></p>
                            <p class="cart-item-size">Size: <?php echo htmlspecialchars($size); ?></p>
                            <p class="cart-item-quantity">Quantity: <?php echo $row['quantity']; ?></p>
                        </div>
                        <div class="cart-item-actions">
                            <a href="#"
                                onclick="openEditModal(
        <?php echo $row['cart_id']; ?>, 
        <?php echo $row['product_id']; ?>, 
        '<?php echo htmlspecialchars($row['size']); ?>', 
        <?php echo $row['quantity']; ?>
    )"
                                class="edit-item">
                                Edit
                            </a>

                            <a href="#" onclick="removeFromCart(<?php echo $row['cart_id']; ?>)" class="remove-item">Remove</a>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="cart-total">
                <?php
                $totalQuery = "SELECT SUM(p.product_price * c.quantity) AS total
                               FROM cart c
                               JOIN products p ON c.product_id = p.product_id
                               WHERE c.user_id = '$userId'";
                $resultTotal = mysqli_query($conn, $totalQuery);
                $totalRow = mysqli_fetch_assoc($resultTotal);
                $totalCost = $totalRow['total'];
                ?>

                <p class="total-price">Total: RM <?php echo number_format($totalCost, 2); ?></p>
                <a href="checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
            </div>
        <?php } else { ?>
            <p>Your cart is empty. Start shopping now!</p>
            <a href="shop.php" class="cart-btn">Go to Shop</a>
        <?php } ?>
    </div>

    <div id="editModal" style="display: none;">
        <form id="editForm">
            <input type="hidden" id="cart_id" name="cart_id">

            <label for="editSize">Size:</label>
            <select id="editSize" name="size"></select>

            <label for="editQuantity">Quantity:</label>
            <input type="number" id="editQuantity" name="quantity" min="1">

            <label for="editPrice">Price:</label>
            <input type="text" id="editPrice" name="price" readonly>

            <button type="submit">Update Cart</button>
            <button type="button" onclick="closeEditModal()">Cancel</button>
        </form>
    </div>


    <?php include 'components/footer.php'; ?>

    <script>
        function openEditModal(cartId, productId, currentSize, currentQuantity) {
            console.log("Cart ID:", cartId);
            console.log("Product ID:", productId);
            console.log("Current Size:", currentSize);
            console.log("Current Quantity:", currentQuantity);

            let sizeSelect = document.getElementById('editSize');
            let priceField = document.getElementById('editPrice');

            if (!sizeSelect || !priceField) {
                console.error("Error: Elements 'editSize' or 'editPrice' not found in DOM.");
                return;
            }

            document.getElementById('cart_id').value = cartId;
            document.getElementById('editQuantity').value = currentQuantity;

            fetch('functions/getProductDetails.php?product_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error fetching product details:", data.error);
                        alert("Error: " + data.error);
                        return;
                    }

                    console.log("Product details:", data);

                    let sizeSelect = document.getElementById('editSize');
                    let priceField = document.getElementById('editPrice');

                    if (!sizeSelect || !priceField) {
                        console.error("Error: Elements 'editSize' or 'editPrice' not found in DOM.");
                        return;
                    }

                    sizeSelect.innerHTML = "";

                    if ([4, 5, 6].includes(data.category_id)) {
                        let sizes = ["S", "M", "L", "XL"];
                        sizes.forEach(size => {
                            let option = document.createElement("option");
                            option.value = size;
                            option.textContent = `${size} - RM ${data.fixed_price}`;
                            option.dataset.price = data.fixed_price;
                            sizeSelect.appendChild(option);
                        });

                        sizeSelect.value = "S";
                        priceField.value = data.fixed_price;
                    } else {
                        fetch('functions/getSizes.php?product_id=' + productId)
                            .then(response => response.json())
                            .then(sizesData => {
                                if (!sizesData.variants) {
                                    console.error("Error: No size data found.");
                                    return;
                                }

                                sizesData.variants.forEach(variant => {
                                    let option = document.createElement("option");
                                    option.value = variant.size;
                                    option.textContent = `${variant.size} - RM ${variant.price}`;
                                    option.dataset.price = variant.price;
                                    sizeSelect.appendChild(option);
                                });

                                sizeSelect.value = currentSize;
                                priceField.value = sizeSelect.options[sizeSelect.selectedIndex].dataset.price;
                            })
                            .catch(error => console.error("Error fetching sizes:", error));
                    }
                })
                .catch(error => console.error("Error fetching product details:", error));

            sizeSelect.addEventListener("change", function() {
                let selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
                priceField.value = selectedOption.dataset.price;
            });

            document.getElementById('editModal').style.display = 'flex';
        }


        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            for (let pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }

            fetch('functions/updateCart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Server response:", data);
                    if (data.success) {
                        alert('Item updated successfully!');
                        location.reload();
                    } else {
                        alert('Error updating item: ' + data.error);
                    }
                })
                .catch(error => console.error('Error updating item:', error));
        });
        document.getElementById('editQuantity').addEventListener("input", function() {
            let quantity = parseInt(this.value);
            let sizeSelect = document.getElementById('editSize');
            let priceField = document.getElementById('editPrice');

            if (!sizeSelect || !priceField) {
                console.error("Error: Elements 'editSize' or 'editPrice' not found in DOM.");
                return;
            }

            let selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
            let basePrice = parseFloat(selectedOption.dataset.price) || 0;

            if (quantity > 0) {
                let totalPrice = basePrice * quantity;
                priceField.value = totalPrice.toFixed(2);
            }
        });




        function removeFromCart(cartId) {
            if (confirm("Are you sure you want to remove this item from your cart?")) {
                fetch('functions/removeFromCart.php', {
                        method: 'POST',
                        body: new URLSearchParams({
                            cart_id: cartId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Item removed successfully!");
                            location.reload();
                        } else {
                            alert("Error removing item.");
                        }
                    });
            }
        }
    </script>

</body>

</html>