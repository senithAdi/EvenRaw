<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'No ID provided']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM contact_submissions WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete contact: ' . $e->getMessage()
    ]);
}
?>