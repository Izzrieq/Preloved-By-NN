<?php
session_start();

$response = [
    "loggedIn" => isset($_SESSION['user_id']),
    "username" => isset($_SESSION['username']) ? $_SESSION['username'] : null
];

header('Content-Type: application/json');
echo json_encode($response);
