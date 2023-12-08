<?php
require 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assign variables from the form
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // This should be hashed with password_hash in a real application

    // SQL to insert new user
    $sql = "INSERT INTO admin (fullname, username, email, password) VALUES (?, ?, ?, ?)";

    // Prepare and execute the query
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ssss", $fullname, $username, $email, $password);
        $stmt->execute();
        // Redirect to login page or show a success message
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Add your CSS here */
        html, body {
        height: 100%;
        margin: 0;
        }

        body {
        display: flex;
        justify-content: center;
        align-items: center;
        }
        .container { width: 300px; margin: auto; }
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; }
        input[type="submit"] {
            width: 100%; padding: 10px; margin: 10px 0; border: none; background-color: orange; color: white; }
        a { color: orange; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <center><h2>Register</h2>
        <form method="post" action="register.php">
            <input type="text" name="fullname" placeholder="FullName" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Repeat Password" required>
            <input type="submit" value="Register">
        </form>
        <a href="index.php">Already have an account? Login</a>
    </div>
</body>
</html>
