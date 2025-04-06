<?php
session_start();
include 'components/nav.php';
include 'config/conn.php';

$isLoggedIn = isset($_SESSION['user_id']);

$queryTshirts = "SELECT * FROM products WHERE category_id IN (4, 5 ) ORDER BY order_count DESC LIMIT 6";
$resultTshirts = mysqli_query($conn, $queryTshirts);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preloved by NN</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <section class="best-selling-tshirts">
        <h2>Men's Collection</h2>
        <div class="tshirt-list">
            <?php while ($row = mysqli_fetch_assoc($resultTshirts)) { ?>
                <div class="tshirt-card">
                    <?php
                    $imageData = base64_encode($row['product_img']);
                    $imageSrc = "data:image/jpeg;base64," . $imageData;
                    ?>
                    <div class="tshirt-img">
                        <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <div class="tshirt-overlay">
                            <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                            <button class="buy-now" onclick="handleBuyNow(<?php echo $row['product_id']; ?>)">Buy Now</button>
                            <span class="add-cart" onclick="handleAddToCart(<?php echo $row['product_id']; ?>)">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                        </div>
                    </div>
                    <p class="tshirt-price">RM <?php echo number_format($row['product_price'], 2); ?></p>
                </div>
            <?php } ?>
        </div>
    </section>

    <div id="loginPopup" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Please Log In</h2>
            <p>You need to log in to proceed with the purchase or add items to your cart.</p>
            <a href="auth/login_signup.php" class="log-btn">Log In</a>
        </div>
    </div>
    <div id="customAlertModal" class="custom-alert-modal">
        <div class="custom-alert-content">
            <span class="close" onclick="closeCustomAlert()">&times;</span>
            <h2>Item Added to Cart!</h2>
            <p>Your item has been successfully added to the cart.</p>
            <button onclick="goToCart()">Go to Cart</button>
            <button onclick="closeCustomAlert()">Continue Shopping</button>
        </div>
    </div>
    <script>
        let isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;

        function handleAddToCart(productId) {
            if (!isLoggedIn) {
                showLoginPopup();
            } else {
                const sizeElement = document.getElementById(`size-${productId}`);
                const size = sizeElement ? sizeElement.value : null;

                const quantityElement = document.getElementById(`quantity-${productId}`);
                const quantity = quantityElement ? quantityElement.value : 1;

                const formData = new FormData();
                formData.append('addToCart', true);
                formData.append('productId', productId);
                if (size !== null) {
                    formData.append('size', size);
                }
                formData.append('quantity', quantity);

                fetch('functions/handleAddToCart.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showCustomAlert();
                            updateCartModal();
                        } else {
                            alert(data.message || 'Failed to add item to cart.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred.');
                    });
            }
        }

        function showCustomAlert() {
            const modal = document.getElementById('customAlertModal');
            modal.style.display = 'flex';
        }

        function closeCustomAlert() {
            const modal = document.getElementById('customAlertModal');
            modal.style.display = 'none';
        }

        function goToCart() {
            window.location.href = 'cart.php';
        }

        function handleBuyNow(productId) {
            if (!isLoggedIn) {
                showLoginPopup();
            } else {
                window.location.href = "product_detail.php?productId=" + productId;
            }
        }

        function showCustomAlert() {
            const modal = document.getElementById('customAlertModal');
            modal.style.display = 'flex';

            setTimeout(() => {
                closeCustomAlert();
            }, 3000);
        }


        function updateCartModal() {
            const cartModal = document.getElementById('cartModal');
            const cartItems = document.getElementById('cartItems');

            fetch('functions/fetch_cart_items.php')
                .then(response => response.json())
                .then(data => {
                    cartItems.innerHTML = '';
                    if (data.cart && data.cart.length > 0) {
                        data.cart.forEach(product => {
                            const productDiv = document.createElement('div');
                            productDiv.classList.add('cart-item');
                            productDiv.innerHTML = `
                                <img src="${product.image}" alt="${product.name}">
                                <p>${product.name}</p>
                                <p>RM ${product.price}</p>
                            `;
                            cartItems.appendChild(productDiv);
                        });
                    } else {
                        cartItems.innerHTML = '<p>Your cart is empty.</p>';
                    }
                    cartModal.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function closeCart() {
            document.getElementById('cartModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('cartModal')) {
                closeCart();
            }
        }

        function showLoginPopup() {
            document.getElementById('loginPopup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('loginPopup').style.display = 'none';
        }
    </script>

    <?php include 'components/footer.php'; ?>
</body>

</html>