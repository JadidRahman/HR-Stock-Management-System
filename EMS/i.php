<?php
// Connection variables
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "EMS"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the default timezone to Bangladesh time (GMT+6)
date_default_timezone_set('Asia/Dhaka');
$sales_date = date('Y-m-d h:i:s A'); // Set the sales date before form processing

function getNextInvoiceNumber($conn)
{
    $result = $conn->query("SELECT MAX(InvoiceNumber) as last_invoice FROM invoices");
    $row = $result->fetch_assoc();
    $lastInvoiceNumber = $row['last_invoice'];
    return ($lastInvoiceNumber === null) ? 4001 : $lastInvoiceNumber + 1;
}

$nextInvoiceNumber = getNextInvoiceNumber($conn);

if (isset($_POST['submit_invoice'])) {
    // Extract and sanitize input data
    $CustomerName = $conn->real_escape_string($_POST["CustomerName"] ?? '');
    $CustomerAddress = $conn->real_escape_string($_POST["CustomerAddress"] ?? '');
    $Contact = $conn->real_escape_string($_POST["Contact"] ?? '');
    $SalesPerson = $conn->real_escape_string($_POST["SalesPerson"] ?? '');
    $DeliveryAddress = $conn->real_escape_string($_POST["DeliveryAddress"] ?? '');
    $DeliveryInstruction = $conn->real_escape_string($_POST["DeliveryInstruction"] ?? '');
    $TotalBill = $conn->real_escape_string($_POST["TotalBill"] ?? '');
    $InWords = $conn->real_escape_string($_POST["InWords"] ?? '');
    $Note = $conn->real_escape_string($_POST["Note"] ?? '');


    // Prepare the insert statement for the invoice
    // Example PHP code for handling SKU in the backend
    $stmt = $conn->prepare("INSERT INTO invoice_items (InvoiceID, SerialNumber, SKU, Product, Quantity, Unit, Rate, Discount, Amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $item_stmt->bind_param("iisiiddd", $invoice_id, $sl_count, $sku, $product, $quantity, $unit, $rate, $discount, $amount);


    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "New invoice created successfully";
        $invoice_id = $conn->insert_id;

        // Process each item in the invoice
        $sl_count = 1;
        foreach ($_POST['items'] as $item) {
            $product = $conn->real_escape_string($item['product'] ?? '');
            $quantity = $item['quantity'] ?? 0;
            $unit = $conn->real_escape_string($item['unit'] ?? '');
            $rate = $item['rate'] ?? 0.0;
            $discount = $item['discount'] ?? 0.0;
            $amount = $item['amount'] ?? 0.0;

            // Prepare the insert statement for each item
            $item_stmt = $conn->prepare("INSERT INTO invoice_items (InvoiceID, SerialNumber, Product, Quantity, Unit, Rate, Discount, Amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $item_stmt->bind_param("iisiiddd", $invoice_id, $sl_count, $product, $quantity, $unit, $rate, $discount, $amount);

            // Execute the statement and check for success
            if (!$item_stmt->execute()) {
                echo "Error: " . $item_stmt->error;
            } else {
                $sl_count++;
            }
            $item_stmt->close();
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

    // Redirect to avoid resubmission
    header('Location: i.php');
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <style>
        /* .invoice-header {
            display: grid;
            grid-template-columns: 6fr 1fr 5fr;
            padding-bottom: 15px;
        } */

        .print-logo img {
            max-width: 100%;
            /* Ensures the image is never more than 100% of its container */
            height: auto;
            /* Keeps the aspect ratio of the image */
            width: 20%;
            /* You can use a percentage to make it responsive */
            display: block;
            margin: 0 auto 20px 10px;
            padding-top: 20px;
        }

        .header-grid-item {
            padding: 5px;
            font-size: 12px;
            border-radius: 10px;
        }

        .header-left {
            text-align: left;
        }

        .header-right {
            background: black;
            color: white;
        }

        .grid-div {
            display: grid;
            grid-template-columns: 6fr 1fr 5.1fr;
            padding-bottom: 10px;
        }

        .grid-item {
            border: .5px solid rgba(0, 0, 0, 0.8);
            padding: 15px;
            font-size: 8px;
            border-radius: 10px;
        }

        .grid-item-2 {
            border: 0px solid;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
        }

        .invoice-footer {
            text-align: left;
        }

        .invoice-body table {
            width: calc(100% - 40px);
            /* Adjust the 40px to increase or decrease the total margin */
            margin-left: auto;
            margin-right: auto;
        }



        /* Adjust column widths */
        .sl-column {
            width: 3%;
            /* Example width, adjust as needed */
        }

        .sku-column {
            width: 5%;
            /* Example width, adjust as needed */
        }

        .product-column td {
            min-height: 80px;
            /* Adjust as needed to fit 3-4 lines */
            word-wrap: break-word;
            vertical-align: top;
            /* Align content to the top of the cell */
        }

        /* Optional: Set a specific height if you want exactly 4 lines of text, 
       assuming your line-height is 15px */
        .product-column td {
            height: 60px;
            /* 4 lines at 15px line-height */
            line-height: 15px;
            /* Adjust line-height as needed */
        }

        .product-column {
            white-space: normal;
            /* Overrides nowrap if set elsewhere */
            word-wrap: break-word;
            /* Allows long words to be broken and wrapped to the next line */
        }

        .qty-column,
        .unit-column,
        .rate-column,
        .discount-column {
            width: 6%;
            /* Example width, adjust as needed */
        }

        .amount-column {
            width: 15%;
            /* Example width, adjust as needed */
        }


        #inWords,
        #note {
            font-size: 10px;
            /* Set the desired font size */
        }

        .address-block {
            text-align: right;
            /* Align text to the right */
            position: absolute;
            /* Position the block absolutely within its parent */
            top: 10px;
            /* 10px from the top of the page */
            right: 10px;
            /* 10px from the right of the page */
            font-size: 8px;
            /* Smaller font size */
        }

        .line {
            display: inline-block;
            min-width: 300px;
            /* Or as much width as you need */
            border-bottom: 1px solid #000;
            /* Create the bottom border line */
        }




        @media print {
            body {
                margin-top: -10mm;
                padding: 0;
                font-family: Arial, sans-serif;
            }




            /* Adjust column widths */
            .sl-column {
                width: 3%;
                /* Example width, adjust as needed */
            }

            .sku-column {
                width: 5%;
                /* Example width, adjust as needed */
            }

            .product-column td {
                min-height: 80px;
                /* Adjust as needed to fit 3-4 lines */
                word-wrap: break-word;
                vertical-align: top;
                /* Align content to the top of the cell */
            }

            /* Optional: Set a specific height if you want exactly 4 lines of text, 
       assuming your line-height is 15px */
            .product-column td {
                height: 60px;
                /* 4 lines at 15px line-height */
                line-height: 15px;
                /* Adjust line-height as needed */
            }

            .product-column {
                white-space: normal;
                /* Overrides nowrap if set elsewhere */
                word-wrap: break-word;
                /* Allows long words to be broken and wrapped to the next line */
            }

            .qty-column,
            .unit-column,
            .rate-column,
            .discount-column {
                width: 7%;
                /* Example width, adjust as needed */
            }

            .amount-column {
                width: 15%;
                /* Example width, adjust as needed */
            }

            .print-logo img {
                max-width: 200px;
                margin-top: 17px;
                /* Smaller logo for print */
            }



            table {
                width: 100%;
                max-width: 100%;
                table-layout: fixed;
                /* Fixed table layout to honor cell widths */
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid black;
                padding: 5px;
                font-size: 8px;
                /* Reduce font size */
                overflow: hidden;
                /* Prevent content from overflowing */
                word-wrap: break-word;
                /* Ensure long words are wrapped */
                min-height: 60px;
                /* Adjust based on your needs and line height */
                vertical-align: top;
                /* Align content to the top */
            }

            .invoice-body {
                transform: scale(1.0);
                transform-origin: top left;
            }

            .editable {
                border: none;
                background-color: transparent;
            }

            button {
                display: none;
                /* Hide buttons */
            }

            .invoice-container {
                margin-top: -20px;
                /* Adjust the value as needed to move up the content */
            }

            #inWords,
            #note {
                font-size: 10px;
                /* Adjust the font size as desired */
                border: 1px solid #000;
                /* Add a border to make the box visible */
                padding: 2px;
                /* Add some padding inside the box */
                display: block;
                /* Ensure the element is treated as a block for proper box rendering */
                margin-bottom: 5px;
                /* Add some space below the box */
                min-height: 20px;
                /* Ensure the box has a minimum height */
            }

            /* Make sure borders are visible for all inputs and editable areas */
            input[type="text"],
            input[type="number"],
            .editable {
                border: none;
                /* Ensure the border is solid and visible */
            }

            .editable {
                border: none;
                /* Ensure no borders are printed */
                background-color: transparent;
            }

            .line {
                border-bottom: 1px solid #000;
                /* Ensure the line is printed */
                width: auto;
                /* Adjust if necessary */
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="print-logo">
            <img src="Safwah_Main_logo-removebg-preview.png" alt="Company Logo" />
            <div class="address-block">
                <small>
                    <p>Confidence Center, Shajadpur,</p>
                </small>
                <small>
                    <p>Gulshan-2, Dhaka-1212,</p>
                </small>
                <small>
                    <p>Dhaka Division, Bangladesh</p>
                </small>
            </div>
        </div>

        <div class="header-center header-grid-item">
            <center>
                <h1>INVOICE</h1>
            </center>
        </div>
    </div>

    <form action="i.php" method="post" id="invoiceForm">
        <div class="grid-div">
            <div class="left-div grid-item">
                <p>INVOICE NO.: <span class="readonly" id="invoiceNumber">
                        <?php echo $nextInvoiceNumber; ?>
                    </span></p>
                <input type="hidden" name="InvoiceNumber" value="<?php echo $nextInvoiceNumber; ?>">
                <p>CUSTOMER NAME: <span class="editable" contenteditable="true" id="customerName"></span></p>
                <input type="hidden" name="CustomerName" id="customerNameHidden">
                <p>CUSTOMER ADDRESS: <span class="editable" contenteditable="true" id="customerAddress"></span></p>
                <input type="hidden" name="CustomerAddress" id="customerAddressHidden">
                <p>Contact: <span class="editable" contenteditable="true" id="contact"></span></p>
                <input type="hidden" name="Contact" id="contactHidden">
            </div>
            <div class="grid-item grid-item-2">
                <!-- Placeholder for future content or styling purposes -->
            </div>
            <div class="right-div grid-item">
                <p>SALES DATE: <span class="readonly" id="salesDate">
                        <?php echo $sales_date; ?>
                    </span></p>
                <input type="hidden" name="SalesDate" value="<?php echo $sales_date; ?>">
                <p>SALES PERSON: <span class="editable" contenteditable="true" id="salesPerson"></span></p>
                <input type="hidden" name="SalesPerson" id="salesPersonHidden">
                <p>DELIVERY ADDRESS: <span class="editable" contenteditable="true" id="deliveryAddress"></span></p>
                <input type="hidden" name="DeliveryAddress" id="deliveryAddressHidden">
                <p>DELIVERY INSTRUCTION: <span class="editable" contenteditable="true" id="deliveryInstruction"></span>
                </p>
                <input type="hidden" name="DeliveryInstruction" id="deliveryInstructionHidden">
            </div>
        </div>
        <div class="invoice-body">
            <table>
                <thead>
                    <tr>
                        <th class="sl-column">SL</th>
                        <th class="sku-column">SKU</th>
                        <th class="product-column">PRODUCT</th>
                        <th class="qty-column">QTY</th>
                        <th class="unit-column">UNIT</th>
                        <th class="rate-column">RATE</th>
                        <th class="discount-column">DISCOUNT</th>
                        <th class="amount-column">AMOUNT</th>

                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="7">Total Amount</th>
                        <th id="totalAmount">0.00</th>
                    </tr>
                </tfoot>
                <tbody id="itemsContainer">
                    <!-- Item rows will be added here dynamically with JavaScript -->
                </tbody>
            </table>
            <button type="button" onclick="addItem()">Add Item</button>
        </div>
        <div class="invoice-footer">
            <p>IN WORD: <span class="line" id="inWords">
                    <?php echo $InWords; ?>
                </span></p>
            <p>Note: <span class="line" id="note">
                    <?php echo $Note; ?>
                </span></p>
            <input type="submit" name="submit_invoice" value="Submit Invoice">
        </div>

        </div>
        </div>
    </form>
    <script>
        document.getElementById('invoiceForm').addEventListener('submit', function () {
            // Capture contenteditable data and place it into hidden inputs
            document.getElementById('customerNameHidden').value = document.getElementById('customerName').textContent;
            document.getElementById('customerAddressHidden').value = document.getElementById('customerAddress').textContent;
            document.getElementById('contactHidden').value = document.getElementById('contact').textContent;
            document.getElementById('salesPersonHidden').value = document.getElementById('salesPerson').textContent;
            document.getElementById('deliveryAddressHidden').value = document.getElementById('deliveryAddress').textContent;
            document.getElementById('deliveryInstructionHidden').value = document.getElementById('deliveryInstruction').textContent;
            document.getElementById('inWordsHidden').value = document.getElementById('inWords').textContent;
            document.getElementById('noteHidden').value = document.getElementById('note').textContent;  // Include any additional JavaScript needed for handling invoice items
        });
        let sl = 1; // Initialize serial number
        function calculateAmount(element) {
            const row = element.closest('tr');
            const rateInput = row.querySelector('input[name="items[rate][]"]');
            const rate = parseFloat(rateInput.value) || 0;
            const discountInput = row.querySelector('input[name="items[discount][]"]');
            const discountValue = discountInput.value.trim();
            let discount = 0;
            if (discountValue.includes('%')) {
                const percentage = parseFloat(discountValue.replace('%', '')) / 100;
                discount = rate * percentage;
            } else {
                discount = parseFloat(discountValue) || 0;
            }
            const quantityInput = row.querySelector('input[name="items[quantity][]"]');
            const quantity = parseFloat(quantityInput.value) || 1;
            const amountInput = row.querySelector('input[name="items[amount][]"]');
            const amount = (rate - discount) * quantity;
            amountInput.value = amount.toFixed(2);
            updateTotal(); // Update the total when an amount is calculated
        }
        function addItem() {
            const container = document.getElementById('itemsContainer');
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${sl++}</td>
        <td><input type="text" name="items[sku][]" required></td>
        <td class="product-column" contenteditable="true"></td>
        <td><input type="number" name="items[quantity][]" required oninput="calculateAmount(this)"></td>
        <td><input type="text" name="items[unit][]" required></td>
        <td><input type="number" step="0.01" name="items[rate][]" required oninput="calculateAmount(this)"></td>
        <td><input type="text" name="items[discount][]" oninput="calculateAmount(this)"></td>
        <td><input type="number" step="0.01" name="items[amount][]" readonly></td>
    `;
            container.appendChild(row);
            updateTotal(); // Update the total when a new item is added
        }
        function updateTotal() {
            let total = 0;
            const amounts = document.querySelectorAll('input[name="items[amount][]"]');
            amounts.forEach(function (amountInput) {
                total += parseFloat(amountInput.value) || 0;
            });
            document.getElementById('totalAmount').textContent = total.toFixed(2);

            // Update the total in words
            const totalInWords = numberToWords(total) + " BDT Only";
            document.getElementById('inWords').textContent = totalInWords;
        }
        // Convert a number to words for Indian numbering system
        function numberToWords(number) {
            if (number === 0) return 'Zero';
            if (number > 999999999) return 'Number too large'; // Limit to 100 Crore
            function numberToWordsLessThanThousand(number) {
                const ones = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
                if (number < 20) return ones[number];
                if (number < 100) return tens[Math.floor(number / 10)] + (number % 10 !== 0 ? ' ' + ones[number % 10] : '');
                return ones[Math.floor(number / 100)] + ' Hundred' + (number % 100 !== 0 ? ' ' + numberToWordsLessThanThousand(number % 100) : '');
            }
            let crore = Math.floor(number / 10000000); // Crore
            number %= 10000000;
            let lakh = Math.floor(number / 100000); // Lakh
            number %= 100000;
            let thousand = Math.floor(number / 1000); // Thousand
            number %= 1000;
            let hundred = Math.floor(number / 100); // Hundred
            number %= 100;
            let words = '';
            if (crore) words += numberToWordsLessThanThousand(crore) + ' Crore ';
            if (lakh) words += numberToWordsLessThanThousand(lakh) + ' Lakh ';
            if (thousand) words += numberToWordsLessThanThousand(thousand) + ' Thousand ';
            if (hundred) words += numberToWordsLessThanThousand(hundred) + ' Hundred ';
            if (number) words += numberToWordsLessThanThousand(number);
            return words.trim();
        }
        // This function is to be called when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Update the total in case there are any pre-filled item amounts
            updateTotal();
        });
    </script>
</body>

</html>