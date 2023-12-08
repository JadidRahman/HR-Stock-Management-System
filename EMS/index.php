<?php
// Include your database connection script here
require 'db.php';

$error = ''; // Initialize the error message variable

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assign variables from the form
    $email = $_POST['email'];
    $password = $_POST['password']; // This password should be verified using password_verify in a real application

    // SQL to check the user
    $sql = "SELECT * FROM admin WHERE email = ?";

    // Prepare and execute the query
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify the password
            if ($user['password'] === $password) { // Use password_verify in a real application
                // Set session variables and redirect to a new page
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: admindashboard.php'); // Redirect to a welcome or dashboard page
                exit();
            } else {
                // Invalid credentials
                $error = 'Invalid Credentials';
            }
        } else {
            // No user found
            $error = 'Invalid Credentials';
        }
        $stmt->close();
    } else {
        // SQL preparation failed
        $error = 'Login failed, please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <center>
        <title>Login</title>
        <style>
            /* Add your CSS here */
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }

            .container {
                width: 300px;
                text-align: center;
            }

            .logo {
                display: block;
                margin: 0 auto 20px;
                max-width: 250px;
                /* Smaller size for the logo */
                height: auto;
            }

            input[type="text"],
            input[type="password"],
            input[type="email"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #ddd;
            }

            input[type="submit"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                border: none;
                background-color: #FD7564;
                color: white;
                transition: background-color 0.3s ease;
                /* Transition effect for smooth color change */
            }

            input[type="submit"]:hover {
                background-color: #FD8954;
                /* Darker shade when hovered */
            }

            a {
                color: orange;
                text-decoration: none;
            }

            .error-message {
                color: red;
                background-image: url('cross-icon.png');
                /* Add your red cross icon here */
                background-repeat: no-repeat;
                background-position: left center;
                padding-left: 20px;
                /* Adjust the padding to fit your icon */
                margin-bottom: 10px;
            }
        </style>
</head>

<body>
    <div class="container">
        <!-- Company logo goes here -->
        <img src="logo.jpg" alt="Company Logo" class="logo">

        <h2>Login</h2>
        <form method="post" action="index.php"> <!-- Changed action to index.php -->
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
            <!-- Error message is now within the form and will only display after form submission -->
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        </form>
        <a href="register.php">Don't have an account yet? Register</a>
    </div>
</body>

</html>