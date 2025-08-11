<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT * FROM contact_submissions ORDER BY submission_date DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($contacts);
} catch(PDOException $e) {
    echo json_encode([
        'error' => 'Failed to fetch contacts: ' . $e->getMessage()
    ]);
}
?>