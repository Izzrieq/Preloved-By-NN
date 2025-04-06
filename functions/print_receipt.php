<?php
session_start();
include '../config/conn.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$payment_id = $_GET['payment_id'];
$user_id = $_SESSION['user_id'];

$paymentQuery = "SELECT p.payment_id, p.total_amount, p.payment_date, p.payment_status, u.username
                 FROM payments p
                 JOIN users u ON p.user_id = u.user_id
                 WHERE p.payment_id = '$payment_id' AND p.user_id = '$user_id'";

$paymentResult = mysqli_query($conn, $paymentQuery);
$paymentDetails = mysqli_fetch_assoc($paymentResult);

$cartQuery = "SELECT c.cart_id, c.product_id, c.quantity, c.size, p.product_name, p.product_price
              FROM cart c
              JOIN products p ON c.product_id = p.product_id
              WHERE c.user_id = '$user_id'";

$cartResult = mysqli_query($conn, $cartQuery);

$totalCost = 0;

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Receipt - Preloved by NN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .receipt-header img {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .receipt-header h1 {
            margin: 0;
        }
        .receipt-details, .cart-items {
            margin: 20px 0;
        }
        .receipt-details table, .cart-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-details th, .cart-items th,
        .receipt-details td, .cart-items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .cart-items td {
            text-align: right;
        }
        .total {
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class='receipt-header'>
        <img src='../assets/logo2.png' alt='Preloved by NN Logo'>
        <h1>Receipt</h1>
        <p>Thank you for purchasing with Preloved by NN</p>
    </div>

    <div class='receipt-details'>
        <h3>Payment Details</h3>
        <table>
            <tr>
                <th>Payment ID</th>
                <td>{$paymentDetails['payment_id']}</td>
            </tr>
            <tr>
                <th>Username</th>
                <td>{$paymentDetails['username']}</td>
            </tr>
            <tr>
                <th>Payment Date</th>
                <td>{$paymentDetails['payment_date']}</td>
            </tr>
            <tr>
                <th>Payment Status</th>
                <td>" . ucfirst($paymentDetails['payment_status']) . "</td>
            </tr>
            <tr>
                <th>Total Amount</th>
                <td>RM " . number_format($paymentDetails['total_amount'], 2) . "</td>
            </tr>
        </table>
    </div>

    <div class='cart-items'>
        <h3>Items Purchased</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>";

while ($row = mysqli_fetch_assoc($cartResult)) {
    if (in_array($row['product_id'], [1, 2, 3])) {
        $row['size'] = 'N';
    }

    $productTotal = $row['product_price'] * $row['quantity'];
    $totalCost += $productTotal;
    echo "<tr>
                        <td>{$row['product_name']}</td>
                        <td>{$row['size']}</td>
                        <td>{$row['quantity']}</td>
                        <td>RM " . number_format($row['product_price'], 2) . "</td>
                        <td>RM " . number_format($productTotal, 2) . "</td>
                      </tr>";
}

echo "</tbody>
        </table>
    </div>

    <div class='total'>
        <h3>Total: RM " . number_format($totalCost, 2) . "</h3>
    </div>

    <div class='footer'>
        <p>If you have any questions, feel free to contact us.</p>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>";
