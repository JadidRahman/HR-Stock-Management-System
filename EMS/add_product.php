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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $sku = $conn->real_escape_string($_POST['sku']);
    $product_type = $conn->real_escape_string($_POST['product_type']);
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $brand_name = $conn->real_escape_string($_POST['brand_name']);
    $qty = intval($_POST['qty']);
    $unit = $conn->real_escape_string($_POST['unit']);
    $purchaserate = floatval($_POST['purchaserate']);
    $mrp = floatval($_POST['mrp']);
    $expire_date = $conn->real_escape_string($_POST['expire_date']);
    $manufacture_date= $conn->real_escape_string($_POST['manufacture_date']);

    // SQL query to insert data into products_table
    $sql = "INSERT INTO products_table (sku, product_type, product_name, brand_name, qty, unit, purchaserate, mrp, expire_date, manufacture_date )
            VALUES ('$sku', '$product_type', '$product_name', '$brand_name', $qty, '$unit', $purchaserate, $mrp, '$expire_date', '$manufacture_date' )";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "New product added successfully";
        // Redirect or inform the user of success
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        // Handle error
    }
}

// Close connection
$conn->close();
?>
