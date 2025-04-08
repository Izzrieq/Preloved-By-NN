<!-- save card func start -->
<?php
session_start();
include '../config/conn.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_card'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $card_number = mysqli_real_escape_string($conn, $_POST['card_number']);
    $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);

    $query = "INSERT INTO user_cards (user_id, card_name, card_number, expiry_date, cvv) 
              VALUES ('$user_id', '$name', '$card_number', '$expiry_date', '$cvv')";
    if (mysqli_query($conn, $query)) {
        header('Location: ../checkout.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
//save card func end