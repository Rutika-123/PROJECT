<?php
$host = "localhost";
$db = "ADDMEDICINE";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST['clear_history'])) {
    $sqlClearHistory = "DELETE FROM reminders WHERE played = 1";
    $conn->query($sqlClearHistory);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// Set default status for entries that don't have one
$updateNullStatus = "UPDATE reminders SET status = 'Taken' WHERE played = 1 AND (status IS NULL OR status = '')"; 
$conn->query($updateNullStatus);

$sql = "SELECT medicine_name, date, time, status FROM reminders WHERE played = 1 ORDER BY date DESC, time DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'theme_header.php'; ?>
<title>Medicine History | MedClock</title>
<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 20px; 
    position: relative;
}

body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('Resources/medicine.jpg') no-repeat center center fixed;
    background-size: cover;
    filter: blur(1px) brightness(1.0);
    z-index: -1;
}

body.dark-theme::before {
    filter: blur(1px) brightness(0.7);
}

.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

h2 {
    color: #4c8bf5;
    margin-bottom: 20px;
}

body.dark-theme h2 {
    color: #b19cd9;
}

table { 
    width: 100%; 
    border-collapse: collapse;
    margin-top: 15px;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    transition: background-color 0.3s, box-shadow 0.3s;
}

body.dark-theme table {
    background-color: rgba(45, 55, 72, 0.8);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

th, td { 
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

body.dark-theme th, 
body.dark-theme td {
    border-bottom: 1px solid #4a5568;
    color: #f4f4f4;
}

th { 
    background-color: #87CEEB;
    color: #333;
    font-weight: bold;
}

body.dark-theme th {
    background-color: #5a4cad;
    color: #f4f4f4;
}

tr:hover {
    background-color: #f9f9f9;
}

body.dark-theme tr:hover {
    background-color: rgba(74, 85, 104, 0.3);
}

tr:last-child td {
    border-bottom: none;
}

.home-link {
    display: inline-block;
    margin-bottom: 15px;
    background: #4c8bf5;
    color: white;
    text-align: center;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s;
}

body.dark-theme .home-link {
    background: #5a4cad;
}

.home-link:hover {
    background: #3b7ae4;
}

body.dark-theme .home-link:hover {
    background: #3a2c8d;
}

.clear-btn {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s;
    margin-bottom: 15px;
}

.clear-btn:hover {
    background-color: #c0392b;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.no-history {
    text-align: center;
    padding: 30px;
    color: #777;
    font-style: italic;
}

body.dark-theme .no-history {
    color: #a0aec0;
}
</style>
</head>
<body id="body">
<div class="container">
    <h2>Medicine History</h2>
    
    <div class="header-actions">
        <a href="dashboard2.php" class="home-link">Home</a>
        
        <form method="post">
            <button type="submit" name="clear_history" class="clear-btn" 
                   onclick="return confirm('Are you sure you want to clear all history?');">
                Clear All History
            </button>
        </form>
    </div>

    <table>
        <tr>
            <th>Medicine</th>
            <th>Scheduled Date</th>
            <th>Scheduled Time</th>
            <th>Status</th>
        </tr>
        <?php if ($result->num_rows > 0): 
            while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo htmlspecialchars($row['time']); ?></td>
                <td><?php echo htmlspecialchars($row['status'] ?? 'Taken'); ?></td>
            </tr>
        <?php endwhile; 
        else: ?>
            <tr>
                <td colspan='4' class="no-history">No history found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<?php include 'theme_footer.php'; ?>
</body>
</html>