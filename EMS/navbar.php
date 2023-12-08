<!--navbar.php -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Navbar</title>
<style>
    /* Basic reset of styles */
    * {
        margin: 00;
        padding: 0;
        box-sizing: border-box;
    }

    /* Style the navbar */
    .navbar {
        background-color: #FD7B5E;
        overflow: hidden;
        font-family: Arial, sans-serif;
    }

    /* Navbar links */
    .navbar a {
        float: left;
        display: block;
        color: #f2f2f2;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
    }

    /* Right-aligned link items */
    .navbar a.right {
        float: right;
    }

    /* Dropdown container */
    .dropdown {
        float: right;
        overflow: hidden;
    }

    /* Dropdown button */
    .dropdown .dropbtn {
        font-size: 16px;    
        border: none;
        outline: none;
        color: white;
        padding: 14px 16px;
        background-color: inherit;
        font-family: inherit; /* Necessary for vertical align on mobile phones */
        margin: 0; /* Important for vertical align on mobile phones */
    }

    /* Add a red background color to navbar links on hover */
    .navbar a:hover, .dropdown:hover .dropbtn {
        background-color: red;
    }

    /* Dropdown content (hidden by default) */
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    /* Links inside the dropdown */
    .dropdown-content a {
        float: none;
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        text-align: left;
    }

    /* Add a grey background color to dropdown links on hover */
    .dropdown-content a:hover {
        background-color: #ddd;
    }

    /* Show the dropdown menu on hover */
    .dropdown:hover .dropdown-content {
        display: block;
    }
</style>
</head>
<body>

<div class="navbar">


  <!-- Dropdown for admin profile -->
  <div class="dropdown">
    <button class="dropbtn">Admin Profile 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="#profile">My Profile</a>
      <a href="logout.php">Logout</a>
    </div>
  </div> 
</div>

</body>
</html>
 