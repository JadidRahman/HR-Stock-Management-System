<?php
if (isset($_GET['sl_no'])) {
    $sl_no = $_GET['sl_no'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'EMS');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the query
    $stmt = $conn->prepare("SELECT * FROM receipts WHERE sl_no = ?");
    $stmt->bind_param("i", $sl_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $receipt = $result->fetch_assoc();

    if ($receipt) {
        // Print the receipt details
        echo "<div class='receipt'>";
        // Format the receipt details here as per your layout requirement
        echo "<p>SL No: " . htmlspecialchars($receipt['sl_no']) . "</p>";
        echo "<p>Voucher No: " . htmlspecialchars($receipt['voucher_no']) . "</p>";
        // ... Print other details ...
        echo "</div>";

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo "Receipt not found.";
    }
} else {
    echo "No receipt specified.";
}
?>

<style>
/* Add styles here for printing */
.receipt {
    /* Styles for the receipt */
}
@media print {
    /* Styles for print layout */
}
</style>

<script>
// Automatically trigger the print dialog after loading
window.onload = function() {
    window.print();
}
</script>
