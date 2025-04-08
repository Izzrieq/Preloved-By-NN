<!-- update profile func start -->
<?php
session_start();
include '../../config/conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nophone = mysqli_real_escape_string($conn, $_POST['nophone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "UPDATE users SET 
                username='$username', 
                email='$email', 
                nophone='$nophone', 
                address='$address', 
                dob='$dob', 
                status='$status' 
              WHERE user_id = $user_id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Profile updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating profile: " . mysqli_error($conn);
    }

    header("Location: ../setting.php");
    exit();
}
//update profile func end