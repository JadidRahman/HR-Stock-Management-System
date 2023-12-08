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

$success = false; // Initialize the success flag

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $serial_number = intval($_POST['serial_number']);
    $sku = $conn->real_escape_string($_POST['sku']);
    $product_type = $conn->real_escape_string($_POST['product_type']);
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $brand_name = $conn->real_escape_string($_POST['brand_name']);
    $qty = intval($_POST['qty']);
    $unit = $conn->real_escape_string($_POST['unit']);
    $purchaserate = floatval($_POST['purchaserate']);
    $mrp = floatval($_POST['mrp']);
    $expire_date = $conn->real_escape_string($_POST['expire_date']);
    $manufacture_date = $conn->real_escape_string($_POST['manufacture_date']);

    // SQL query to update data in products_table
    $sql = "UPDATE products_table SET 
                sku = '$sku',
                product_type = '$product_type', 
                product_name = '$product_name', 
                brand_name = '$brand_name', 
                qty = $qty, 
                unit = '$unit', 
                purchaserate = $purchaserate, 
                mrp = $mrp, 
                expire_date = '$expire_date', 
                manufacture_date = '$manufacture_date'
            WHERE serial_number = $serial_number";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        $success = true; // Set success flag
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Update Successful</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p><i class="fas fa-check-circle" style="color: green; font-size: 48px;"></i></p>
                    <p>Product updated successfully.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($success): ?>
            $(document).ready(function () {
                // Show the modal
                $('#successModal').modal('show');

                // Set a timeout to redirect after 2 seconds
                setTimeout(function () {
                    window.location.href = 'product.php'; // Redirect to product.php
                }, 200); // 200 milliseconds = 2 seconds
            });
        <?php endif; ?>
    </script>



</body>

</html>