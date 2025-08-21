<?php
// update_package.php - update package info and optionally upload/replace PDF (multipart/form-data)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db_connect.php';

try {
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		throw new Exception('Invalid request method');
	}

	$packageName = trim($_POST['packageName'] ?? '');
	$category    = trim($_POST['packageCategory'] ?? '');
	$price       = trim($_POST['packagePrice'] ?? '');
	$details     = trim($_POST['packageDetails'] ?? '');

	if ($packageName === '' || $category === '') {
		throw new Exception('Package name and category are required');
	}

	// Ensure row exists
	$select = $conn->prepare("SELECT id, pdf_path FROM packages WHERE name = ? AND category = ?");
	$select->execute([$packageName, $category]);
	$row = $select->fetch(PDO::FETCH_ASSOC);

	$newPdfPath = null;
	// Handle PDF upload if provided
	if (isset($_FILES['quotePdf']) && $_FILES['quotePdf']['error'] !== UPLOAD_ERR_NO_FILE) {
		if ($_FILES['quotePdf']['error'] !== UPLOAD_ERR_OK) {
			throw new Exception('File upload error');
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime  = finfo_file($finfo, $_FILES['quotePdf']['tmp_name']);
		finfo_close($finfo);
		if ($mime !== 'application/pdf') {
			throw new Exception('Only PDF files are allowed');
		}

		$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'Uploads' . DIRECTORY_SEPARATOR . 'quotes';
		if (!is_dir($uploadDir)) {
			if (!mkdir($uploadDir, 0777, true)) {
				throw new Exception('Failed to create upload directory');
			}
		}

		$slug = function($s) {
			$s = preg_replace('/[^A-Za-z0-9\- ]/', '', $s);
			$s = preg_replace('/\s+/', '_', trim($s));
			return strtolower($s);
		};
		$filename = $slug($category) . '_' . $slug($packageName) . '_' . time() . '.pdf';
		$targetAbs = $uploadDir . DIRECTORY_SEPARATOR . $filename;
		$targetRel = 'Uploads/quotes/' . $filename;

		if (!move_uploaded_file($_FILES['quotePdf']['tmp_name'], $targetAbs)) {
			throw new Exception('Failed to save uploaded file');
		}

		// Delete old PDF if exists
		if ($row && !empty($row['pdf_path'])) {
			$oldAbs = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $row['pdf_path']);
			if (is_file($oldAbs)) @unlink($oldAbs);
		}
		$newPdfPath = $targetRel;
	}

	if ($row) {
		// Build dynamic update
		$sets = [];
		$params = [];
		if ($price !== '') { $sets[] = 'price = ?'; $params[] = $price; }
		if ($details !== '') { $sets[] = 'details = ?'; $params[] = $details; }
		if ($newPdfPath !== null) { $sets[] = 'pdf_path = ?'; $params[] = $newPdfPath; }
		// if PDF uploaded, mark active
		if ($newPdfPath !== null) { $sets[] = 'is_active = 1'; }

		if (count($sets) > 0) {
			$params[] = $packageName;
			$params[] = $category;
			$sql = "UPDATE packages SET " . implode(', ', $sets) . " WHERE name = ? AND category = ?";
			$stmt = $conn->prepare($sql);
			$stmt->execute($params);
		}
	} else {
		// Insert new package row
		$stmt = $conn->prepare("INSERT INTO packages (name, category, price, details, pdf_path, is_active) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->execute([$packageName, $category, $price, $details, $newPdfPath, 1]);
	}

	echo json_encode(['status' => 'success', 'message' => 'Package saved']);
} catch (Exception $e) {
	echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}