<?php
session_start();

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'EMS';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$rowsPerPage = 25;
$offset = ($page - 1) * $rowsPerPage;
$startDate = '';
$endDate = '';
$transactions = [];
$debitTotal = 0;
$creditTotal = 0;
$balanceTotal = 0;
$totalPages = 1;

// If there's a start date, get the last forwarding balance before that date
if (isset($_GET['start_date'])) {
    $startDate = date('Y-m-d', strtotime($_GET['start_date']));
    $getForwardingBalanceStmt = $conn->prepare("SELECT forwarding_balance FROM acc_ledger WHERE date < ? ORDER BY date DESC, id DESC LIMIT 1");
    $getForwardingBalanceStmt->bind_param("s", $startDate);
    $getForwardingBalanceStmt->execute();
    $resultForwardingBalance = $getForwardingBalanceStmt->get_result();
    if ($resultForwardingBalance->num_rows > 0) {
        $openingBalance = $resultForwardingBalance->fetch_assoc()['forwarding_balance'];
    } else {
        $openingBalance = 0.00; // Default to 0 if no previous balance is found
    }
    $getForwardingBalanceStmt->close();
} else {
    $openingBalance = 00.00; // Default opening balance if no start date is set
}

$balance = $openingBalance; // Initialize balance with the opening balance

// Check if the date range form has been submitted
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $startDate = date('Y-m-d', strtotime($_GET['start_date']));
    $endDate = date('Y-m-d', strtotime($_GET['end_date']));

    // Validate dates
    if (strtotime($startDate) && strtotime($endDate)) {
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        // Update the query to filter by the date range
        $query = "SELECT id, date, description, debit, credit FROM acc_ledger 
        WHERE date BETWEEN '$startDate' AND '$endDate' 
        ORDER BY date ASC, id ASC LIMIT $offset, $rowsPerPage";
        // Query for calculating totals within the date range
        $totalsQuery = "SELECT SUM(debit) as totalDebit, SUM(credit) as totalCredit 
                        FROM acc_ledger 
                        WHERE date BETWEEN '$startDate' AND '$endDate'";
        $totalsResult = $conn->query($totalsQuery);
        if ($totalsResult && $totalsResult->num_rows > 0) {
            $totalsRow = $totalsResult->fetch_assoc();
            $debitTotal = $totalsRow['totalDebit'];
            $creditTotal = $totalsRow['totalCredit'];
            // Assuming credits add to the balance and debits subtract from it$balanceTotal = $openingBalance + $debitTotal - $creditTotal;
            $balanceTotal = $openingBalance + $creditTotal - $debitTotal;
        }

        // Fetch total number of rows for pagination purposes
        $totalRowsQuery = "SELECT COUNT(*) as total FROM acc_ledger WHERE date BETWEEN '$startDate' AND '$endDate'";
        $totalRowsResult = $conn->query($totalRowsQuery);
        if ($totalRowsResult && $totalRowsResult->num_rows > 0) {
            $totalRows = $totalRowsResult->fetch_assoc()['total'];
            $totalPages = ceil($totalRows / $rowsPerPage);
        }
    } else {
        echo "Invalid date range!";
    }
} else {
    // Use the default query if no date range is provided
    $query = "SELECT id, date, description, debit, credit FROM acc_ledger 
              ORDER BY date ASC, id ASC LIMIT $offset, $rowsPerPage";
    // Default totals and pagination
    $totalsQuery = "SELECT SUM(debit) as totalDebit, SUM(credit) as totalCredit FROM acc_ledger";
}

$result = $conn->query($query);
$balance = $openingBalance;

if ($result && $result->num_rows > 0) {
    $lastTransactionId = null;
    // Prepare the update query outside the loop to be used inside the loop
    $updateStmt = $conn->prepare("UPDATE acc_ledger SET balance = ? WHERE id = ?");

    while ($row = $result->fetch_assoc()) {
        $debit = $row['debit'] ?? 0;
        $credit = $row['credit'] ?? 0;
        $balance += $debit - $credit;


        // Update the balance in the transaction row before adding it to the array
        $row['balance'] = $balance;
        $transactions[] = $row; // Add the row to the transactions array

        // Bind the new balance and transaction ID to the prepared statement
        $updateStmt->bind_param("di", $balance, $row['id']);

        // Execute the prepared statement to update the balance for the current transaction
        if (!$updateStmt->execute()) {
            echo "Error updating balance for transaction ID: " . htmlspecialchars($row['id']);
        }

        $lastTransactionId = $row['id']; // Keep track of the last transaction ID
    }

    // Close the prepared statement
    $updateStmt->close();

    // Only update the forwarding balance if this is the last page of the results
    if ($page == $totalPages && $lastTransactionId) {
        $updateForwardingBalanceStmt = $conn->prepare("UPDATE acc_ledger SET forwarding_balance = ? WHERE id = ?");
        $updateForwardingBalanceStmt->bind_param("di", $balance, $lastTransactionId);
        if (!$updateForwardingBalanceStmt->execute()) {
            echo "Error updating forwarding balance for transaction ID: " . htmlspecialchars($lastTransactionId);
        }
        $updateForwardingBalanceStmt->close();
    }
}

$balanceTotal = $balance; // Final balance total after all transactions

date_default_timezone_set('Asia/Dhaka');
$generatedOn = date('Y-m-d h:i:s A'); // Format: YYYY-MM-DD HH:MM:SS AM/PM


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts Ledger</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }

        .container {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        .print-logo img {
            max-width: 100%;
            /* Ensures the image is never more than 100% of its container */
            height: auto;
            /* Keeps the aspect ratio of the image */
            width: 20%;
            /* You can use a percentage to make it responsive */
            display: block;
            margin: 0 auto 20px auto;
            padding-top: 20px;
        }

        #page-number {
            text-align: center;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #fff;
            padding: 5px;
            font-size: 12px;
            display: none;
            /* Hide the page number on screen by default */
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #e9ecef;
            color: #495057;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .date-picker {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .date-picker input[type="date"],
        .date-picker input[type="submit"] {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            margin-right: 10px;
        }

        .date-picker input[type="submit"] {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .date-picker input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .pagination {
            display: flex;
            justify-content: center;
            padding-left: 0;
            list-style: none;
        }

        .pagination li a {
            color: #007bff;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 5px 10px;
            margin-left: -1px;
        }

        .pagination li.active a {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }

        .pagination li a:hover {
            color: #0056b3;
            background-color: #e9ecef;
        }

        @page {
            counter-increment: page;

            @bottom-right {
                content: "Page " counter(page);
            }
        }

        .no-print {
            display: none;
        }

        #back-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
            /* Ensures it's above other content */
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        button,
        input[type="submit"] {
            padding: 12px 18px;
            font-size: 1rem;
            /* Larger font size for readability */
        }

        .date-picker input[type="date"],
        .date-picker input[type="submit"] {
            padding: 10px;
            font-size: 1rem;
        }


        @media only screen and (max-width: 768px) {
            body {
                font-size: 14px;
            }

            .date-picker input[type="date"],
            .date-picker input[type="submit"],
            #back-button {
                padding: 5px;
                font-size: 14px;
            }

            .table th,
            .table td {
                padding: 0.5rem;
                /* Smaller padding */
            }

            .print-logo img {
                width: 50%;
                /* Larger logo on small screens */
            }
        }

        #back-button {
            position: static;
            margin: 10px 0;
            display: block;
            /* Full-width on smaller screens */
        }

        @media print {

            .date-picker,
            .pagination,
            .no-print,
            #back-button {
                display: none;
            }

            .no-print {
                display: block;
                /* or 'display: table-row;' for table rows */
            }

            body {
                margin-bottom: auto;
                /* Provide space at the bottom for the page number */
            }

            statement-summary,
            .generated-on,
            .footer-print {
                text-align: center;
                font-size: small;
                /* Adjust as needed for your layout */
            }

            .table {
                width: 100%;
                /* Make sure the table uses the full page width */
                max-width: none;
                /* Override any existing max-width */
            }

            .table th,
            .table td {
                padding: 1rem;
                /* Increase padding to use more space */
                /* Adjust the padding as necessary */
            }

            /* Example of setting column widths, adjust as necessary */
            .table th:nth-child(1),
            .table td:nth-child(1) {
                width: 5%;
            }

            .table th:nth-child(2),
            .table td:nth-child(2) {
                width: 10%;
            }

            .table th:nth-child(3),
            .table td:nth-child(3) {
                width: 5%;
            }

            .table th:nth-child(4),
            .table td:nth-child(4) {
                width: 5%;
            }

            .table th:nth-child(5),
            .table td:nth-child(5) {
                width: 5%;
            }

            @page {
                margin-bottom: 50px;
                /* Adjust this value to move the page number up or down */
                counter-reset: page;
            }

            .page-number::after {
                counter-increment: page;
                content: "Page " counter(page);
            }

            .page-number {
                position: fixed;
                bottom: 10px;
                left: 10px;
                text-align: left;
                font-size: 12px;
            }

            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: center;
            }

            .footer:after {
                counter-increment: page;
                content: "Page " counter(page);
            }


            .footer-print {
                position: fixed;
                bottom: 10px;
                right: 10px;
                text-align: right;
                font-size: 8px;
                /* This is typically the smallest readable size */
            }

        }
    </style>

</head>

<body>

    <div class="print-logo">
        <img src="Safwah_Main_logo-removebg-preview.png" alt="Company Logo" />
    </div>

    <div class="container mt-5">
        <!-- Date Range Filter Form -->
        <div class="date-picker">
            <form action="accountsledger.php" method="get">
                From: <input type="date" name="start_date" id="start_date" placeholder="Start Date" required>
                To: <input type="date" name="end_date" id="end_date" placeholder="End Date" required>
                <input type="submit" value="Search">
            </form>
        </div>


        <div class="statement-period-print">
            <?php if (!empty($startDate) && !empty($endDate)): ?>
                <p>Statement Period From
                    <?php echo htmlspecialchars($startDate); ?> To
                    <?php echo htmlspecialchars($endDate); ?>.
                </p>
            <?php endif; ?>
        </div>


        <form action="accountsledger.php" method="post">
            <button type="button" id="back-button" onclick="window.location.href='admindashboard.php';">Back to
                Dashboard</button>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <!-- <th>Cheque/Advice</th> -->
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- If there's an opening balance, display it as "Forwarding Balance" -->
                        <?php if ($openingBalance > 0 && $startDate): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($startDate); ?>
                                </td>
                                <td>Forwarding Balance</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>
                                    <?php echo number_format($openingBalance, 2); ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <!-- Display each transaction -->
                        <?php
                        $periodDebitTotal = 0;
                        $periodCreditTotal = 0;
                        $lastBalance = $openingBalance;
                        foreach ($transactions as $transaction):
                            $periodDebitTotal += $transaction['debit'];
                            $periodCreditTotal += $transaction['credit'];
                            $lastBalance = $lastBalance + $transaction['debit'] - $transaction['credit'];

                            ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($transaction['date']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($transaction['description']); ?>
                                </td>
                                <td>
                                    <?php echo number_format($transaction['debit'], 2); ?>
                                </td>
                                <td>
                                    <?php echo number_format($transaction['credit'], 2); ?>
                                </td>
                                <td>
                                    <?php echo number_format($lastBalance, 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Display Total Row for transactions within the selected date range only -->
                        <tr>
                            <td colspan="2">
                                <strong>Total for period
                                    <?php echo (!empty($startDate) && !empty($endDate)) ? " $startDate to $endDate" : ''; ?>
                                </strong>
                            </td>
                            <td>
                                <?php echo number_format($periodDebitTotal, 2); ?>
                            </td>
                            <td>
                                <?php echo number_format($periodCreditTotal, 2); ?>
                            </td>
                            <td>
                                <?php echo number_format($lastBalance, 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <center /><strong />.................................................

                <tfoot <div class="statement-summary no-print">
                    <Center /><strong>STATEMENT SUMMARY:-</strong><br>
                    <tr>
                        <th colspan="4">Debits</th>
                        <td>
                            <?php echo number_format($debitTotal, 2); ?>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="4">Credits</th>
                        <td>
                            <?php echo number_format($creditTotal, 2); ?>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="4">Closing Balance</th>
                        <td>
                            <?php echo number_format($balanceTotal, 2); ?>
                        </td>
                    </tr>
                </tfoot>
                <div class="generated-on no-print">
                    <center /><strong>Generated On:
                        <?php echo $generatedOn; ?>
                    </strong>
                </div><br><br>

                <!-- Pagination -->
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>"><a class="page-link"
                                href="?page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a></li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
                <div class="footer-print no-print">
                    This is a software-generated document and does not require a signature.
                </div>
            </div>

            <div class="page-number no-print"></div>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.bundle.min.js"></script>

            <div class="page-number"></div>
</body>

</html>