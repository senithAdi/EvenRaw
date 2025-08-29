<?php
// view_quote_details.php
require_once 'db_connect.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: view_quotes.php");
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM quote_requests WHERE id = ?");
    $stmt->execute([$id]);
    $quote = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$quote) {
        die("Quote not found");
    }
} catch(PDOException $e) {
    die("Error fetching quote: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote Details - EvenRaw</title>
</head>
<body>
    <h1>Quote Request Details</h1>
    
    <p><strong>Name:</strong> <?php echo htmlspecialchars($quote['full_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($quote['email']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($quote['phone']); ?></p>
    <p><strong>Service Type:</strong> <?php echo htmlspecialchars($quote['service_type']); ?></p>
    <p><strong>Submission Date:</strong> <?php echo $quote['submission_date']; ?></p>
    <p><strong>Status:</strong> <?php echo ucfirst($quote['status']); ?></p>
    
    <h2>Project Details:</h2>
    <p><?php echo nl2br(htmlspecialchars($quote['project_details'])); ?></p>
    
    <a href="view_quotes.php">Back to Quote List</a>
</body>
</html>