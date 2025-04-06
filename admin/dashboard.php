<?php
include '../config/conn.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login_signup.php');
    exit();
}

$customerQuery = "SELECT COUNT(*) AS total_customers FROM users WHERE userType = 'customer'";
$customerResult = mysqli_query($conn, $customerQuery);
$customerData = mysqli_fetch_assoc($customerResult);
$totalCustomers = $customerData['total_customers'];

$adminQuery = "SELECT COUNT(*) AS total_admins FROM users WHERE userType = 'admin'";
$adminResult = mysqli_query($conn, $adminQuery);
$adminData = mysqli_fetch_assoc($adminResult);
$totalAdmins = $adminData['total_admins'];

$stockQuery = "SELECT SUM(stocks) AS total_stock FROM products";
$stockResult = mysqli_query($conn, $stockQuery);
$stockData = mysqli_fetch_assoc($stockResult);
$totalStock = $stockData['total_stock'];

$salesQuery = "SELECT SUM(total_amount) AS total_amount FROM record_payment";
$salesResult = mysqli_query($conn, $salesQuery);
$salesData = mysqli_fetch_assoc($salesResult);
$totalSales = $salesData['total_amount'];

$salesPerformanceQuery = "SELECT MONTH(payment_date) AS month, SUM(total_amount) AS total FROM record_payment GROUP BY MONTH(payment_date)";
$salesPerformanceResult = mysqli_query($conn, $salesPerformanceQuery);

$months = [];
$sales = [];
while ($row = mysqli_fetch_assoc($salesPerformanceResult)) {
    $months[] = date('F', mktime(0, 0, 0, $row['month'], 1));
    $sales[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" href="../assets/logo2.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="wrapper">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <div class="header">
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
            </div>
            <h2>Dashboard Overview</h2>
            <div class="dashboard-cards">
                <div class="card">Total Customers: <strong><?php echo $totalCustomers; ?></strong></div>
                <div class="card">Total Admins: <strong><?php echo $totalAdmins; ?></strong></div>
                <div class="card">Total Stock: <strong><?php echo $totalStock; ?></strong></div>
                <div class="card">Total Sales: <strong>RM <?php echo number_format($totalSales, 2); ?></strong></div>
            </div>
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Sales Performance (RM)',
                    data: <?php echo json_encode($sales); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>