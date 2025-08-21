<?php
// get_active_packages.php - list active packages that have PDFs
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

try {
	$stmt = $conn->query("SELECT name, category, price, details, pdf_path FROM packages WHERE is_active = 1 AND pdf_path IS NOT NULL ORDER BY category, name");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode(['status' => 'success', 'data' => $rows]);
} catch (Exception $e) {
	echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}