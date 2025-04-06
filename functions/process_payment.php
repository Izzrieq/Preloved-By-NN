<?php
session_start();
include '../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || !isset($_POST['totalCost']) || !isset($_POST['card_id'])) {
    die('Invalid request.');
}

$user_id = $_SESSION['user_id'];
$total_amount = (float) $_POST['totalCost'];
$card_id = (int) $_POST['card_id'];
$payment_status = 'pending';
$payment_date = date('Y-m-d H:i:s');

if (!is_numeric($total_amount) || $total_amount <= 0) {
    die('Invalid total amount.');
}

// Check if selected card exists for this user
$checkCardQuery = "SELECT * FROM user_cards WHERE card_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $checkCardQuery);
mysqli_stmt_bind_param($stmt, "ii", $card_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cardExists = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$cardExists) {
    die('Invalid card selection.');
}

// Insert Payment with selected `card_id`
$query = "INSERT INTO payments (user_id, card_id, total_amount, payment_status, payment_date) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "iidss", $user_id, $card_id, $total_amount, $payment_status, $payment_date);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Your payment is pending!');
                window.location.href = '../checkout.php';
              </script>";
    } else {
        echo "Error executing query: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing the query: " . mysqli_error($conn);
}

mysqli_close($conn);
