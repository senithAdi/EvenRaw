<?php
// package.php - update package price/details for a given name+category
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db_connect.php';

try {
	$data = json_decode(file_get_contents("php://input"), true);
	if (!$data) throw new Exception("No data received");

	$packageName = trim($data['packageName'] ?? '');
	$packagePrice = trim($data['packagePrice'] ?? '');
	$packageDetails = trim($data['packageDetails'] ?? '');
	$category = trim($data['packageCategory'] ?? '');

	if ($packageName === '' || $packagePrice === '' || $packageDetails === '' || $category === '') {
		throw new Exception("All fields are required");
	}

	// Check existence and current values
	$check = $conn->prepare("SELECT price, details FROM packages WHERE name = ? AND category = ?");
	$check->execute([$packageName, $category]);
	$current = $check->fetch(PDO::FETCH_ASSOC);

	if (!$current) {
		echo json_encode(["status" => "error", "message" => "Package not found"]);
		exit;
	}

	if ($current['price'] === $packagePrice && $current['details'] === $packageDetails) {
		echo json_encode(["status" => "warning", "message" => "No changes made"]);
		exit;
	}

	$stmt = $conn->prepare("UPDATE packages SET price = ?, details = ? WHERE name = ? AND category = ?");
	$stmt->execute([$packagePrice, $packageDetails, $packageName, $category]);

	echo json_encode(["status" => "success", "message" => "Package updated"]);
} catch (Exception $e) {
	echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>