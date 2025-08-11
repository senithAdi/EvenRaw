<?php
// update_package.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once 'db_connect.php';

try {
    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        throw new Exception("No data received");
    }
    
    $packageName = trim($data['packageName']);
    $packagePrice = trim($data['packagePrice']);
    $packageDetails = trim($data['packageDetails']);
    
    // Validate input
    if (empty($packageName) || empty($packagePrice) || empty($packageDetails)) {
        throw new Exception("All fields are required");
    }
    
    // Prepare SQL (update by package name)
    $stmt = $conn->prepare("UPDATE packages SET price = ?, details = ? WHERE name = ?");
    $stmt->execute([$packagePrice, $packageDetails, $packageName]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Package updated successfully"]);
    } else {
        // If no rows were updated, the package might not exist
        echo json_encode(["status" => "warning", "message" => "Package not found or no changes made"]);
    }
    
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>