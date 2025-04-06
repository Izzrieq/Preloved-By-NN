<?php
session_start();
include 'config/conn.php';
include 'components/nav.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$cartQuery = "SELECT 
                c.cart_id, 
                c.product_id, 
                c.quantity, 
                c.size, 
                p.product_name, 
                COALESCE(pv.price, p.product_price) AS product_price
              FROM cart c
              JOIN products p ON c.product_id = p.product_id
              LEFT JOIN product_variants pv 
                ON c.product_id = pv.product_id 
                AND c.size = pv.size 
              WHERE c.user_id = '$user_id'";

$cartResult = mysqli_query($conn, $cartQuery);
$cartItems = [];
$totalCost = 0;
while ($row = mysqli_fetch_assoc($cartResult)) {
    $cartItems[] = $row;
    $totalCost += $row['product_price'] * $row['quantity'];
}

$paymentQuery = "SELECT * FROM payments WHERE user_id = '$user_id' ORDER BY payment_date DESC LIMIT 1";
$paymentResult = mysqli_query($conn, $paymentQuery);
$paymentDetails = mysqli_fetch_assoc($paymentResult);
$paymentPending = $paymentDetails && $paymentDetails['payment_status'] == 'pending';

$cardQuery = "SELECT * FROM user_cards WHERE user_id = '$user_id'";
$cardResult = mysqli_query($conn, $cardQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Preloved by NN</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<body>
    <section class="checkout">
        <div class="checkout-container">
            <h2>Checkout</h2>
            <?php if ($paymentDetails && $paymentDetails['payment_status'] == 'success') { ?>
                <h1>Thank you for purchasing with us!</h1>
                <a href='functions/print_receipt.php?payment_id=<?= $paymentDetails['payment_id'] ?>' target='_blank'>
                    <button class='print-receipt-btn'>Print Receipt</button>
                </a>
            <?php } else { ?>
                <div class="checkout-items">
                    <h3>Your Cart</h3>
                    <?php if (count($cartItems) > 0) { ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <?php if (!$paymentPending) { ?><th>Remove</th><?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $row) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                                        <td><?= htmlspecialchars($row['size']) ?></td>
                                        <td><?= htmlspecialchars($row['quantity']) ?></td>
                                        <td>RM <?= number_format($row['product_price'], 2) ?></td>
                                        <td>RM <?= number_format($row['product_price'] * $row['quantity'], 2) ?></td>
                                        <?php if (!$paymentPending) { ?>
                                            <td><a href="functions/removeFromCheckout.php?cart_id=<?= $row['cart_id'] ?>" class="remove-item">X</a></td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="checkout-total">
                            <h4>Total: RM <?= number_format($totalCost, 2) ?></h4>
                        </div>
                    <?php } else { ?>
                        <p>Your cart is empty.</p>
                    <?php } ?>
                </div>

                <div class="checkout-payment">
                    <h3>Payment</h3>
                    <form action="functions/process_payment.php" method="POST">
                        <input type="hidden" name="totalCost" value="<?= $totalCost ?>">

                        <label for="card">Select a Card</label>
                        <select id="card" name="card_id" <?= $paymentPending ? 'disabled' : '' ?> required>
                            <?php while ($card = mysqli_fetch_assoc($cardResult)) { ?>
                                <option value="<?= $card['card_id']; ?>">
                                    <?= htmlspecialchars($card['card_name']) . " - **** " . substr($card['card_number'], -4); ?>
                                </option>
                            <?php } ?>
                        </select>

                        <button type="button" id="addCardBtn" <?= $paymentPending ? 'disabled' : '' ?>>+ Add New Card</button>

                        <div class="checkout-submit">
                            <?php if ($paymentPending) { ?>
                                <p>Your payment is in process</p>
                                <a href='functions/print_receipt.php?payment_id=<?= $paymentDetails['payment_id'] ?>' target='_blank'>
                                    <button type="button" class="print-receipt-btn">Print Receipt</button>
                                </a>
                            <?php } else { ?>
                                <button class="submit-payment-btn" type="submit" name="submit_payment">Pay RM <?= number_format($totalCost, 2) ?></button>
                            <?php } ?>
                        </div>
                    </form>
                </div>


                <div id="addCardModalUnique" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Add a New Card</h2>
                        <form action="functions/add_card.php" method="POST">
                            <div class="input-group">
                                <label for="card_name">Name on Card</label>
                                <input type="text" id="card_name" name="card_name" placeholder="John Doe" required>
                            </div>

                            <div class="input-group">
                                <label for="card_number">Card Number</label>
                                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                            </div>

                            <div class="input-row">
                                <div class="input-group">
                                    <label for="expiry_date">Expiry Date</label>
                                    <input type="month" id="expiry_date" name="expiry_date" required>
                                </div>

                                <div class="input-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>
                                </div>
                            </div>

                            <button type="submit" name="add_card" class="save-btn">Save Card</button>
                        </form>
                    </div>
                </div>

            <?php } ?>
        </div>
    </section>
    <script>
        document.getElementById("addCardBtn").addEventListener("click", function() {
            document.getElementById("addCardModalUnique").style.display = "block";
        });

        document.querySelector(".close").addEventListener("click", function() {
            document.getElementById("addCardModalUnique").style.display = "none";
        });
    </script>
    <?php include 'components/footer.php'; ?>
</body>

</html>