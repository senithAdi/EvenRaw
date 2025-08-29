<?php
// process_feedback.php
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get and validate form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $rating = intval($_POST['rating'] ?? 5);
    $comments = trim($_POST['comments'] ?? '');
    $contact_me = isset($_POST['contact_me']) ? 1 : 0;

    // Validate required fields
    if (empty($name) || empty($email) || empty($service) || empty($comments)) {
        throw new Exception('All required fields must be filled');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please provide a valid email address');
    }

    if ($rating < 1 || $rating > 5) {
        throw new Exception('Please provide a valid rating');
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO feedback (name, email, service, rating, comments, contact_me, submission_date) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->execute([$name, $email, $service, $rating, $comments, $contact_me]);

    echo json_encode([
        'status' => 'success', 
        'message' => 'Thank you for your feedback! We appreciate your time and will use your input to improve our services.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>