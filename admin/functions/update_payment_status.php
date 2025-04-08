<!-- update payment status function start -->
<?php
include '../../config/conn.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['payment_id']) || !isset($_POST['status'])) {
    die('Invalid request.');
}

$payment_id = (int) $_POST['payment_id'];
$status = $_POST['status'];

if ($status !== 'success') {
    die('Invalid status.');
}

$paymentQuery = "SELECT * FROM payments WHERE payment_id = ?";
$stmt = mysqli_prepare($conn, $paymentQuery);
mysqli_stmt_bind_param($stmt, "i", $payment_id);
mysqli_stmt_execute($stmt);
$paymentResult = mysqli_stmt_get_result($stmt);
$payment = mysqli_fetch_assoc($paymentResult);
mysqli_stmt_close($stmt);

if (!$payment) {
    die('Payment not found.');
}

$user_id = $payment['user_id'];
$card_id = $payment['card_id'];
$total_amount = $payment['total_amount'];
$payment_date = $payment['payment_date'];

mysqli_begin_transaction($conn);

try {
    $insertQuery = "INSERT INTO record_payment (user_id, card_id, total_amount, payment_status, payment_date) 
                VALUES (?, ?, ?, 'success', ?)";

    $stmt = mysqli_prepare($conn, $insertQuery);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "iids", $user_id, $card_id, $total_amount, $payment_date);

    if (!mysqli_stmt_execute($stmt)) {
        die("Execute failed: " . mysqli_error($conn));
    }

    $record_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $cartQuery = "SELECT c.product_id, c.size, c.quantity, COALESCE(pv.price, p.product_price) AS product_price, p.product_name 
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    LEFT JOIN product_variants pv ON c.product_id = pv.product_id AND c.size = pv.size
    WHERE c.user_id = ?";

    $stmtCart = mysqli_prepare($conn, $cartQuery);
    mysqli_stmt_bind_param($stmtCart, "i", $user_id);
    mysqli_stmt_execute($stmtCart);
    $cartItems = mysqli_stmt_get_result($stmtCart);

    while ($item = mysqli_fetch_assoc($cartItems)) {
        $insertItemQuery = "INSERT INTO record_payment_items (record_id, product_id, product_name, size, quantity, product_price) 
                            VALUES (?, ?, ?, ?, ?, ?)";
        $stmtItem = mysqli_prepare($conn, $insertItemQuery);
        mysqli_stmt_bind_param($stmtItem, "iissid", $record_id, $item['product_id'], $item['product_name'], $item['size'], $item['quantity'], $item['product_price']);
        mysqli_stmt_execute($stmtItem);
        mysqli_stmt_close($stmtItem);
    }
    mysqli_stmt_close($stmtCart);

    $deletePaymentQuery = "DELETE FROM payments WHERE payment_id = ?";
    $stmtDelete = mysqli_prepare($conn, $deletePaymentQuery);
    mysqli_stmt_bind_param($stmtDelete, "i", $payment_id);
    mysqli_stmt_execute($stmtDelete);
    mysqli_stmt_close($stmtDelete);

    $deleteCartQuery = "DELETE FROM cart WHERE user_id = ?";
    $stmtDeleteCart = mysqli_prepare($conn, $deleteCartQuery);
    mysqli_stmt_bind_param($stmtDeleteCart, "i", $user_id);
    mysqli_stmt_execute($stmtDeleteCart);
    mysqli_stmt_close($stmtDeleteCart);

    mysqli_commit($conn);
    echo "success";
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Failed to update payment status: " . $e->getMessage();
}

mysqli_close($conn);
//update payment status function end