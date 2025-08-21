<?php
// delete_package.php - delete the PDF and hide the package (is_active = 0)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db_connect.php';

try {
	$data = json_decode(file_get_contents('php://input'), true);
	if (!$data) throw new Exception('No data received');

	$packageName = trim($data['packageName'] ?? '');
	$category    = trim($data['category'] ?? '');

	if ($packageName === '' || $category === '') {
		throw new Exception('Package name and category are required');
	}

	// Get existing pdf
	$sel = $conn->prepare("SELECT pdf_path FROM packages WHERE name = ? AND category = ?");
	$sel->execute([$packageName, $category]);
	$row = $sel->fetch(PDO::FETCH_ASSOC);
	if (!$row) {
		echo json_encode(['status' => 'warning', 'message' => 'Package not found']);
		exit;
	}

	// Delete file
	if (!empty($row['pdf_path'])) {
		$abs = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $row['pdf_path']);
		if (is_file($abs)) @unlink($abs);
	}

	$upd = $conn->prepare("UPDATE packages SET pdf_path = NULL, is_active = 0 WHERE name = ? AND category = ?");
	$upd->execute([$packageName, $category]);

	echo json_encode(['status' => 'success', 'message' => 'PDF deleted and package hidden']);
} catch (Exception $e) {
	echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}