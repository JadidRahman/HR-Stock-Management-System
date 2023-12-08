<?php
session_start();

$servername = "localhost"; // or your DB server
$username = "root";
$password = "";
$dbname = "EMS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $descriptions = $_POST['description'];
    $subtotals = $_POST['subtotal'];

    $total = 0;
    $allDescriptions = []; // Array to hold all descriptions

    // Start transaction
    $conn->begin_transaction();

    try {
        // Prepare statement for expense_vouchers
        $stmt = $conn->prepare("INSERT INTO expense_vouchers (name, issue_date, description, subtotal) VALUES (?, ?, ?, ?)");
        for ($i = 0; $i < count($descriptions); $i++) {
            $description = filter_var($descriptions[$i], FILTER_SANITIZE_STRING);
            $subtotal = filter_var($subtotals[$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            $total += $subtotal;
            $date = date("Y-m-d"); // Assuming the date is the same for all entries, otherwise take date from a form input

            $stmt->bind_param("sssd", $name, $date, $description, $subtotal);
            $stmt->execute();

            $allDescriptions[] = $description; // Add to descriptions array
        }
        $stmt->close();

        // Combine all descriptions into one string
        $combinedDescription = implode("; ", $allDescriptions);

        // Insert the total into acc_ledger table
        $stmt = $conn->prepare("INSERT INTO acc_ledger (credit, date, description) VALUES (?, ?, ?)");
        $stmt->bind_param("dss", $total, $date, $combinedDescription);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        $_SESSION['message'] = "Expense voucher and total debit saved successfully.";
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $_SESSION['message'] = "Error: " . $exception->getMessage();
    }

    // Redirect back to the form page
    header('Location: p.php'); // Replace with the actual URL or PHP file of your form
    exit();
}
?>