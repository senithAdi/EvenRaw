<?php
// Include database connection
require_once 'db_connect.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

try {
    // Get and sanitize form data
    $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $service = isset($_POST['service']) ? filter_var($_POST['service'], FILTER_SANITIZE_STRING) : '';
    $rating = isset($_POST['rating']) ? filter_var($_POST['rating'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $comments = isset($_POST['comments']) ? filter_var($_POST['comments'], FILTER_SANITIZE_STRING) : '';
    $contact_me = isset($_POST['contact_me']) ? 1 : 0;
    $submission_date = date('Y-m-d H:i:s');

    // Validate required fields
    if (empty($name) || empty($email) || empty($service) || empty($comments) || $rating < 1 || $rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields correctly']);
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit();
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO feedback 
                           (name, email, service, rating, comments, contact_me, submission_date) 
                           VALUES 
                           (:name, :email, :service, :rating, :comments, :contact_me, :submission_date)");

    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':service', $service);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':comments', $comments);
    $stmt->bindParam(':contact_me', $contact_me, PDO::PARAM_INT);
    $stmt->bindParam(':submission_date', $submission_date);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Thank you for your feedback!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save feedback']);
    }

} catch(PDOException $e) {
    // Log the error for debugging (in a real app, you'd log to a file)
    error_log("Database error: " . $e->getMessage());
    
    // Return a user-friendly message
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred. Please try again later.']);
} catch(Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
}
?>