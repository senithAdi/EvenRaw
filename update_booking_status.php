<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

$booking_id = $input['booking_id'] ?? null;
$type = $input['type'] ?? null;

if (!$booking_id || !$type) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

try {
    if ($type === 'payment') {
        $payment_status = $input['payment_status'] ?? null;
        if (!$payment_status) {
            echo json_encode(['success' => false, 'message' => 'Payment status is required']);
            exit();
        }
        
        $stmt = $conn->prepare("UPDATE bookings SET payment_status = ? WHERE id = ?");
        $stmt->execute([$payment_status, $booking_id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Payment status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made or booking not found']);
        }
        
    } elseif ($type === 'booking') {
        $booking_status = $input['booking_status'] ?? null;
        if (!$booking_status) {
            echo json_encode(['success' => false, 'message' => 'Booking status is required']);
            exit();
        }
        
        $stmt = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
        $stmt->execute([$booking_status, $booking_id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Booking status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made or booking not found']);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid update type']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 