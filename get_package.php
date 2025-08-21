<?php
// get_package.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db_connect.php';

try {
	$packageName = $_GET['name'] ?? '';
	$category    = $_GET['category'] ?? '';

	if ($packageName === '' || $category === '') {
		throw new Exception("Package name and category are required");
	}

	$stmt = $conn->prepare("SELECT name, price, details, pdf_path, is_active FROM packages WHERE name = ? AND category = ?");
	$stmt->execute([$packageName, $category]);

	$package = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($package) {
		echo json_encode(["status" => "success", "data" => $package]);
	} else {
		echo json_encode(["status" => "error", "message" => "Package not found"]);
	}
} catch (Exception $e) {
	echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}