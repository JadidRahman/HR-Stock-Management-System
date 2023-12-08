<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'EMS';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sl_no = isset($_POST['sl_no']) ? $conn->real_escape_string($_POST['sl_no']) : NULL;
    $voucher_no = isset($_POST['voucher_no']) ? $conn->real_escape_string($_POST['voucher_no']) : NULL;
    $received_from = isset($_POST['received_from']) ? $conn->real_escape_string($_POST['received_from']) : NULL;
    $amount = isset($_POST['amount']) ? $conn->real_escape_string($_POST['amount']) : NULL;
    $payment_method = isset($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : NULL;
    $cheque_no = isset($_POST['cheque_no']) ? $conn->real_escape_string($_POST['cheque_no']) : NULL;
    $bank_name = isset($_POST['bank_name']) ? $conn->real_escape_string($_POST['bank_name']) : NULL;
    $transaction_date = isset($_POST['transaction_date']) ? $conn->real_escape_string($_POST['transaction_date']) : NULL;
    $sender_no = isset($_POST['sender_no']) ? $conn->real_escape_string($_POST['sender_no']) : NULL;
    $receiver_no = isset($_POST['receiver_no']) ? $conn->real_escape_string($_POST['receiver_no']) : NULL;
    $transaction_no = isset($_POST['transaction_no']) ? $conn->real_escape_string($_POST['transaction_no']) : NULL;
    $purpose = isset($_POST['purpose']) ? $conn->real_escape_string($_POST['purpose']) : NULL;

    // Start transaction
    $conn->begin_transaction();
    try {
        $receiptStmt = $conn->prepare("INSERT INTO receipts (sl_no, voucher_no, received_from, amount, payment_method, cheque_no, bank_name, transaction_date, sender_no, receiver_no, transaction_no, purpose, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $receiptStmt->bind_param("iissssssssss", $sl_no, $voucher_no, $received_from, $amount, $payment_method, $cheque_no, $bank_name, $transaction_date, $sender_no, $receiver_no, $transaction_no, $purpose);

        if (!$receiptStmt->execute()) {
            throw new Exception("Error: " . $receiptStmt->error);
        }
        $receiptStmt->close();

        $ledgerStmt = $conn->prepare("INSERT INTO acc_ledger (date, debit, description, created_at) VALUES (?, ?, ?, NOW())");
        $ledgerStmt->bind_param("sds", $transaction_date, $amount, $purpose);

        if (!$ledgerStmt->execute()) {
            throw new Exception("Error: " . $ledgerStmt->error);
        }
        $ledgerStmt->close();

        $conn->commit();
        $_SESSION['message'] = "Receipt and ledger entry submitted successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = $e->getMessage();
    }

    $conn->close();
    header('Location: l.php');
    exit();
} else {
    header('Location: l.php');
    exit();
}
?>