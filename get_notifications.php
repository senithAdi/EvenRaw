<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    // Count unread messages
    $stmt = $conn->prepare("SELECT COUNT(*) as unread_count FROM contact_submissions WHERE is_read = FALSE");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'unreadCount' => $result['unread_count']
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>