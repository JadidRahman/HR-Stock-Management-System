<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Receipts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3pV8TcC2Hfdcti2wTzIIdI3W7b8U0z0RjSEU6Cf6TAWjBAFjM0lOJ67giS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
        integrity="sha384-0I6z5M8Sy3V9t6C2BdaaNqHjxmTecL8OpHg5h9ITnqz9pFAEn16lwkT6j+KR0ny1" crossorigin="anonymous">
    <style>
        /* Add your CSS styling here */
        table {
            width: 100%;
            /* Full width */
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .pagination {
            display: inline-block;
            padding-left: 0;
            margin: 20px 0;
            margin-left: 1000px;
            border-radius: 4px;
        }

        .pagination li {
            display: inline;
        }

        .pagination li a,
        .pagination li.active a,
        .pagination li.disabled {
            color: #337ab7;
            padding: 6px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin-left: -1px;
        }

        .pagination li:first-child a {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .pagination li:last-child a {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .pagination li a:hover,
        .pagination li.active a,
        .pagination li.disabled {
            background-color: #eee;
        }

        .pagination li.active a {
            color: white;
            background-color: #337ab7;
            border-color: #337ab7;
        }

        .pagination li.disabled {
            color: #777;
            cursor: not-allowed;
        }

        .print-btn {
            margin-bottom: 10px;
            background: none;
            border: none;
        }

        .print-btn img {
            width: 25px;
            /* Adjust as necessary */
            height: auto;
        }

        .print-logo {
            display: none;
        }

        @media (min-width: 992px) {

            /* Adjusting for Bootstrap's 'lg' breakpoint */
            .main-content {
                margin-left: 255px;
                /* Adjust this value based on the width of your sidebar */
            }
        }

        /* Styles for the print button */
        .print-btn {
            margin-bottom: 10px;
        }

        #printStatement {
            display: none;
            text-align: center;
            margin: 20px 0;
            font-size: 1.2em;
        }


        @media print {

            .print-btn,
            form,
            .pagination,
            .print-btn,
            .col-md-3,
            .navbar {
                display: none;
                /* Hide elements during printing */
            }

            .print-logo {
                display: block;
                position: fixed;
                top: 10px;
                right: 10px;
                left: 10px;
            }

            .print-logo img {
                max-width: 200px;
                /* Adjust as needed */
                max-height: 100px;
                /* Adjust as needed */
            }

            body {
                margin: 0;
                padding: 0;
                line-height: 1.4;
                word-spacing: 1px;
                letter-spacing: 0.2px;
                font: 14px Arial, sans-serif;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
                font-size: 12px;
            }

            .print-only {
                display: block;
                /* Show this div only when printing */

            }

            #printStatement {
                display: block;
                /* Only show during printing */

            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Place this inside the <body> tag where appropriate -->
    <div class="print-logo">
        <img src="Safwah_Main_logo-removebg-preview.png" alt="Company Logo" />
    </div>

    <?php include 'navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include 'sidebar.php'; ?>
            </div><br /><br /><br />
            <div class="main-content">
                <center />
                <h2>Money Receipt</h2><br />
                <div id="printStatement" class="no-print"></div>
                <div>
                    <!-- Search form -->
                    <form action="viewl.php" method="get">
                        <input type="text" name="search" placeholder="Search receipts...">
                        From: <input type="date" name="start_date" id="start_date" placeholder="Start Date">
                        To: <input type="date" name="end_date" id="end_date" placeholder="End Date">
                        <input type="submit" value="Search" onclick="updatePrintStatement()">
                    </form>
                    <button onclick="printPage()" class="btn btn-primary no-print">
                        <i class="fas fa-print"></i>
                    </button>
                    </form>
                    <table>
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>Voucher No</th>
                                <th>Received From</th>
                                <th>Payment Method</th>
                                <th>Cheque No.</th>
                                <th>Bank</th>
                                <th>Transection Date</th>
                                <th>Sender No.</th>
                                <th>Receiver No.</th>
                                <th>Transection No.</th>
                                <th>Purpose</th>
                                <th>Amount</th>

                            </tr>
                        </thead>
                        <tbody>
                </div>
                <!-- Hidden print statement for print view only -->
                <div id="printStatement" class="print-only">
                    <?php
                    // Check if the form has been submitted and start_date and end_date are set
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
                    if ($start_date && $end_date) {
                        echo "This Statement is from " . date('m/d/Y', strtotime($start_date)) . " to " . date('m/d/Y', strtotime($end_date));
                    }
                    ?>

                    <?php
                    $host = 'localhost'; // Your database host
                    $username = 'root'; // Your database username
                    $password = ''; // Your database password
                    $database = 'EMS'; // Your database name
                    
                    // Create a new MySQLi connection
                    $conn = new mysqli($host, $username, $password, $database);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    // Pagination logic
                    $resultsPerPage = 25;
                    $sql = "SELECT COUNT(*) as total FROM receipts";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    $totalPages = ceil($row['total'] / $resultsPerPage);
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $thisPageFirstResult = ($page - 1) * $resultsPerPage;

                    // Get the search term and the search dates from the URL parameters
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

                    // Convert start_date and end_date to the proper format
                    $start_date = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : null;
                    $end_date = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : null;

                    // Append the limit clause to the SQL statement
                    $sql .= " LIMIT " . $thisPageFirstResult . ',' . $resultsPerPage;
                    $totalAmount = 0.0;

                    // Initialize the query with a condition that's always true
                    $sql = "SELECT * FROM receipts WHERE 1=1";

                    // Initialize an array for the types and parameters
                    $types = '';
                    $params = [];


                    // Add conditions for the search term
                    if (!empty($search)) {
                        $sql .= " AND (sl_no LIKE ? OR voucher_no LIKE ? OR received_from LIKE ? OR purpose LIKE ?)";
                        $searchTerm = "%$search%";
                        $params[] = &$searchTerm;
                        $params[] = &$searchTerm;
                        $params[] = &$searchTerm;
                        $params[] = &$searchTerm;
                        $types .= 'ssss';
                    }

                    // Add conditions for the date range
                    if (!empty($start_date) && !empty($end_date)) {
                        $sql .= " AND transaction_date BETWEEN ? AND ?";
                        $params[] = &$start_date;
                        $params[] = &$end_date;
                        $types .= 'ss';
                    }

                    $stmt = $conn->prepare($sql);

                    // Check if there are parameters to bind
                    if ($types) {
                        $stmt->bind_param($types, ...$params);
                    }

                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Fetch and display the data
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['sl_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['voucher_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['received_from']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";

                        echo "<td>" . htmlspecialchars($row['cheque_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['bank_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['transaction_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sender_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['receiver_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['transaction_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['purpose']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                        // Calculate the running total
                        $totalAmount += $row['amount'];
                    }

                    // Display the total amount row
                    echo "<tr class='total-row'>";
                    echo "<th colspan='11'>Total Amount</th>"; // Adjust the colspan to match your number of columns
                    echo "<td>" . number_format($totalAmount, 2) . "</td>";
                    echo "<td></td>"; // Assuming the last column is for the print button
                    echo "</tr>";

                    echo "</tr>";



                    // Close the statement and connection
                    $stmt->close();
                    $conn->close();
                    ?>
                    </tbody>
                    </table>
                    <!-- Pagination links -->
                    <div class="pagination">
                        <ul>
                            <?php
                            // Previous page link
                            if ($page > 1) {
                                echo '<li><a href="viewreceipt.php?page=' . ($page - 1) . '">&laquo; Previous</a></li>';
                            } else {
                                echo '<li class="disabled">&laquo; Previous</li>';
                            }

                            // Page number links
                            for ($i = 1; $i <= $totalPages; $i++) {
                                $class = ($page == $i) ? "active" : "";
                                echo '<li class="' . $class . '"><a href="viewreceipt.php?page=' . $i . '">' . $i . '</a></li>';
                            }

                            // Next page link
                            if ($page < $totalPages) {
                                echo '<li><a href="viewreceipt.php?page=' . ($page + 1) . '">Next &raquo;</a></li>';
                            } else {
                                echo '<li class="disabled">Next &raquo;</li>';
                            }
                            ?>
                        </ul>
                    </div>

                    <script>
                        function updatePrintStatement() {
                            var startDate = document.getElementById('start_date').value;
                            var endDate = document.getElementById('end_date').value;
                            var printStatementDiv = document.getElementById('printStatement');

                            // Change the print statement only if both dates are selected
                            if (startDate && endDate) {
                                printStatementDiv.textContent = 'This Statement is from ' + startDate + ' to ' + endDate;
                            }
                        }

                        function printPage() {
                            updatePrintStatement(); // Make sure the print statement is updated
                            window.print();
                        }

                        // You may want to call updatePrintStatement on page load if you want to prepopulate dates
                        window.onload = updatePrintStatement;
                    </script>


</body>

</html>