<?php
// mark_taken.php - Marks a reminder as taken/played

// Database connection
$host = "localhost";
$db = "ADDMEDICINE";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]));
}

// Get reminder ID from request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false,
        'message' => 'Invalid reminder ID'
    ]));
}

// First, get the reminder details before marking it as played
$fetchSql = "SELECT medicine_name, date, time FROM reminders WHERE id = ?";
$fetchStmt = $conn->prepare($fetchSql);
$fetchStmt->bind_param("i", $id);
$fetchStmt->execute();
$reminderData = $fetchStmt->get_result()->fetch_assoc();
$fetchStmt->close();

// Now mark the reminder as played and update status
// Using simple update that works with existing database structure
$sql = "UPDATE reminders SET played = 1, status = 'Taken' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$result = $stmt->execute();

// Return result with more comprehensive information
header('Content-Type: application/json');
echo json_encode([
    'success' => $result,
    'id' => $id,
    'reminder' => $reminderData ? [
        'medicine_name' => $reminderData['medicine_name'] ?? 'Unknown',
        'date' => $reminderData['date'] ?? '',
        'time' => $reminderData['time'] ?? '',
        'status' => 'Taken'
    ] : null,
    'message' => $result ? 'Reminder successfully marked as taken' : 'Failed to mark reminder as taken'
]);

// Close connection
$stmt->close();
$conn->close();
?>