<?php
include 'db_connectPortfolio.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';

if ($category === '' || ($type !== 'view' && $type !== 'click')) {
    http_response_code(400);
    exit('Invalid Parameters');
}

$month = date('Y-m');
$addViews = $type === 'view' ? 1 : 0;
$addClicks = $type === 'click' ? 1 : 0;

try {
    $stmt = $conn->prepare("
        INSERT INTO portfolio_metrics (category, month_year, total_views, total_clicks)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            total_views = total_views + VALUES(total_views),
            total_clicks = total_clicks + VALUES(total_clicks)
    ");
    
    $result = $stmt->execute([$category, $month, $addViews, $addClicks]);
    
    if ($result) {
        echo 'success';
    } else {
        echo 'database_error';
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo 'error: ' . $e->getMessage();
}
?>