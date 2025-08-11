<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Get user ID from URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_exists = $stmt->fetch();

if (!$user_exists) {
    $_SESSION['error_message'] = "User not found!";
    header("Location: admin_users.php");
    exit();
}

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error_message'] = "You cannot delete your own account!";
    header("Location: admin_users.php");
    exit();
}

// Delete user
try {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    
    $_SESSION['success_message'] = "User deleted successfully!";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error deleting user: " . $e->getMessage();
}

header("Location: admin_users.php");
exit();
?>