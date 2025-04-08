<!-- fetch sales item func start -->
<?php
include '../../config/conn.php';

if (!isset($_GET['record_id'])) {
    die('Invalid request.');
}

$record_id = (int) $_GET['record_id'];

$itemsQuery = "
    SELECT 
        rpi.product_id, 
        rpi.size, 
        rpi.quantity, 
        p.product_name 
    FROM record_payment_items rpi
    JOIN products p ON rpi.product_id = p.product_id
    WHERE rpi.record_id = ?
";

$stmt = mysqli_prepare($conn, $itemsQuery);
mysqli_stmt_bind_param($stmt, "i", $record_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' width='100%'>";
    echo "<thead><tr><th>Product Name</th><th>Size</th><th>Quantity</th></tr></thead>";
    echo "<tbody>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['size']) . "</td>";
        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No items found for this sale.</p>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
//fetch sales items func end