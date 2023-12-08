<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      margin: 1px;
      /* Reduce the outer margin of the body */
      padding: 10px;
    }

    label {
      font-size: 8px;
    }

    .invoice-container {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      margin: auto;
      padding: 10px;
      width: 100%;
      max-width: 1000px;
    }

    .invoice-header,
    .invoice-body,
    .invoice-footer {
      display: block;
    }

    .invoice-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .invoice-header img {
      width: 100px;
    }

    .invoice-title {
      background-color: black;
      color: white;
      display: inline-block;
      padding: 10px 20px;
      margin-top: 10px;
    }

    .grid-div {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      /* Keep two columns */
      gap: 10px;
      /* Adjust the gap between columns if needed */
      margin-bottom: 20px;
    }

    .grid-item {
      background: #fff;
      border: 1px solid #ddd;
      padding: 8px;
      border-radius: 4px;
    }

    .invoice-body,
    .invoice-footer {
      background: #fff;
      border: 1px solid #ddd;
      margin-top: 20px;
      padding: 10px;
      border-radius: 4px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #f0f0f0;
    }

    .add-row-button {
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      text-align: center;
      display: inline-block;
      font-size: 16px;
      cursor: pointer;
      border: none;
      border-radius: 4px;
    }

    .add-row-button:hover {
      background-color: #45a049;
    }

    .invoice-footer {
      margin-top: 20px;
    }

    .submit-btn {
      background-color: #008CBA;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }

    .submit-btn:hover {
      background-color: #007B9A;
    }

    input[type="text"],
    input[type="date"],
    input[type="number"],
    textarea {
      width: auto;
      /* Change from 100% to auto to reduce width */
      padding: 5px;
      /* Reduced padding inside the inputs */
      margin: 2px 0;
      /* Reduced margin for less space between inputs */
      font-size: 12px;
      /* Adjust font size if needed */
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
    }

    .invoice-body {
      position: relative;
    }
  </style>
</head>

<body>
  <div class="invoice-container">
    <form id="invoiceForm">
      <div class="invoice-header">
        <img src="Safwah-Limited-logo.png" alt="SAFWAH LIMITED">
        <div class="invoice-title">
          INVOICE
        </div>
      </div>

      <div class="grid-div">
        <div class="grid-item">
          <label for="invoiceNo">INVOICE NO:</label>
          <input type="text" id="invoiceNo" name="invoiceNo" required>
          <br><label for="customerName">CUSTOMER NAME:</label>
          <input type="text" id="customerName" name="customerName" required>
          <br><label for="customerAddress">CUSTOMER ADDRESS:</label>
          <input type="text" id="customerAddress" name="customerAddress" required>
          <br><label for="contact">CONTACT:</label>
          <input type="text" id="contact" name="contact" required>
        </div>
        <div class="grid-item">
          <label for="salesDate">SALES DATE:</label>
          <input type="date" id="salesDate" name="salesDate" required>
          <br><label for="salesPerson">SALES PERSON:</label>
          <input type="text" id="salesPerson" name="salesPerson" required>
          <br><label for="deliveryAddress">DELIVERY ADDRESS:</label>
          <input type="text" id="deliveryAddress" name="deliveryAddress" required>
          <br><label for="deliveryInstruction">DELIVERY INSTRUCTION:</label>
          <input type="text" id="deliveryInstruction" name="deliveryInstruction" required>
        </div>
      </div>
  </div>

  <div class="invoice-body">
    <table id="invoiceItems">
      <thead>
        <tr>
          <th>SL</th>
          <th>PRODUCT</th>
          <th>QTY</th>
          <th>UNIT</th>
          <th>RATE</th>
          <th>DISCOUNT</th>
          <th>AMOUNT</th>
        </tr>
      </thead>
      <tbody>
        <!-- Rows will be added here -->
      </tbody>
      <tfoot>
        <tr>
          <th colspan="6">Total Bill</th>
          <th><input type="number" name="totalBill" placeholder="0" readonly></th>
        </tr>
      </tfoot>
    </table>
    <button type="button" class="add-row-button" onclick="addRow()">Add Row</button>
  </div>

  <div class="invoice-footer">
    <input type="text" name="inWords" placeholder="IN WORD:" required>
    <textarea name="note" placeholder="NOTE:"></textarea>
    <input type="submit" value="Submit Invoice" class="submit-btn">
  </div>
  </form>
  </div>
  <script>
    let rowCount = 0; // Initialize row count

    function addRow() {
      if (rowCount < 25) { // Check if less than 25 rows
        const table = document.getElementById("invoiceItems").getElementsByTagName('tbody')[0];
        const newRow = table.insertRow(table.rows.length);

        for (let i = 0; i < 7; i++) {
          let newCell = newRow.insertCell(i);
          let input = document.createElement("input");
          input.type = "text";
          newCell.appendChild(input);
        }

        rowCount++;
      }
    } F
  </script>

</body>

</html>