<!-- profile handler func start -->
<?php
session_start();
include '../config/conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'updateProfile') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $nophone = mysqli_real_escape_string($conn, $_POST['nophone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);

        $query = "UPDATE users SET username='$username', email='$email', nophone='$nophone', address='$address', shipping_address='$shipping_address', dob='$dob' WHERE user_id='$user_id'";

        if (mysqli_query($conn, $query)) {
            echo "Profile updated successfully!";
        } else {
            echo "Error updating profile: " . mysqli_error($conn);
        }
    }

    if ($action === 'changePassword') {
        $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

        // Fetch the current password from DB
        $result = mysqli_query($conn, "SELECT password FROM users WHERE user_id='$user_id'");
        $user = mysqli_fetch_assoc($result);

        if (!password_verify($current_password, $user['password'])) {
            echo "Current password is incorrect!";
            exit();
        }

        if ($new_password !== $confirm_password) {
            echo "New passwords do not match!";
            exit();
        }

        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password='$hashed_password' WHERE user_id='$user_id'";

        if (mysqli_query($conn, $query)) {
            echo "Password changed successfully!";
        } else {
            echo "Error changing password: " . mysqli_error($conn);
        }
    }

    if ($action === "add_card") {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(["status" => "error", "message" => "User not logged in."]);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $card_name = mysqli_real_escape_string($conn, $_POST['card_name']);
        $card_number = mysqli_real_escape_string($conn, $_POST['card_number']);
        $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
        $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);

        if (strlen($card_number) !== 16 || !is_numeric($card_number)) {
            echo json_encode(["status" => "error", "message" => "Invalid card number!"]);
            exit();
        }
        if (strlen($cvv) !== 3 || !is_numeric($cvv)) {
            echo json_encode(["status" => "error", "message" => "Invalid CVV!"]);
            exit();
        }

        $query = "INSERT INTO user_cards (user_id, card_name, card_number, expiry_date, cvv) 
                  VALUES ('$user_id', '$card_name', '$card_number', '$expiry_date', '$cvv')";

        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "Card added successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add card!"]);
        }
        exit();
    }
    if ($action === "delete_card") {
        $card_id = intval($_POST['card_id']);
        $query = "DELETE FROM user_cards WHERE card_id = '$card_id'";

        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "Card deleted successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete card!"]);
        }
        exit();
    }
}
//profile handler func end