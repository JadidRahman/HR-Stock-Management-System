<?php
// We assume that you have the functions.php file where calculateDays and calculateLeave are defined.
include 'leave.php';

// Function to calculate the number of days between two dates
function calculateDays($start_date, $end_date)
{
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    // Include the end date in the calculation
    $end->modify('+1 day');
    $interval = $start->diff($end);
    // Return the number of days in the interval
    return $interval->days;
}

// Function to calculate taken and balance leave
function calculateLeave($start_date, $end_date, $entitlement, $taken)
{
    $days = calculateDays($start_date, $end_date);
    $new_taken = $taken + $days;
    $balance = max($entitlement - $new_taken, 0); // Ensure the balance doesn't go below zero
    // Return an array with the new taken days and balance
    return array('days' => $days, 'taken' => $new_taken, 'balance' => $balance);
}

// Make sure the script only runs for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the necessary POST data is present
    if (isset($_POST['start_date'], $_POST['end_date'], $_POST['entitlement'], $_POST['taken'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $entitlement = intval($_POST['entitlement']);
        $taken = intval($_POST['taken']);

        // Calculate the leave details
        $leaveDetails = calculateLeave($start_date, $end_date, $entitlement, $taken);

        // Return the results as JSON
        echo json_encode(
            array(
                'success' => true,
                'days' => $leaveDetails['days'],
                'taken' => $leaveDetails['taken'],
                'balance' => $leaveDetails['balance']
            )
        );
    } else {
        echo json_encode(
            array(
                'success' => false,
                'message' => 'Missing required POST data'
            )
        );
    }
} else {
    echo json_encode(
        array(
            'success' => false,
            'message' => 'Invalid request method'
        )
    );
}
?>