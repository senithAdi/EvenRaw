<?php
// get_package.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once 'db_connect.php';

try {
    // Get package name from query parameter
    $packageName = $_GET['name'] ?? '';
    
    if (empty($packageName)) {
        throw new Exception("Package name is required");
    }
    
    // Prepare SQL to get package details
    $stmt = $conn->prepare("SELECT name, price, details FROM packages WHERE name = ?");
    $stmt->execute([$packageName]);
    
    $package = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($package) {
        echo json_encode(["status" => "success", "data" => $package]);
    } else {
        echo json_encode(["status" => "error", "message" => "Package not found"]);
    }
    
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>