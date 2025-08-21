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
header('Content-Disposition: attachment; filename=evenraw_users_' . $type . '_' . date('Y-m-d') . '.csv');

// Add title rows
fputcsv($output, array('EvenRaw Users Report - ' . ucfirst($type)));
fputcsv($output, array('Generated on: ' . date('Y-m-d H:i:s')));
fputcsv($output, array('')); // Empty row for spacing

// Set CSV headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=evenraw_users_' . date('Y-m-d') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, array('ID', 'Name', 'Email', 'NIC Number', 'Contact Number', 'Registration Date'));

// Add data
foreach ($users as $user) {
    fputcsv($output, array(
        $user['id'],
        $user['name'],
        $user['email'],
        $user['nic_number'] ?? 'N/A',
        $user['contact_number'] ?? 'N/A',
        $user['created_at']
    ));
}

fclose($output);
exit;
?>