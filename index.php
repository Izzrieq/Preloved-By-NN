<?php
session_start();
include 'components/nav.php';
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$isLoggedIn = isset($_SESSION['user_id']);

$queryPerfumes = "SELECT * FROM products WHERE category_id IN (1, 2, 3) ORDER BY order_count DESC LIMIT 5";
$resultPerfumes = mysqli_query($conn, $queryPerfumes);

$queryTshirts = "SELECT * FROM products WHERE category_id IN (4, 5, 6) ORDER BY order_count DESC LIMIT 5";
$resultTshirts = mysqli_query($conn, $queryTshirts);

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preloved by NN</title>
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>

<body>

    <div class="hero-section">
        <div class="overlay"></div>
        <div class="content">
            <h1>Discover Preloved Luxury</h1>
            <p>Shop high-quality preloved fashion at the best prices.</p>
            <a href="shop.php" class="btn">Shop Now</a>
        </div>
    </div>

    <section class="best-selling">
        <h2>Best Selling Perfumes</h2>
        <div class="perfume-list">
            <?php while ($row = mysqli_fetch_assoc($resultPerfumes)) { ?>
                <div class="perfume-card">
                    <?php
                    $imageData = base64_encode($row['product_img']);
                    $imageSrc = "data:image/jpeg;base64," . $imageData;
                    ?>
                    <div class="perfume-img">
                        <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <div class="perfume-overlay">
                            <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                            <button class="buy-now" onclick="handleBuyNow(<?php echo $row['product_id']; ?>, <?php echo $row['category_id']; ?>)">Buy Now</button>
                            <span class="add-cart" onclick="handleAddToCart(<?php echo $row['product_id']; ?>, <?php echo $row['category_id']; ?>)">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                        </div>
                    </div>
                    <p class="perfume-price">RM <?php echo number_format($row['product_price'], 2); ?></p>
                </div>
            <?php } ?>
        </div>
    </section>

    <section class="best-selling-tshirts">
        <h2>Best Selling Daily Wear</h2>
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

        function handleAddToCart(productId, categoryId) {
            if (!isLoggedIn) {
                showLoginPopup();
                return;
            }

            let size = (categoryId === 1 || categoryId === 2 || categoryId === 3) ? '60ml' : 'S';
            const quantity = 1;

            const formData = new FormData();
            formData.append('addToCart', true);
            formData.append('productId', productId);
            formData.append('size', size);
            formData.append('quantity', quantity);

            fetch('functions/handleAddToCart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        console.log("Server Response:", data);
                        if (data.success) {
                            showCustomAlert();
                            updateCartModal();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (error) {
                        console.error("Parsing error:", error);
                        console.error("Raw response:", text);
                        alert("Unexpected response from server: " + text);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('An error occurred.');
                });
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