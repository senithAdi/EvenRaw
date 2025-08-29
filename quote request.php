<?php
// update_quote_status.php
require_once 'db_connect.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    
    // Validate status
    $allowed_statuses = ['pending', 'contacted', 'quoted', 'completed'];
    if (!in_array($status, $allowed_statuses)) {
        die("Invalid status");
    }
    
    try {
        $stmt = $conn->prepare("UPDATE quote_requests SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        header("Location: view_quotes.php");
        exit;
    } catch(PDOException $e) {
        die("Error updating status: " . $e->getMessage());
    }
} else {
    header("Location: view_quotes.php");
    exit;
}
?>