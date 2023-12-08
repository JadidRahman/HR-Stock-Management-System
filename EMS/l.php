<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Receipt</title>
    <style>
        .receipt-container {
            width: 877px;
            border: 1px solid #000;
            padding: 15px;
            margin: auto;
            background-color: #fff;
        }

        .header,
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header {
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            /* Adjust as needed */
            height: auto;
            background-image: url('logo.jpg');
            /* Replace with your logo path */
            background-size: contain;
            background-repeat: no-repeat;
        }

        .title {
            text-align: center;
            flex-grow: 1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            min-width: 10px;
            display: inline-block;
        }

        input[type="text"],
        input[type="checkbox"] {
            margin-right: 10px;
        }

        .signature-space {
            padding-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
        }

        .submit-btn {
            width: 100%;
            background-color: #4CAF50;
            border: none;
            padding: 10px;
            color: white;
            cursor: pointer;
        }

        .no-print {
            display: none;
        }

        @media print {
            body {
                width: auto;
                font: 10pt "Tahoma";
            }

            .receipt-container {
                width: 215mm;
                /* Adjust the width to fit within the printable area of A4 */
                border: 1px solid #000;
                padding: 15px;
                margin: auto;
                /* Automatically adjust left and right margins */
                background-color: #fff;
                box-sizing: border-box;
                /* Include padding in the container's width */
            }

            .header,
            .footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .header {
                margin-bottom: 20px;
            }

            .logo {
                width: 10px;
                /* Adjust as needed */
                height: auto;
                background-image: url('logo.jpg');
                /* Replace with your logo path */
                background-size: contain;
                background-repeat: no-repeat;
            }

            .title {
                text-align: center;
                flex-grow: 1;
            }

            .form-group {
                margin-top: 20px;
                margin-bottom: 20px;
            }

            label {
                min-width: 10px;
                display: inline-block;
            }

            input[type="text"],
            input[type="checkbox"] {
                margin-right: 10px;
                border: none;
            }

            .signature-space {
                padding-top: 25px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .signature-line {
                border-top: 1px solid #000;
                width: 200px;
                text-align: center;
            }

            .submit-btn {
                display: none;
            }

            .navbar,
            .col-md-3 {
                display: none;
            }

            .no-print {
                display: block;
                /* or 'display: table-row;' for table rows */
            }

            .generated-on,
            .footer-print {
                text-align: right;
                margin-top: 580px;
                font-size: 8px;
                /* Adjust as needed for your layout */
            }


        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/words-to-numbers/1.5.0/w2n.min.js"></script>

    <?php
    // Database credentials
    $host = 'localhost'; // Your database host
    $username = 'root'; // Your database username
    $password = ''; // Your database password
    $database = 'EMS'; // Your database name
    
    // Create a new MySQLi connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the latest SL. No. and Voucher No. from the database
    $query = "SELECT sl_no, voucher_no FROM receipts ORDER BY created_at DESC LIMIT 1";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        // Fetch data
        $row = $result->fetch_assoc();
        $current_sl_no = $row['sl_no'] + 1; // Increment for next receipt
        $current_voucher_no = $row['voucher_no'] + 1; // Increment for next voucher
    } else {
        // Default starting values
        $current_sl_no = 9001;
        $current_voucher_no = 1001;
    }
    ?>

</head>

<body>

    <?php include 'navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include 'sidebar.php'; ?>
            </div><br /><br /><br />
            <!-- Main content -->
            <div class="receipt-container">
                <div class="header">
                    <!-- Corrected the img src attribute -->
                    <img src="Safwah_Main_logo-removebg-preview.png" alt="Company Logo"
                        style="float: left; width: 200px; height: auto;" />
                    <div class="title">
                        <h2>MONEY RECEIPT</h2>
                    </div>
                    <div class="voucher-info">
                        Voucher No: <span id="voucher_no">
                            <?php echo $current_voucher_no; ?>
                        </span><br>
                        Date & Time: <span id="date_time">
                            <?php echo date("Y-m-d H:i:s"); ?>
                        </span>
                    </div>
                </div>
                <form action="submit_receipt.php" method="post">
                    <div class="body">
                        <div class="form-group">
                            <label>SL. No.:</label>
                            <span id="sl_no">
                                <?php echo $current_sl_no; ?>
                            </span>
                            <input type="hidden" name="sl_no" value="<?php echo $current_sl_no; ?>">
                            <input type="hidden" name="voucher_no" value="<?php echo $current_voucher_no; ?>">
                        </div>
                        <div class="form-group">
                            <label>Received With Thanks From:</label>
                            <input type="text" name="received_from" style="width: 600px;" />
                        </div>
                        <div class="form-group">
                            <label>Amount of Taka:</label>
                            <input type="text" id="amount" name="amount" onkeyup="convertToWords(this.value)" />
                            <label>In Words:</label>
                            <span id="amount_words"></span>
                        </div>
                        <!-- Payment method details -->
                        <div class="form-group payment-methods">
                            <input type="checkbox" id="cash" name="payment_method" value="cash" />
                            <label for="cash">By Cash</label>
                            <input type="checkbox" id="cheque" name="payment_method" value="cheque" />
                            <label for="cheque">By Cheque No.:</label>
                            <input type="text" id="cheque_no" name="cheque_no" />
                            <label>Bank:</label>
                            <input type="text" name="bank_name" />
                            <label>Date:</label>
                            <input type="date" name="transaction_date" />
                        </div>
                        <!-- MFS details -->
                        <div class="form-group mfs-details">
                            <input type="checkbox" id="mfs" name="payment_method" value="mfs" />
                            <label for="mfs">MFS</label><br>
                            <label>Sender No.:</label>
                            <input type="text" name="sender_no" style="width: 120px;" />
                            <label>Receiver No.:</label>
                            <input type="text" name="receiver_no" />
                            <label>Transaction No.:</label>
                            <input type="text" name="transaction_no" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>For The Purpose Of:</label>
                        <input type="text" name="purpose" style="width: 600px;" />
                        <!-- <label>Contact No:</label>
                <input type="text" name="contact_no" /> -->
                    </div>
                    <div class="signature-space">
                        <div class="signature-line">Received By</div>
                        <div class="signature-line">Authorized Signature</div>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="submit-btn" value="Submit Receipt">
                    </div>
                </form>
            </div>
            <script>
                function numberToIndianWords(number) {
                    const words = [
                        'Zero', 'One', 'Two', 'Three', 'Four',
                        'Five', 'Six', 'Seven', 'Eight', 'Nine',
                        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen',
                        'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
                    ];
                    const tens = [
                        '', '', 'Twenty', 'Thirty', 'Forty',
                        'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
                    ];

                    if (number < 20) {
                        return words[number];
                    } else if (number < 100) {
                        return tens[Math.floor(number / 10)] + (number % 10 !== 0 ? ' ' + words[number % 10] : '');
                    } else if (number < 1000) {
                        return words[Math.floor(number / 100)] + ' Hundred' + (number % 100 !== 0 ? ' ' + numberToIndianWords(number % 100) : '');
                    } else if (number < 100000) {
                        return numberToIndianWords(Math.floor(number / 1000)) + ' Thousand' + (number % 1000 !== 0 ? ' ' + numberToIndianWords(number % 1000) : '');
                    } else { // Up to 99,999
                        return numberToIndianWords(Math.floor(number / 100000)) + ' Lac' + (number % 100000 !== 0 ? ' ' + numberToIndianWords(number % 100000) : '');
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    document.getElementById('date_time').textContent = new Date().toLocaleString();

                    var amountInput = document.getElementById('amount');
                    amountInput.addEventListener('input', function (e) {
                        var amount = parseInt(e.target.value, 10);
                        if (!isNaN(amount)) {
                            var words = numberToIndianWords(amount);
                            document.getElementById('amount_words').textContent = words + ' Taka';
                            document.getElementById('total_amount').textContent = 'Taka: ' + numeral(amount).format('0,0');
                        } else {
                            document.getElementById('amount_words').textContent = '';
                            document.getElementById('total_amount').textContent = '';
                        }
                    });
                });
            </script>

</body>
<div class="footer-print no-print">
    This is a software-generated document and does not require a signature.
</div>

</html>