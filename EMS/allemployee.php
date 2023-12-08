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
// Get the search term if it exists
$searchTerm = $_GET['search'] ?? '';
// Determine the number of entries per page
$entriesPerPage = isset($_GET['entries']) ? (int) $_GET['entries'] : 10; // Default to 10 entries per page if not set
// Determine the current page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$startFrom = ($page - 1) * $entriesPerPage;
// Secure the inputs
$searchTermLike = "%" . $conn->real_escape_string($searchTerm) . "%";
// Query to select employees based on the search term and pagination
$query = "SELECT * FROM employees_table WHERE CONCAT(name, employee_id, email, mobile, join_date, designation, department) LIKE ? LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $searchTermLike, $startFrom, $entriesPerPage);
$stmt->execute();
$employees = $stmt->get_result();
// Query to get the total number of entries for pagination
$totalEntriesQuery = "SELECT COUNT(*) as count FROM employees_table WHERE CONCAT(name, employee_id, email, mobile, join_date, designation, department) LIKE ?";
$totalStmt = $conn->prepare($totalEntriesQuery);
$totalStmt->bind_param("s", $searchTermLike);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalEntries = $totalRow['count'];
// Calculate the total number of pages
$totalPages = ceil($totalEntries / $entriesPerPage);
// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <style>
        /* Custom CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            padding: 0;
            /* Remove padding if it exists */
        }

        /* #sidebar {
            position: fixed;
            /* Fixed position for sidebar */
        top: 0;
        left: 0;
        bottom: 0;
        width: 250px;
        /* Sidebar width */
        background-color: #f8f9fa;
        /* Sidebar background color */
        overflow-y: auto;
        /* Enable scroll if content is too long */
        }

        #main-content {
            margin-left: 250px;
            /* Same as sidebar width */
            padding: 1rem;
        }

        */ .content {
            display: flex;
            justify-content: center;
            /* Center content */
        }

        .pagination {
            justify-content: center;
        }

        .search-bar input[type=text] {
            margin-right: 10px;
        }

        .btn-group {
            display: flex;
        }

        .btn-group .btn {
            margin-right: 5px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            /* Adjust sidebar for mobile view */
            #sidebar {
                width: 100%;
                position: auto;
            }

            #main-content {
                margin-left: 0;
            }

            /* Make table horizontally scrollable on small screens */
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }


        .modal-header,
        .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-dialog {
            max-width: 800px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .overflow-btn {
            border: none;
            background: orange;
            font-size: 24px;
            /* Adjust size as needed */
            vertical-align: middle;
            line-height: 1;
            /* Aligns the button vertically */
        }

        .overflow-btn:focus {
            outline: none;
            box-shadow: none;
        }

        .dropdown-menu {
            min-width: 0;
            /* Override default min-width */
            padding: 0;
            /* Remove padding if you don't need it */
        }

        /* Optional: Add some hover effect for the dots button */
        .overflow-btn:hover {
            color: #555;
            /* Change the color on hover */
        }

        /* Ensure that the dropdown menu items are spaced nicely */
        .dropdown-item {
            padding: 10px;
            /* Adjust padding as necessary */
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <!-- Sidebar -->
        <div id="sidebar">
            <?php include 'sidebar.php'; ?>
            
        </div>
        <!-- Main content column -->
        <div class="col-md-10 offset-md-2" id="main-content">

            <!-- Employee dashboard content -->
            <div class="content col-md-9 col-xl-10">
                <div class="container-fluid">
                    <!-- The rest of your employee dashboard content goes here -->
                    <h1>Employee</h1>
                    <p>Dashboard / Employee</p>

                    <div class="top-bar">
                        <!-- Search form -->
                        <div class="search-bar">
                            <form action="" method="get">
                                <input type="text" name="search" placeholder="Search..."
                                    value="<?php echo htmlspecialchars($searchTerm); ?>">
                                <input type="submit" value="Search" class="btn btn-primary">
                            </form>

                            <!-- Show entries selection -->
                            <form action="" method="get">
                                <label for="show-entries">Show</label>
                                <select name="entries" id="show-entries" onchange="this.form.submit()">
                                    <option value="10" <?php echo $entriesPerPage == 10 ? 'selected' : ''; ?>>10</option>
                                    <option value="25" <?php echo $entriesPerPage == 25 ? 'selected' : ''; ?>>25</option>
                                    <option value="50" <?php echo $entriesPerPage == 50 ? 'selected' : ''; ?>>50</option>
                                    <option value="100" <?php echo $entriesPerPage == 100 ? 'selected' : ''; ?>>100
                                    </option>
                                </select>
                            </form>
                        </div>

                        <!-- Add Employee Button -->
                        <div>
                            <button id="myBtn" class="btn btn-success my-2">+ Add Employee</button>

                            <!-- The Modal -->
                            <div id="myModal" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Add New Employee</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="addemployee.php" method="post">
                                                <!-- Form fields for adding a new employee -->
                                                <div class="form-group">
                                                    <label for="name">Name:</label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="employee_id">Employee ID:</label>
                                                    <input type="text" class="form-control" id="employee_id"
                                                        name="employee_id" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email:</label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mobile">Mobile:</label>
                                                    <input type="tel" class="form-control" id="mobile" name="mobile"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="join_date">Join Date:</label>
                                                    <input type="date" class="form-control" id="join_date"
                                                        name="join_date" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="designation">Designation:</label>
                                                    <input type="text" class="form-control" id="designation"
                                                        name="designation" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="department">Department:</label>
                                                    <input type="text" class="form-control" id="department"
                                                        name="department" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Add Employee</button>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Employee Modal -->
                            <div id="editEmployeeModal" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit Employee</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="editEmployeeForm" action="edit_employee.php" method="post">
                                                <!-- The form fields will be dynamically filled with JavaScript -->
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                // Function to open the edit employee modal and populate it with the selected employee's data
                                function editEmployee(employeeData) {
                                    // Here we populate the form with the employee's data
                                    var formHtml = `
        <input type="hidden" name="id" value="${employeeData.id}">
        <div class="form-group">
            <label for="edit_name">Name:</label>
            <input type="text" class="form-control" id="edit_name" name="name" required value="${employeeData.name}">
        </div>
        <div class="form-group">
            <label for="edit_employee_id">Employee ID:</label>
            <input type="text" class="form-control" id="edit_employee_id" name="employee_id" required value="${employeeData.employee_id}">
        </div>
        <div class="form-group">
            <label for="edit_email">Email:</label>
            <input type="email" class="form-control" id="edit_email" name="email" required value="${employeeData.email}">
        </div>
        <div class="form-group">
            <label for="edit_mobile">Mobile:</label>
            <input type="tel" class="form-control" id="edit_mobile" name="mobile" required value="${employeeData.mobile}">
        </div>
        <div class="form-group">
            <label for="edit_join_date">Join Date:</label>
            <input type="date" class="form-control" id="edit_join_date" name="join_date" required value="${employeeData.join_date}">
        </div>
        <div class="form-group">
            <label for="edit_designation">Designation:</label>
            <input type="text" class="form-control" id="edit_designation" name="designation" required value="${employeeData.designation}">
        </div>
        <div class="form-group">
            <label for="edit_department">Department:</label>
            <input type="text" class="form-control" id="edit_department" name="department" required value="${employeeData.department}">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Employee</button>
        </div>
    `;

                                    // Set the form HTML
                                    $('#editEmployeeForm').html(formHtml);

                                    // Show the modal
                                    $('#editEmployeeModal').modal('show');
                                }
                            </script>
                            </form>
                        </div>

                    </div>
                </div>
            </div>


            <!-- Table of employees -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Employee ID</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Join Date</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($employees->num_rows > 0): ?>
                            <?php while ($employee = $employees->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($employee['name']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($employee['employee_id']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($employee['email']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($employee['mobile']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($employee['join_date']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($employee['designation']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($employee['department']); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="overflow-btn" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <!-- Inside the PHP while loop -->
                                                <a class="dropdown-item" href="#"
                                                    onclick="editEmployee(<?php echo htmlspecialchars(json_encode($employee)); ?>)">Edit</a>

                                                <a class="dropdown-item"
                                                    href="delete_employee.php?id=<?php echo $employee['id']; ?>">Delete</a>
                                                <!-- <a class="dropdown-item"
                                                    href="ledger_employee.php?id=<?php echo $employee['id']; ?>">Ledger</a> -->
                                                <a class="dropdown-item"
                                                    href="leave.php?id=<?php echo $employee['id']; ?>">Leave</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No employees found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination Links -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item <?php if ($page <= 1) {
                            echo 'disabled';
                        } ?>">
                            <a class="page-link"
                                href="?page=<?php echo $page - 1; ?>&entries=<?php echo $entriesPerPage; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>"
                                aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($page == $i) {
                                echo 'active';
                            } ?>">
                                <a class="page-link"
                                    href="?page=<?php echo $i; ?>&entries=<?php echo $entriesPerPage; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php if ($page >= $totalPages) {
                            echo 'disabled';
                        } ?>">
                            <a class="page-link"
                                href="?page=<?php echo $page + 1; ?>&entries=<?php echo $entriesPerPage; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>"
                                aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.6/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

            <script>
                $(document).ready(function () {
                    $('#sidebarToggle').on('click', function () {
                        $('#sidebar').toggleClass('active');
                    });
                });
                // Get the modal
                var modal = $('#myModal');

                // Get the button that opens the modal
                var btn = $("#myBtn");

                // When the user clicks the button, open the modal 
                btn.on('click', function () {
                    modal.modal('show');

                });
            </script>
</body>

</html>