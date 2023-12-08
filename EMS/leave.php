<?php
// Connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "EMS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Constants for leave entitlements
define('CASUAL_LEAVE_ENTITLEMENT', 10);
define('SICK_LEAVE_ENTITLEMENT', 10);
define('HALF_DAY_LEAVE_ENTITLEMENT', 2); // Assuming this is a per month entitlement


function calculateDays($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $interval = $start->diff($end);
    return $interval->days + 1; // +1 to include both start and end dates
}

// Add the calculateLeaveBalance function here
function calculateLeaveBalance($conn, $employee_id, $leave_type, $currentMonth = null)
{
    $currentYear = date("Y");

    // Handling special case for half-day leave
    if ($leave_type == 'Half Day Leave' && $currentMonth != null) {
        $entitlement = HALF_DAY_LEAVE_ENTITLEMENT;
        $taken = getLeaveTaken($conn, $employee_id, $leave_type, $currentYear, $currentMonth);
    } else {
        switch ($leave_type) {
            case 'Casual Leave':
                $entitlement = CASUAL_LEAVE_ENTITLEMENT;
                break;
            case 'Sick Leave':
                $entitlement = SICK_LEAVE_ENTITLEMENT;
                break;
            case 'Half Day Leave':
                $entitlement = HALF_DAY_LEAVE_ENTITLEMENT * 12; // Annualized
                break;
            default:
                $entitlement = 0;
        }
        $taken = getLeaveTaken($conn, $employee_id, $leave_type, $currentYear);
    }

    $balance = $entitlement - $taken;
    return ['entitlement' => $entitlement, 'taken' => $taken, 'balance' => $balance];
}

function getLeaveTaken($conn, $employee_id, $leave_type, $year, $month = null)
{
    if ($leave_type == 'Half Day Leave' && $month != null) {
        $sql = "SELECT SUM(number_of_days) AS total FROM employee_leaves WHERE employee_id = ? AND leave_type = ? AND YEAR(start_date) = ? AND MONTH(start_date) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isii", $employee_id, $leave_type, $year, $month);
    } else {
        $sql = "SELECT SUM(number_of_days) AS total FROM employee_leaves WHERE employee_id = ? AND leave_type = ? AND YEAR(start_date) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $employee_id, $leave_type, $year);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?: 0;
}


if (isset($_GET['id'])) {
    $employee_id = $_GET['id']; // The ID from the URL

    // Prepare the statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT name, join_date, designation, department FROM employees_table WHERE id = ?");
    $stmt->bind_param("i", $employee_id); // 'i' indicates the parameter is an integer
    $stmt->execute();

    // Bind the results to variables
    $stmt->bind_result($employee_name, $joining_date, $designation, $department);

    // Fetch the data
    if ($stmt->fetch()) {
        // Format the joining_date to match the input date format
        $joining_date = date('Y-m-d', strtotime($joining_date));
    } else {
        echo "No employee found with ID: $employee_id";
        // Initialize the variables to prevent errors
        $employee_name = $joining_date = $designation = $department = "";
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Employee ID parameter is missing in the URL.";
    // Initialize the variables to prevent errors
    $employee_name = $joining_date = $designation = $department = "";
}

date_default_timezone_set('Asia/Dhaka'); // Change this to your local timezone

// Get the current date and time in 'Y-m-d H:i:s' format
$application_datetime = date('Y-m-d H:i:s');

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        *,
        *::after,
        *::before {
            margin: 0;
            padding: 0;
            list-style: none;
            box-sizing: border-box;
        }

        .logo {
            max-width: 200px;
            /* Or any other size */
            height: auto;
            /* To maintain aspect ratio */
            float: left;
            /* Aligns the logo to the left */
            margin-left: 85px;

        }

        /* Header style adjustment */
        .header {
            margin: 30px 0;
            /* Top and bottom margin */
            display: flex;
            /* Use flexbox for alignment */
            align-items: center;
            /* Align items vertically */
        }

        .header p {
            margin-left: 10px;
            /* Add some space between logo and text */
            font-size: 12px;
        }

        .container {
            display: block;
            justify-content: center;
            align-items: center;
        }

        table,
        tr,
        td,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            margin: 20px;
            padding: 20px;
            text-transform: uppercase;
            font-size: 12px;
        }

        table {
            width: 90%;
        }

        th {

            background: #ddd;
        }

        .signature td:first-child {
            font-size: 13px;
        }

        .signature td:last-child {
            font-size: 10px;
            padding-top: 0;
            padding-left: 200px;
            margin-top: 0;
        }

        .footer th {
            background: white;
            padding: 5px 15px;
            font-size: 12px;
        }

        .footer td {
            padding: 35px 15px;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: 100%;
            border: none;
            outline: none;
            background-color: transparent;
            font-size: inherit;
            font-family: inherit;
            /* Adjust padding as needed to align with the rest of the form */
            padding: 1px 0;
            /* Add more styles as needed to match the look of the form */
        }

        td[contenteditable] {
            border: 1px dashed #000000;
            /* You can remove this if you don't want to show any indication */
            cursor: text;
            min-width: 100px;
            /* Minimum width */
            min-height: 20px;
            /* Minimum height */
            /* Add other styles as needed */
        }

        @media print {

            /* Apply margin to the body to set the print margins */
            body {
                margin: 1mm;
                /* Adjust the margin as needed */
                width: auto;
            }

            .logo {
                max-width: 300px;
                /* Or any other size */
                height: auto;
                /* To maintain aspect ratio */
                float: left;
                /* Aligns the logo to the left */
                margin-left: 7px;
            }

            /* Adjust the table layout */
            table {
                border: 1px solid black;
                border-collapse: collapse;
                margin: 5mm auto;
                /* Set uniform top and bottom margins for each table */
                padding: 0;
                width: 100%;
                /* Make tables use full width minus the margin */
                page-break-inside: avoid;
                /* Avoid breaking tables across pages */
                font-size: 8px;
                /* Smaller font size to fit content */
            }

            /* Adjust the table header and data cells */
            th,
            td {
                border: 1px solid black;
                text-transform: uppercase;
                padding: 2mm;
                /* Adjust padding to manage space, ensuring content fits */
            }

            /* Adjust signature and date font size */
            .signature td {
                font-size: 10px;
                /* Increased font size */
                padding: 6mm;
                /* Increased padding */
            }

            /* Make the header consistent across all tables */
            th {
                background: #ddd;
                /* Example background color */
                text-transform: uppercase;
                padding: 2mm;
                /* Adjust padding to manage space, ensuring content fits */
                font-size: 10px;
                /* Match the font size of the second table header */
            }

            /* Increase the size of input fields */
            input[type="text"],
            input[type="date"],
            input[type="number"] {
                font-size: 12px;
                /* Increased font size */
                padding: 2mm;
                /* Increased padding */
            }

            /* Adjust footer table spacing */
            .footer {
                margin-top: 5mm;
                /* Adjust if necessary to align with other table margins */
                margin-bottom: 0;
                /* Remove extra space at the bottom if not needed */
            }

            /* Match the font size for all table rows */
            td {
                font-size: 10px;
                /* This sets the font size for all cells, adjust as necessary */
            }
        }
    </style>
</head>

<body>
    <center>
        <div class="container">
            <header>
                <div class="header">
                    <img src="Safwah_Main_logo-removebg-preview.png" alt="Safwah Limited Logo" class="logo">
                </div>
            </header>
            <div class="leave_request">
                <form action="process_leave_request.php" method="post">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">Leave Request Form</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Date & Time of Application: <input type="text" name="application_datetime"
                                        value="<?php echo $application_datetime; ?>" class="forminput" readonly></td>
                                <td>Name of Employee: <input type="text" name="employee_name"
                                        value="<?php echo $employee_name; ?>" class="forminput" readonly></td>
                            </tr>
                            <tr>
                                <td>Joining Date: <input type="text" name="joining_date"
                                        value="<?php echo $joining_date; ?>" class="forminput" readonly></td>
                                <td>Employee ID: <input type="text" name="employee_id"
                                        value="<?php echo $employee_id; ?>" class="forminput" readonly></td>
                            </tr>
                            <tr>
                                <td>Designation: <input type="text" name="designation"
                                        value="<?php echo $designation; ?>" class="forminput" readonly></td>
                                <td>Department: <input type="text" name="department" value="<?php echo $department; ?>"
                                        class="forminput" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="leave_application">
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="4">Leave Application</th>
                                    <th colspan="3">Remaining Entitlement</th>
                                </tr>
                                <tr>
                                    <th class="th_1">Leave Type</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>No. of Days</th>
                                    <th>Entitlement</th>
                                    <th>Taken</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Repeat this pattern for each leave type... -->
                                <tr>
                                    <td>Casual Leave</td>
                                    <td><input type="date" name="casual_leave_start"></td>
                                    <td><input type="date" name="casual_leave_end"></td>
                                    <td><input type="number" name="casual_leave_days"></td>
                                    <td><input type="number" name="casual_leave_entitlement" value=10></td>
                                    <td><input type="number" name="casual_leave_taken"></td>
                                    <td><input type="number" name="casual_leave_balance"></td>
                                </tr>
                                <tr>
                                    <td>Sick Leave</td>
                                    <td><input type="date" name="sick_leave_start"></td>
                                    <td><input type="date" name="sick_leave_end"></td>
                                    <td><input type="number" name="sick_leave_days"></td>
                                    <td><input type="number" name="sick_leave_entitlement" value=10></td>
                                    <td><input type="number" name="sick_leave_taken"></td>
                                    <td><input type="number" name="sick_leave_balance"></td>
                                </tr>
                                <tr>
                                    <td>Half Day Leave</td>
                                    <td><input type="date" name="half_day_leave_start"></td>
                                    <td><input type="date" name="half_day_leave_end"></td>
                                    <td><input type="number" name="half_day_leave_days"></td>
                                    <td><input type="number" name="half_day_leave_entitlement" value=2></td>
                                    <td><input type="number" name="half_day_leave_taken"></td>
                                    <td><input type="number" name="half_day_leave_balance"></td>
                                </tr>
                                <tr>
                                    <td>Leave Without Pay</td>
                                    <td><input type="date" name="leave_without_pay_start"></td>
                                    <td><input type="date" name="leave_without_pay_end"></td>
                                    <td><input type="number" name="leave_without_pay_days"></td>
                                    <td><input type="number" name="leave_without_pay_entitlement"></td>
                                    <td><input type="number" name="leave_without_pay_taken"></td>
                                    <td><input type="number" name="leave_without_pay_balance"></td>
                                </tr>
                                <!-- <tr>
                            <transliterator_list_ids> highlight_string </transliterator_list_ids> -->
                                <tr>
                                    <td>Reason for leave</td>
                                    <td colspan="6"><input type="text" name="reason_for_leave"></td>
                                </tr>
                                <tr>
                                    <td>Address & Contact During Leave</td>
                                    <td colspan="6"><input type="text" name="contact_during_leave"></td>
                                </tr>
                                <tr>
                                    <td>Concern Person During Leave</td>
                                    <td colspan="6"><input type="text" name="concern_person_during_leave"></td>
                                </tr>
                                <tr class="signature">
                                    <td>Signature & date</td>
                                    <td colspan="6">I wish to apply for leave as stated above</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
            <footer>
                <div class="footer">
                    <table>
                        <thead>
                            <tr>
                                <th>Comments or spacial instructoin</th>
                                <th>checked by</th>
                                <th>approved by</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </footer>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            $(document).ready(function () {
                // Set the Date of Application field to the current date
                var today = new Date().toISOString().split('T')[0];
                $('input[name="application_date"]').val(today);

                // Function to fetch and populate employee data
                function fetchEmployeeData(employeeId) {
                    $.ajax({
                        url: 'leave.php', // The script to call to get employee data
                        type: 'POST',
                        dataType: 'json',
                        data: { employee_id: employeeId },
                        success: function (response) {
                            if (response.status === 'success') {
                                // Populate the form fields with the response data
                                $('input[name="employee_name"]').val(response.data.name);
                                $('input[name="joining_date"]').val(response.data.join_date);
                                $('input[name="employee_id"]').val(response.data.employee_id);
                                $('input[name="designation"]').val(response.data.designation);
                                $('input[name="department"]').val(response.data.department);
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            alert("An error occurred while fetching employee data: " + error);
                        }
                    });
                }

                // Trigger data fetch when the Employee ID field loses focus
                $('input[name="employee_id"]').on('blur', function () {
                    var employeeId = $(this).val();
                    if (employeeId) {
                        fetchEmployeeData(employeeId);
                    }
                });

                function calculateDaysBetweenDates(startDate, endDate) {
                    var start = new Date(startDate);
                    var end = new Date(endDate);
                    return (end - start) / (1000 * 60 * 60 * 24) + 1;
                }

                // Function to update leave balance based on leave type and taken days
                function updateLeaveBalance(leaveType, takenDays) {
                    var entitlementInput = $('input[name="' + leaveType.toLowerCase().replace(/\s/g, '_') + '_entitlement"]');
                    var takenInput = $('input[name="' + leaveType.toLowerCase().replace(/\s/g, '_') + '_taken"]');
                    var balanceInput = $('input[name="' + leaveType.toLowerCase().replace(/\s/g, '_') + '_balance"]');

                    var entitlement = parseInt(entitlementInput.val());
                    var taken = parseInt(takenInput.val()) + takenDays;
                    var balance = entitlement - taken;

                    balanceInput.val(balance);
                }
                $('input[type="date"][name$="_start"], input[type="date"][name$="_end"]').on('change', function () {
                    var row = $(this).closest('tr');
                    var startDate = row.find('input[name$="_start"]').val();
                    var endDate = row.find('input[name$="_end"]').val();
                    if (startDate && endDate) {
                        var days = calculateDaysBetweenDates(startDate, endDate);
                        row.find('input[name$="_days"]').val(days);

                        var leaveType = row.find('td:first').text().trim();
                        updateLeaveBalance(leaveType, days);
                    }
                });
            });


        </script>
        <!-- Add the submit button here -->
        <div style="text-align: center; margin-top: 20px;">
            <input type="submit" value="Submit Leave Request"
                style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
        </div>
        </form>

</body>

</html>