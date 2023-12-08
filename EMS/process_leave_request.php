<?php
// Database connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "EMS";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_SANITIZE_NUMBER_INT);
    $employee_name = filter_input(INPUT_POST, 'employee_name', FILTER_SANITIZE_STRING);
    $joining_date = filter_input(INPUT_POST, 'joining_date', FILTER_SANITIZE_STRING);
    $designation = filter_input(INPUT_POST, 'designation', FILTER_SANITIZE_STRING);
    $department = filter_input(INPUT_POST, 'department', FILTER_SANITIZE_STRING);
    // Assuming 'leave_type' is one of the fields in your form
    $leave_type = filter_input(INPUT_POST, 'leave_type', FILTER_SANITIZE_STRING);
    $start_date = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
    $end_date = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);
    $number_of_days = filter_input(INPUT_POST, 'number_of_days', FILTER_SANITIZE_NUMBER_INT);

    // // Validate required fields
    // if (empty($employee_id) || empty($leave_type) || empty($start_date) || empty($end_date) || empty($number_of_days)) {
    //     die("Error: All fields are required.");
    // }

    // Prepare SQL statement to insert leave request
    $sql = "INSERT INTO employee_leaves (employee_id, leave_type, start_date, end_date, number_of_days) VALUES (?, ?, ?, ?, ?)";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isssi", $employee_id, $leave_type, $start_date, $end_date, $number_of_days);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Leave request submitted successfully.";
        } else {
            echo "Error submitting leave request: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the connection
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
