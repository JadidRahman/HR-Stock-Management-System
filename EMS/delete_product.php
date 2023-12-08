<?php
// Connection variables
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "EMS"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the serial number is set in the URL
if (isset($_GET['serial_number'])) {
    $serial_number = intval($_GET['serial_number']); // Sanitize the input

    // SQL query to delete the product
    $sql = "DELETE FROM products_table WHERE serial_number = ?";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $serial_number);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to product.php with success message
        header("Location: product.php?message=Product Deleted Successfully");
        exit();
    } else {
        // Redirect to product.php with error message
        header("Location: product.php?message=Error Deleting Product");
        exit();
    }
} else {
    // Redirect to product.php if serial_number is not set
    header("Location: product.php");
    exit();
}


?>