<!-- admindashboard.php -->
<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding-top: 0px; /* Adjusted to give space for the navbar */
        }

        /* Main content styles */
        .main-content {
            margin-left: 250px; /* Same as the width of the sidebar */
            padding: 1em;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    

<div class="main-content">
    <h1>Admin Dashboard</h1>
    <p>Welcome to the admin dashboard where you can manage the application.</p>
    <!-- Your dashboard content here -->
</div>

</body>
</html>
