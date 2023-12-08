<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Voucher</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
        }

        .logo-container {
            margin-bottom: 0px;
            /* Adjust the margin as needed */
        }

        #company-logo {
            max-width: 280px;
            /* Adjust the width as needed */
            max-height: 100px;
            /* Adjust the height as needed */
            margin: auto;
            /* Centers the logo in the div */
            display: block;
            /* Ensures the logo is centered within the div */
        }


        .content-container {
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 5px;
            margin-top: 40px;
            /* Adjust as per your layout's needs */
            margin-bottom: 20px;
            /* Adjust as per your layout's needs */
            margin-right: flex;
            /* Adjust as per your layout's needs */
            margin-left: 400px;
        }

        .no-print {
            display: none;
        }


        h4 {
            color: #333;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        /* Custom button styles */

        .btn-info {
            background-color: #17a2b8;
            /* Bootstrap info blue */
            border-color: #17a2b8;
            /* If you need additional styling, add it here */
        }

        .btn-success {
            background-color: #28a745;
            /* Bootstrap success green */
            border-color: #28a745;
            /* If you need additional styling, add it here */
        }

        /* Additional styles for alignment if needed */
        .button-group {
            display: flex;
            justify-content: flex-start;
            /* Aligns buttons to the left */
        }

        @media print {
    /* Hide sidebar, add row button, and submit button */
    .col-md-3, .col-sm-10 {
        display: none;
    }
    
    /* Adjust the content container for print */
    .content-container {
        margin-left: auto;
        margin-right: auto;
        max-width: 100%;
        box-shadow: none; /* Remove any shadow effects */
    }

    /* Ensure the body takes full width */
    body {
        width: 100%;
    }
    .no-print {
                display: block;
                /* or 'display: table-row;' for table rows */
            }
            .generated-on,
            .footer-print {
                text-align: right;
                font-size:8px;
                /* Adjust as needed for your layout */
            }        
}

    </style>
</head>

<body>


    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include 'sidebar.php'; ?>
            </div>
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="content-container">
                    <div class="logo-container text-center">
                        <img id="company-logo" src="Safwah_Main_logo-removebg-preview.png"
                            alt="Company Logo Placeholder">
                    </div>

                    <div class="title">
                        <center />
                        <h4>EXPENSE VOUCHER</h4>
                        <strong id="generatedOn">Generated On: <br><span id="dateTime"></span></strong>
                    </div>
                    <form action="voucher.php" method="post">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>

                        <!-- Dynamically added rows will go here -->
                        <div id="expense-rows">
                            <div class="form-row align-items-center">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Total:</label>
                            <input type="text" class="form-control" id="total" name="total" readonly>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="button" class="btn btn-info" onclick="addRow()">Add Row</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>

                        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
                        <script
                            src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
                        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                addRow(); // Adds the first row on page load
                            });

                            function addRow() {
                                const div = document.createElement('div');
                                div.className = 'form-row align-items-center';
                                div.innerHTML = `
    <div class="col">
    <label>Issue Date</label>
      <input type="date" class="form-control mb-2 date-input" name="date[]">
    </div>
    <div class="col">
    <label for="description">Description</label>
      <input type="text" class="form-control mb-2" name="description[]">
    </div>
    <div class="col">
    <label for="subtotal">Subtotal</label>
      <input type="number" class="form-control mb-2 subtotal" name="subtotal[]" onkeyup="calculateTotal()">
    </div>
  `;
                                document.getElementById('expense-rows').appendChild(div);
                                setDateToToday(); // Set the date of the newly added row
                            }

                            function setDateToToday() {
                                // Gets all date input elements with the class 'date-input'
                                const dateInputs = document.querySelectorAll('.date-input');
                                const today = new Date().toISOString().split('T')[0]; // Gets the current date in YYYY-MM-DD format
                                dateInputs.forEach(input => {
                                    input.value = today; // Sets the value of the date input to today's date
                                });
                            }

                            function calculateTotal() {
                                // Sum all the subtotal values
                                const subtotals = document.querySelectorAll('.subtotal');
                                let total = 0;
                                subtotals.forEach(subtotal => {
                                    total += parseFloat(subtotal.value) || 0;
                                });
                                document.getElementById('total').value = total.toFixed(2); // Sets the total input to the sum of subtotals
                            }

                            function updateGeneratedOn() {
    // Get the current time in UTC+0
    var now = new Date();

    // Convert it to Bangladesh Standard Time UTC+6
    var localTime = now.getTime();
    var localOffset = now.getTimezoneOffset() * 60000;
    var utc = localTime + localOffset;
    var bangladeshTime = utc + (3600000 * 6);

    // Create a new Date object with the Bangladesh time
    var bdTime = new Date(bangladeshTime);

    // Format the date and time in a readable format
    var options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
    var formattedDateTime = bdTime.toLocaleDateString('en-US', options) ;

    // Display the date and time in the page
    document.getElementById('dateTime').textContent = formattedDateTime;
}

// Update the date and time when the page loads
document.addEventListener('DOMContentLoaded', function () {
    updateGeneratedOn();
});


                        </script>

</body>
<div class="footer-print no-print">
                This is a software-generated document and does not require a signature.
            </div>

</html>