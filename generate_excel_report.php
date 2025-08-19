<?php
require_once 'db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    exit('Unauthorized access');
}

// Clear any previous output
if (ob_get_length()) ob_clean();

// Fetch users data
try {
    $stmt = $conn->prepare("SELECT id, name, email, nic_number, contact_number, created_at FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('Error fetching user data');
}
// Add this after fetching users data
$type = $_GET['type'] ?? 'detailed';

// Modify the filename to include report type
header('Content-Disposition: attachment; filename=evenraw_users_' . $type . '_' . date('Y-m-d') . '.xls');

// Add a title row
echo '<tr><td colspan="6" style="text-align:center; font-weight:bold; font-size:16px;">EvenRaw Users Report - ' . ucfirst($type) . '</td></tr>';
echo '<tr><td colspan="6" style="text-align:center;">Generated on: ' . date('Y-m-d H:i:s') . '</td></tr>';

// Set Excel headers
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=evenraw_users_' . date('Y-m-d') . '.xls');

// Start Excel content
echo '<table border="1">';
echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>NIC Number</th><th>Contact Number</th><th>Registration Date</th></tr>';

foreach ($users as $user) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($user['id']) . '</td>';
    echo '<td>' . htmlspecialchars($user['name']) . '</td>';
    echo '<td>' . htmlspecialchars($user['email']) . '</td>';
    echo '<td>' . (!empty($user['nic_number']) ? htmlspecialchars($user['nic_number']) : 'N/A') . '</td>';
    echo '<td>' . (!empty($user['contact_number']) ? htmlspecialchars($user['contact_number']) : 'N/A') . '</td>';
    echo '<td>' . htmlspecialchars($user['created_at']) . '</td>';
    echo '</tr>';
}

echo '</table>';
exit;
?>