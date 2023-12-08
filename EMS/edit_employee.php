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

// Function to show a message and redirect after delay
function redirect_with_delay($message, $message_type, $url, $delay = 100) {
    // Encode the message for JavaScript
    $encodedMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    echo "<script type='text/javascript'>
            alert('$encodedMessage');
            setTimeout(function() {
                window.location.href = '$url';
            }, $delay);
          </script>";
}

$message = null;
$message_type = null;

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect post data and sanitize
    $id = isset($_POST['id']) ? $conn->real_escape_string($_POST['id']) : '';
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $employee_id = isset($_POST['employee_id']) ? $conn->real_escape_string($_POST['employee_id']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $mobile = isset($_POST['mobile']) ? $conn->real_escape_string($_POST['mobile']) : '';
    $join_date = isset($_POST['join_date']) ? $conn->real_escape_string($_POST['join_date']) : '';
    $designation = isset($_POST['designation']) ? $conn->real_escape_string($_POST['designation']) : '';

    $sql = "UPDATE employees_table SET name=?, employee_id=?, email=?, mobile=?, join_date=?, designation=? WHERE id=?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssi", $name, $employee_id, $email, $mobile, $join_date, $designation, $id);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            $message = 'Employee updated successfully.';
            $message_type = 'success';
        } else {
            $message = "Error updating record: " . $stmt->error;
            $message_type = 'danger';
        }
        $stmt->close();
        $conn->close();
    } else {
        $message = "Error preparing statement: " . $conn->error;
        $message_type = 'danger';
        $conn->close();
    }
    // Call the function with a message and redirection instructions
    redirect_with_delay($message, $message_type, 'allemployee.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        #message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        }
    </style>
</head>
<body>



</body>
</html>
