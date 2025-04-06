<?php
include '../../config/conn.php';
ini_set('display_errors', 1);
session_start();

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);

    $query = "SELECT c.product_id, c.quantity, pr.product_name, pr.product_price
              FROM cart c
              JOIN products pr ON c.product_id = pr.product_id
              WHERE c.user_id = '$user_id'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "<p>SQL Error: " . mysqli_error($conn) . "</p>";
        exit;
    }

    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' width='100%'>";
        echo "<thead><tr><th>Product Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead>";
        echo "<tbody>";

        while ($row = mysqli_fetch_assoc($result)) {
            $totalPrice = $row['product_price'] * $row['quantity'];
            echo "<tr>
                    <td>" . htmlspecialchars($row['product_name']) . "</td>
                    <td>" . htmlspecialchars($row['quantity']) . "</td>
                    <td>RM " . number_format($row['product_price'], 2) . "</td>
                    <td>RM " . number_format($totalPrice, 2) . "</td>
                  </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p>No items found for this order.</p>";
    }
} else {
    echo "<p>Error: user_id not set.</p>";
}
