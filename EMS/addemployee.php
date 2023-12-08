<?php
// Establish a connection to the database
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "EMS"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect post data
    $name = $conn->real_escape_string($_POST['name']);
    $employee_id = $conn->real_escape_string($_POST['employee_id']);
    $email = $conn->real_escape_string($_POST['email']);
    $mobile = $conn->real_escape_string($_POST['mobile']);
    $join_date = $conn->real_escape_string($_POST['join_date']);
    $designation = $conn->real_escape_string($_POST['designation']);
    $department = $conn->real_escape_string($_POST['department']);

    // Prepare an insert statement
    $sql = "INSERT INTO employees_table (name, employee_id, email, mobile, join_date, designation, department) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("sssssss", $name, $employee_id, $email, $mobile, $join_date, $designation, $department);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to allemployee.php with success message
            header("Location: allemployee.php?success=1");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>
