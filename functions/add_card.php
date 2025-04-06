<?php
session_start();
include '../config/conn.php';

if (!isset($_SESSION['user_id'])) {
    die('User not logged in.');
}

$user_id = $_SESSION['user_id'];
$card_name = $_POST['card_name'] ?? '';
$card_number = $_POST['card_number'] ?? '';
$expiry_date = $_POST['expiry_date'] ?? '';
$cvv = $_POST['cvv'] ?? '';

if (empty($card_name) || empty($card_number) || empty($expiry_date) || empty($cvv)) {
    die('All fields are required.');
}

$query = "INSERT INTO user_cards (user_id, card_name, card_number, expiry_date, cvv) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "issss", $user_id, $card_name, $card_number, $expiry_date, $cvv);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
            alert('Card added successfully!');
            window.location.href = '../checkout.php';
          </script>";
} else {
    echo "Error adding card: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
