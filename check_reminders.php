<?php
// Server-side reminder checker for MedClock

// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Include email function
require_once('send_reminder_email.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'db_connection.php';

// Get current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i');

// Create time range for more reliable matching
$time_obj = new DateTime($current_time);
$time_obj->modify('-1 minute');
$prev_minute = $time_obj->format('H:i');

// Query to check for active reminders
$query = "SELECT * FROM reminders 
          WHERE date = ? 
          AND time BETWEEN ? AND ?
          AND taken = 0 
          AND active = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $current_date, $prev_minute, $current_time);
$stmt->execute();
$result = $stmt->get_result();

// Initialize response array
$response = [
    'active' => false,
    'medicine' => '',
    'current_time' => $current_time,
    'current_date' => $current_date,
    'time_range' => [$prev_minute, $current_time]
];

// Check if there are any active reminders
if ($result->num_rows > 0) {
    $reminder = $result->fetch_assoc();
    $response['active'] = true;
    $response['medicine'] = $reminder['medicine_name'];
    
    // Log for debugging
    error_log("Active reminder found: " . $reminder['medicine_name'] . " at " . $current_time);
}

// Get all active reminders for today
$today_query = "SELECT * FROM reminders 
                WHERE date = ? 
                AND taken = 0 
                AND active = 1 
                ORDER BY time ASC";
$today_stmt = $conn->prepare($today_query);
$today_stmt->bind_param("s", $current_date);
$today_stmt->execute();
$today_result = $today_stmt->get_result();

$response['all_active'] = [];
while ($reminder = $today_result->fetch_assoc()) {
    $response['all_active'][] = $reminder;
}

try {
    // Get current date and time
    $currentDate = date("Y-m-d");
    $currentTime = date("H:i");
    
    // Create a time range for matching (current minute and the previous one)
    $currentTimeObj = new DateTime($currentTime);
    $oneMinuteAgo = clone $currentTimeObj;
    $oneMinuteAgo->modify('-1 minute');
    
    $startTime = $oneMinuteAgo->format('H:i');
    $endTime = $currentTimeObj->format('H:i');

    // Log the values for debugging
    error_log("Checking reminders at date: $currentDate, time range: $startTime - $endTime");

    // Query to find reminders due now or in the last minute
    // This is more reliable as the checks might not run exactly on the minute
    $sql = "SELECT * FROM reminders 
            WHERE date = ? AND (time BETWEEN ? AND ?)
            ORDER BY time ASC
            LIMIT 5";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $currentDate, $startTime, $endTime);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare response
    $response = [
        'error' => false,
        'active' => false,
        'medicine' => null,
        'all_active' => [],
        'current_time' => $currentTime,
        'current_date' => $currentDate,
        'time_range' => "$startTime - $endTime"
    ];

    // Check if we found any active reminders
    if ($result && $result->num_rows > 0) {
        $response['active'] = true;
        $firstRow = true;
        
        // Get user email from profile database
        $profileConn = new mysqli("localhost", "root", "", "profile");
        $userEmail = "rutikanaik809@gmail.com"; // Default email
        
        if (!$profileConn->connect_error) {
            $emailQuery = "SELECT email FROM userprofile ORDER BY id DESC LIMIT 1";
            $emailResult = $profileConn->query($emailQuery);
            if ($emailResult && $emailResult->num_rows > 0) {
                $emailRow = $emailResult->fetch_assoc();
                if (!empty($emailRow['email'])) {
                    $userEmail = $emailRow['email'];
                }
            }
            $profileConn->close();
        }
        
        while ($row = $result->fetch_assoc()) {
            $response['all_active'][] = [
                'id' => $row['id'] ?? 'unknown',
                'medicine' => $row['medicine_name'],
                'time' => $row['time'],
                'exact_match' => ($row['time'] === $currentTime)
            ];
            
            // Set the primary medicine to the first match
            if ($firstRow) {
                $response['medicine'] = $row['medicine_name'];
                
                // Send email notification for the first active reminder
                sendReminderEmail(
                    $userEmail,
                    $row['medicine_name'],
                    $row['date'],
                    $row['time']
                );
                
                $firstRow = false;
            }
        }
    } else {
        error_log("No reminders found for date: $currentDate, time range: $startTime - $endTime");
    }

    // Close connection
    $stmt->close();
    $conn->close();

    // Return response
    echo json_encode($response);
    
} catch (Exception $e) {
    // Return error response with details
    echo json_encode([
        'error' => true,
        'message' => 'Error: ' . $e->getMessage(),
        'active' => false,
        'current_time' => date("H:i"),
        'current_date' => date("Y-m-d")
    ]);
} 