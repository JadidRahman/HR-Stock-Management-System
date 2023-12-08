<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sidebar</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: wh;
        }

        /* Sidebar styles */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #34444C;
            overflow-x: hidden;
            padding-top: 20px;
        }

        /* .sidebar-header {
    background-color: #FD7B5E;
    padding: 20px;
    text-align: center;

} */

        .menu-label {
            color: grey;
            padding: 10px 20px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .menu-item {
            padding: 10px 20px;
            color: #818181;
            display: block;
            text-decoration: none;
            font-size: 16px;
        }

        .menu-item:hover,
        .menu-item.active {
            background-color: #ff5722;
            color: white;
        }

        .dropdown-container {
            display: none;
            background-color: #34444C;
            padding-left: 10px;
        }

        .fa-caret-down {
            float: right;
        }

        .sidebar-toggle {
            position: fixed;
            top: 10px;
            left: 250px;
            /* Start aligned with the sidebar width */
            z-index: 10;
            /* Ensure it's above other content */
            cursor: pointer;
            transition: left 0.3s;
            /* Smooth transition for moving toggle */
        }

        /* Collapsed sidebar styles */
        .sidebar.collapsed {
            width: 50px;
            /* Width of the collapsed sidebar */
        }

        .sidebar.collapsed .menu-item,
        .sidebar.collapsed .menu-label {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            /* adjust padding to match your design */
        }

        /* Hide the text for menu items when sidebar is collapsed */
        .sidebar.collapsed .menu-item span {
            display: none;
        }

        /* Adjust toggle button position when sidebar is collapsed */
        .sidebar.collapsed~.sidebar-toggle {
            left: 50px;
            /* Align with the width of the collapsed sidebar */
        }

        /* Ensure active menu-item text can still be visible */
        .sidebar .menu-item.active i::after {
            display: inline;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .menu-item,
            .menu-label {
                text-align: left;
            }

            .dropdown-container {
                padding-left: 20px;
            }

            /* Style for the toggle button */
            .sidebar-toggle {
                display: block;
                position: absolute;
                top: 10px;
                left: 10px;
                font-size: 20px;
                cursor: pointer;
            }

            /* Initially hide the sidebar */
            .sidebar {
                width: 0;
                overflow: hidden;
            }
        }
    </style>
</head>


<body>
    <div class="sidebar-toggle">☰</div>
    <div class="sidebar">
        <div class="sidebar-header">
            <center><img src="logo1.png" alt="Company Logo" style="max-width: 40%;"> <!-- Replace with your logo -->
            </center>
        </div>
        <p class="menu-label">Main</p>
        <a href="admindashboard.php" class="menu-item active"><i
                class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="#apps" class="menu-item"><i class="fas fa-th"></i><span>Apps</span></a>
        <!-- <p class="menu-label">Employees</p> -->
        <center/><button class="menu-item dropdown-btn"><i class="fas fa-users"></i><span>Employees</span><i
                class="fa fa-caret-down"></i></button>
        <div class="dropdown-container">
            <a href="allemployee.php" class="menu-item"><i class="fas fa-users"></i><span>All Employees</span></a>
            <a href="#" class="menu-item"><i class="fas fa-users "></i><span>Leaves</span></a>   
            <!-- Additional employee links -->
        </div>
        <!-- <p class="menu-label">HR</p> -->
        <button class="menu-item dropdown-btn"><i class="fas fa-magnet "></i><span>Accounts</span><i
                class="fa fa-caret-down"></i></button>
        <div class="dropdown-container">
            <a href="p.php" class="menu-item"><i class="fas fa-credit-card "></i><span>Expense Voucher</span></a>
            <a href="l.php" class="menu-item"><i class="fas fa-credit-card "></i><span>Money Receipt</span></a>
            <a href="viewl.php" class="menu-item"><i class="fas fa-credit-card "></i><span>All Money Receipt</span></a>
            <a href="accountsledger.php" class="menu-item"><i class="fas fa-credit-card  "></i><span>Accounts Ledger</span></a>
        </div>
        <button class="menu-item dropdown-btn"><i class='fas fa-balance-scale'></i><span>Stock</span><i
                class="fa fa-caret-down"></i></button>
        <div class="dropdown-container">
            <a href="product.php" class="menu-item"><i class='fas fa-box-tissue'></i><span>Product</span></a>
            <a href="invoice.php" class="menu-item"><i class='fas fa-money-check'></i><span>Sales Invoice</span></a>
            
        </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            var sidebarToggle = document.querySelector('.sidebar-toggle');
            var sidebar = document.querySelector('.sidebar');

            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                // Move the toggle button along with the sidebar
                sidebarToggle.style.left = sidebar.classList.contains('collapsed') ? '50px' : '250px';
            });

            // Handle dropdowns
            var dropdowns = document.getElementsByClassName("dropdown-btn");
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].addEventListener("click", function () {
                    this.classList.toggle("active");
                    var dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                    } else {
                        dropdownContent.style.display = "block";
                    }
                });
            }
        });

    </script>
</body>

</html>