<?php
session_start();
include 'components/nav.php';
include 'config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login_signup.php");
    exit();
}


$user_id = $_SESSION['user_id'];
$queryUser = "SELECT username, email, nophone, address, dob, shipping_address FROM users WHERE user_id = '$user_id'";
$resultUser = mysqli_query($conn, $queryUser);
$user = mysqli_fetch_assoc($resultUser);

$queryCards = "SELECT * FROM user_cards WHERE user_id = '$user_id'";
$resultCards = mysqli_query($conn, $queryCards);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Preloved by NN</title>
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>

<body>

    <div class="profile-container">
        <div class="profile-section">
            <h2>Edit Profile</h2>
            <form id="updateProfileForm">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

                <label>Phone Number</label>
                <input type="text" name="nophone" value="<?= htmlspecialchars($user['nophone']); ?>" required>

                <label>Address</label>
                <input type="text" name="address" id="address" value="<?= htmlspecialchars($user['address']); ?>" required oninput="copyAddress()">

                <label>Use the same shipping address?</label>
                <input type="checkbox" id="sameAddress" checked onclick="toggleShipping()">

                <label>Shipping Address</label>
                <input type="text" name="shipping_address" id="shipping_address" value="<?= htmlspecialchars($user['shipping_address'] ?? ''); ?>">

                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']); ?>" required>

                <button type="submit">Update Profile</button>
            </form>
        </div>

        <div class="card-section">
            <h2>Add Card</h2>
            <div id="cardsContainer">
                <?php while ($card = mysqli_fetch_assoc($resultCards)) { ?>
                    <div class="card">
                        <button class="delete-card" onclick="deleteCard(<?= $card['card_id']; ?>)">
                            <i class="fa fa-times"></i>
                        </button>
                        <p><strong><?= htmlspecialchars($card['card_name']); ?></strong></p>
                        <p>
                            <span class="card-number" data-full="<?= htmlspecialchars($card['card_number']); ?>">
                                **** **** **** <?= substr($card['card_number'], -4); ?>
                            </span>
                            <i class="fa fa-eye toggle-eye" onclick="toggleCardNumber(this)"></i>
                        </p>
                        <p>Exp: <?= htmlspecialchars($card['expiry_date']); ?></p>
                    </div>
                <?php } ?>
            </div>
            <button class="add-card-btn" onclick="addCardForm()"><i class="fas fa-plus"></i> Add Card</button>
        </div>

        <div class="profile-section">
            <h2>Change Password</h2>
            <form id="changePasswordForm">
                <label>Current Password</label>
                <input type="password" name="current_password" required>

                <label>New Password</label>
                <input type="password" name="new_password" required>

                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>

                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>

    <script>
        function copyAddress() {
            if (document.getElementById("sameAddress").checked) {
                document.getElementById("shipping_address").value = document.getElementById("address").value;
            }
        }

        function toggleShipping() {
            const address = document.getElementById("address");
            const shippingAddress = document.getElementById("shipping_address");
            const checkbox = document.getElementById("sameAddress");

            if (!address || !shippingAddress || !checkbox) {
                console.error("One or more elements are missing!");
                return;
            }

            if (checkbox.checked) {
                shippingAddress.value = address.value;
                shippingAddress.readOnly = true;
            } else {
                shippingAddress.readOnly = false;
            }
        }

        document.getElementById("address").addEventListener("input", function() {
            if (document.getElementById("sameAddress").checked) {
                document.getElementById("shipping_address").value = this.value;
            }
        });

        function toggleCardNumber(icon) {
            const cardNumberSpan = icon.previousElementSibling;
            const fullNumber = cardNumberSpan.getAttribute("data-full");

            if (cardNumberSpan.textContent.includes("****")) {
                cardNumberSpan.textContent = fullNumber;
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                cardNumberSpan.textContent = "**** **** **** " + fullNumber.slice(-4);
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        function deleteCard(cardId) {
            if (!confirm("Are you sure you want to delete this card?")) return;

            fetch("functions/profileHandler.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `action=delete_card&card_id=${cardId}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === "success") location.reload();
                })
                .catch(error => console.error("Error:", error));
        }


        $(document).ready(function() {
            $("#updateProfileForm").submit(function(event) {
                event.preventDefault();
                $.ajax({
                    url: "functions/profileHandler.php",
                    type: "POST",
                    data: $(this).serialize() + "&action=updateProfile",
                    success: function(response) {
                        alert(response);
                    },
                    error: function() {
                        alert("Error updating profile!");
                    }
                });
            });

            $("#changePasswordForm").submit(function(event) {
                event.preventDefault();
                $.ajax({
                    url: "functions/profileHandler.php",
                    type: "POST",
                    data: $(this).serialize() + "&action=changePassword",
                    success: function(response) {
                        alert(response);
                    },
                    error: function() {
                        alert("Error changing password!");
                    }
                });
            });

            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("addCardForm").addEventListener("submit", function(event) {
                    event.preventDefault();

                    const formData = new FormData(this);
                    formData.append("action", "add_card");

                    fetch("functions/profileHandler.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
            });

        });

        function addCardForm() {
            const container = document.getElementById("cardsContainer");
            const newCardForm = document.createElement("form");
            newCardForm.classList.add("addCardForm");
            newCardForm.innerHTML = `
        <label>Card Name</label>
        <input type="text" name="card_name" required>

        <label>Card Number</label>
        <input type="text" name="card_number" maxlength="16" required>

        <label>Expiry Date</label>
        <input type="month" name="expiry_date" required>

        <label>CVV</label>
        <input type="text" name="cvv" maxlength="3" required>

        <button type="submit">Save Card</button>
    `;

            container.appendChild(newCardForm);

            newCardForm.addEventListener("submit", function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                formData.append("action", "add_card");

                fetch("functions/profileHandler.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.status === "success") location.reload();
                    })
                    .catch(error => console.error("Error:", error));
            });
        }
    </script>

    <?php include 'components/footer.php'; ?>
</body>

</html>