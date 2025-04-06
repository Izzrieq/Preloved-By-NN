<?php
session_start();
include 'config/conn.php';
include 'components/nav.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$payment_id = $_GET['payment_id'];
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM payments WHERE payment_id = '$payment_id' AND user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$payment = mysqli_fetch_assoc($result);

if (!$payment) {
    echo "Payment not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Preloved by NN</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<body>
    <section class="receipt">
        <div class="receipt-container">
            <h2>Payment Receipt</h2>
            <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($payment['payment_id']); ?></p>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($payment['user_id']); ?></p>
            <p><strong>Total Amount:</strong> RM <?php echo number_format($payment['total_amount'], 2); ?></p>
            <p><strong>Payment Status:</strong> <?php echo ucfirst($payment['payment_status']); ?></p>
            <p><strong>Payment Date:</strong> <?php echo $payment['payment_date']; ?></p>

            <?php if ($payment['payment_status'] == 'success') { ?>
                <button onclick="window.print()">Print Receipt</button>
            <?php } else { ?>
                <p>Your payment is still pending.</p>
            <?php } ?>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
</body>

</html>