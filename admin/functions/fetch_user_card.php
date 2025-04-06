<?php
include '../../config/conn.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $cardQuery = "SELECT * FROM user_cards WHERE user_id = $user_id";
    $cardResult = mysqli_query($conn, $cardQuery);

    if (mysqli_num_rows($cardResult) > 0) {
        echo "<table border='1' width='100%'>
                <thead>
                    <tr>
                        <th>Card Name</th>
                        <th>Card Number</th>
                        <th>Expiry Date</th>
                        <th>CVV</th>
                    </tr>
                </thead>
                <tbody>";
        while ($card = mysqli_fetch_assoc($cardResult)) {
            echo "<tr>
                    <td>" . htmlspecialchars($card['card_name']) . "</td>
                    <td>" . htmlspecialchars($card['card_number']) . "</td>
                    <td>" . htmlspecialchars($card['expiry_date']) . "</td>
                    <td>" . htmlspecialchars($card['cvv']) . "</td>
                </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>User doesn’t insert card info.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
