<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    // First get all contacts
    $stmt = $conn->query("SELECT * FROM contact_submissions ORDER BY submission_date DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mark all as read
    $conn->query("UPDATE contact_submissions SET is_read = TRUE WHERE is_read = FALSE");
    
    echo json_encode($contacts);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>