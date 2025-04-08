<!-- customer start -->
<?php
include '../config/conn.php';


$currentDate = date('Y-m-d H:i:s');

$query = "
    SELECT *,
        CASE
            WHEN last_login IS NULL OR TIMESTAMPDIFF(DAY, last_login, '$currentDate') > 3 THEN 'inactive'
            ELSE status
        END AS status
    FROM users WHERE userType = 'customer'
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer List</title>
    <link rel="icon" href="../assets/logo2.png" type="image/png">
    <link rel="stylesheet" href="styles/style.css">
    <script>
        function toggleCardDetails(userId) {
            var cardRow = document.getElementById("card-row-" + userId);

            if (cardRow.style.display === "none") {
                cardRow.style.display = "table-row";

                var xhr = new XMLHttpRequest();
                xhr.open("GET", "functions/fetch_user_card.php?user_id=" + userId, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        document.getElementById("card-data-" + userId).innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            } else {
                cardRow.style.display = "none";
            }
        }
    </script>
</head>

<body>
    <div class="wrapper">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <h2>Customer List</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>No Phone</th>
                        <th>Address</th>
                        <th>DOB</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) {
                        $user_id = $row['user_id'];
                    ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['nophone']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['dob']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <button onclick="toggleCardDetails(<?php echo $user_id; ?>)">View</button>
                            </td>
                        </tr>

                        <tr id="card-row-<?php echo $user_id; ?>" style="display: none;">
                            <td colspan="8" id="card-data-<?php echo $user_id; ?>">
                                Loading...
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
<!-- customer end -->