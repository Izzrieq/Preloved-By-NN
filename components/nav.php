<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preloved by NN</title>
    <link rel="icon" href="assets/logo2.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #F8F9FA;
            color: #333;
            padding-top: 70px;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f1f1;
            padding: 15px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .logo h1 {
            margin-top: 10px;
            font-size: 30px;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            list-style: none;
            flex-grow: 1;
            justify-content: center;
        }

        .nav-links li {
            margin: 0 15px;
            position: relative;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #f4a261;
        }

        .dropdown {
            display: none;
            position: absolute;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
            width: 150px;
        }

        .nav-links li:hover .dropdown {
            display: block;
        }

        .icons {
            display: flex;
            align-items: center;
        }

        .cart-icon,
        .user-icon {
            font-size: 18px;
            cursor: pointer;
            margin-left: 10px;
            transition: color 0.3s ease;
        }

        .cart-icon:hover,
        .user-icon:hover {
            color: #f4a261;
        }

        .cart-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            overflow-y: auto;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            text-align: center;
            border-radius: 8px;
        }

        .user-icon {
            position: relative;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 30px;
            right: 0;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .user-icon:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 5px;
            text-decoration: none;
            color: #333;
        }

        .dropdown-menu a:hover {
            color: #f4a261;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .checkout-btn {
            padding: 10px 20px;
            background-color: #00A6FF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .view-cart-btn {
            background-color: white;
            color: black;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid #02b602;
        }

        .view-cart-btn:hover {
            background-color: #02b602;
            color: white;
        }

        .checkout-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .checkout-btn:hover {
            background-color: #218838;
            color: white;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 60px;
                right: 0;
                background: #ffffff;
                width: 100%;
                text-align: center;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .nav-links.active {
                display: flex;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <nav>
        <div class="logo">
            <img src="assets/logo2.png" alt="logo">
            <h1>Preloved by NN</h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li>
                <a href="#">Category</a>
                <div class="dropdown">
                    <a href="mens_wear.php">Men's Wear</a>
                    <a href="womens_wear.php">Women's Wear</a>
                    <a href="perfume.php">Perfume</a>
                </div>
            </li>
            <li><a href="about_us.php">About</a></li>
            <li><a href="contact_us.php">Contact</a></li>
        </ul>
        <div class="icons">
            <div class="cart-icon" id="add-to-cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="user-icon" id="user-icon">
                <i class="fas fa-user"></i>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="profile.php">Profile</a>
                    <a href="auth/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div id="cartModal" class="cart-modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeCartModal()">&times;</span>
            <div id="cart-items">
                <p>Loading cart items...</p>
            </div>
            <div class="cart-actions">
                <a href="cart.php" class="view-cart-btn">View Cart</a>
                <button class="checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetchCartItems();
            setupUserIcon();
            setupAddToCartIcon();
        });

        function fetchCartItems() {
            fetch('functions/fetch_cart_items.php')
                .then(response => response.json())
                .then(data => {
                    const cartContainer = document.getElementById('cart-items');
                    cartContainer.innerHTML = "";

                    if (data.cart.length === 0) {
                        cartContainer.innerHTML = "<p>No items in the cart.</p>";
                        return;
                    }

                    data.cart.forEach(item => {
                        const cartItem = `
                <div class="cart-item" style="justify-content: space-between; display: flex; margin-bottom: 10px;">
                    <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px; margin-right: 10px;">
                    <span>${item.name}</span>
                    <span>RM${item.price}</span>
                </div>
            `;
                        cartContainer.innerHTML += cartItem;
                    });
                })
                .catch(error => console.error("Error fetching cart:", error));
        }

        function openCartModal() {
            document.getElementById("cartModal").style.display = "block";
        }

        function closeCartModal() {
            document.getElementById("cartModal").style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target === document.getElementById("cartModal")) {
                closeCartModal();
            }
        };

        function setupUserIcon() {
            const userIcon = document.getElementById("user-icon");
            const dropdownMenu = document.getElementById("dropdown-menu");

            const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

            if (!isLoggedIn) {
                userIcon.style.pointerEvents = 'none';
            }

            userIcon.addEventListener("mouseenter", function() {
                if (isLoggedIn) {
                    dropdownMenu.style.display = "block";
                }
            });

            userIcon.addEventListener("mouseleave", function() {
                dropdownMenu.style.display = "none";
            });
        }

        function setupAddToCartIcon() {
            const addToCartIcon = document.getElementById("add-to-cart-icon");

            const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

            addToCartIcon.addEventListener("click", function() {
                if (!isLoggedIn) {
                    alert("You need to log in to add items to the cart.");
                    window.location.href = "auth/login_signup.php";

                } else {
                    openCartModal();
                }
            });
        }

        function proceedToCheckout() {
            window.location.href = "checkout.php";
        }
    </script>
</body>

</html>