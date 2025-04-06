<?php

include '../config/conn.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login_signup.php');
    exit();
}


$orderQuery = "
    SELECT COUNT(*) AS total_orders FROM (
        SELECT payment_id FROM payments
        UNION ALL
        SELECT record_id FROM record_payment 
    ) AS combined_orders";

$orderResult = mysqli_query($conn, $orderQuery);
$orderData = mysqli_fetch_assoc($orderResult);
$totalOrders = $orderData['total_orders'];



$revenueQuery = "SELECT SUM(total_amount) AS total_revenue FROM record_payment";
$revenueResult = mysqli_query($conn, $revenueQuery);
$revenueData = mysqli_fetch_assoc($revenueResult);
$totalRevenue = $revenueData['total_revenue'];


$pendingQuery = "SELECT COUNT(*) AS pending_orders FROM payments WHERE payment_status = 'pending'";
$pendingResult = mysqli_query($conn, $pendingQuery);
$pendingData = mysqli_fetch_assoc($pendingResult);
$pendingOrders = $pendingData['pending_orders'];


$completedQuery = "SELECT COUNT(*) AS completed_orders FROM record_payment";
$completedResult = mysqli_query($conn, $completedQuery);
$completedData = mysqli_fetch_assoc($completedResult);
$completedOrders = $completedData['completed_orders'];


$orderDetailsQuery = "SELECT p.payment_id, p.user_id, p.total_amount, p.payment_status, p.payment_date, u.username, u.email 
                      FROM payments p 
                      JOIN users u ON p.user_id = u.user_id 
                      ORDER BY p.payment_date DESC";
$orderDetailsResult = mysqli_query($conn, $orderDetailsQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" href="../assets/logo2.png" type="image/png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .order-details {
            display: none;
        }

        .order-row {
            cursor: pointer;
        }

        .order-row:hover {
            background-color: #f1f1f1;
        }

        .btn-update {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .btn-update:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <div class="header">
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                <div class="user-info">
                    <a href="profile.php">Profile</a>
                    <a href="../auth/logout.php">Logout</a>
                </div>
            </div>
            <h2>Orders Overview</h2>
            <div class="dashboard-cards">
                <div class="card">Total Orders: <strong><?php echo $totalOrders; ?></strong></div>
                <div class="card">Total Revenue: <strong>RM <?php echo number_format($totalRevenue, 2); ?></strong></div>
                <div class="card">Pending Orders: <strong><?php echo $pendingOrders; ?></strong></div>
                <div class="card">Completed Orders: <strong><?php echo $completedOrders; ?></strong></div>
            </div>

            <h3>Order Details</h3>
            <table border="1" width="100%">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($orderDetailsResult)) { ?>
                        <tr class="order-row" data-user="<?php echo $row['user_id']; ?>">
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>RM <?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                            <td>
                                <button class="view-details">View</button>
                                <?php if ($row['payment_status'] == 'pending') { ?>
                                    <button class="btn-update" data-payment-id="<?php echo $row['payment_id']; ?>">Update Status</button>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr class="order-details" id="details-<?php echo $row['user_id']; ?>">
                            <td colspan="6">
                                <div class="loading">Loading...</div>
                                <div class="items-container"></div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".view-details").click(function() {
                let userId = $(this).closest(".order-row").data("user");
                let detailsRow = $("#details-" + userId);

                if (detailsRow.is(":visible")) {
                    detailsRow.hide();
                } else {
                    $(".order-details").hide();
                    detailsRow.show();

                    if (detailsRow.find(".items-container").is(":empty")) {
                        $.ajax({
                            url: "functions/fetch_order_items.php",
                            type: "GET",
                            data: {
                                user_id: userId
                            },
                            success: function(response) {
                                detailsRow.find(".loading").hide();
                                detailsRow.find(".items-container").html(response);
                            }
                        });
                    }
                }
            });

            $(".btn-update").click(function() {
                let paymentId = $(this).data("payment-id");

                $.ajax({
                    url: "functions/update_payment_status.php",
                    type: "POST",
                    data: {
                        payment_id: paymentId,
                        status: "success"
                    },
                    success: function(response) {
                        console.log(response); // Log the response to check for errors
                        if (response.trim() == "success") {
                            alert("Payment status updated to 'Success'");
                            location.reload();
                        } else {
                            alert("Error: " + response); // Show error message
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert("AJAX Error: " + error);
                    }
                });
            });
        });
    </script>
</body>

</html>