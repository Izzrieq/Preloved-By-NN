<!-- sales record start -->
<?php
include '../config/conn.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login_signup.php');
    exit();
}

$monthFilter = "";
if (isset($_GET['month']) && $_GET['month'] !== "") {
    $month = (int) $_GET['month'];
    $monthFilter = "AND MONTH(rp.payment_date) = $month";
}

$salesQuery = "
    SELECT 
        rp.record_id, 
        rp.user_id, 
        rp.card_id, 
        rp.total_amount, 
        rp.payment_date, 
        uc.card_name, 
        uc.card_number 
    FROM record_payment rp
    JOIN user_cards uc ON rp.card_id = uc.card_id
    WHERE 1 $monthFilter
    ORDER BY rp.payment_date DESC
";


$salesResult = mysqli_query($conn, $salesQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Records</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" href="../assets/logo2.png" type="image/png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <div class="wrapper">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <h2>Sales Records</h2>
            <form method="GET" action="sales_record.php">
                <label for="month">Filter by Month:</label>
                <select name="month" id="month" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php
                    for ($m = 1; $m <= 12; $m++) {
                        $monthName = date('F', mktime(0, 0, 0, $m, 1));
                        echo "<option value='$m' " . (isset($_GET['month']) && $_GET['month'] == $m ? 'selected' : '') . ">$monthName</option>";
                    }
                    ?>
                </select>
            </form>


            <table id="salesTable" border="1" width="100%">
                <thead>
                    <tr>
                        <th>Record ID</th>
                        <th>User ID</th>
                        <th>Card Name</th>
                        <th>Card Number</th>
                        <th>Total Amount (RM)</th>
                        <th>Payment Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($salesResult)) { ?>
                        <tr class="record-row" data-record="<?php echo $row['record_id']; ?>">
                            <td><?php echo htmlspecialchars($row['record_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['card_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['card_number'], -4)); ?>****</td>
                            <td><?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
                            <td><button class="view-details">View Items</button></td>
                        </tr>
                        <tr class="record-details" id="details-<?php echo $row['record_id']; ?>">
                            <td colspan="7">
                                <div class="loading">Loading...</div>
                                <div class="items-container"></div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button onclick="printTable()" class="print-button">Print Sales Record</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".view-details").click(function() {
                let recordId = $(this).closest(".record-row").data("record");
                let detailsRow = $("#details-" + recordId);

                if (detailsRow.is(":visible")) {
                    detailsRow.hide();
                } else {
                    $(".record-details").hide();
                    detailsRow.show();

                    if (detailsRow.find(".items-container").is(":empty")) {
                        $.ajax({
                            url: "functions/fetch_sales_items.php",
                            type: "GET",
                            data: {
                                record_id: recordId
                            },
                            success: function(response) {
                                detailsRow.find(".loading").hide();
                                detailsRow.find(".items-container").html(response);
                            }
                        });
                    }
                }
            });
        });


        function printTable() {
            var printContents = document.getElementById("salesTable").outerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = "<html><head><title>Print Sales Record</title></head><body><h2>Sales Records</h2>" + printContents + "</body></html>";
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>

</body>

</html>
<!-- sales record end -->