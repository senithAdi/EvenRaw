<?php
require_once('tcpdf/tcpdf.php');
require_once 'db_connect.php';

session_start();

// Allow any logged-in user (matches how the app uses sessions)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
	if (ob_get_length()) ob_clean();
	header('Location: Login.html');
	exit();
}
if (ob_get_length()) ob_clean();

class BookingReportPDF extends TCPDF {
	public function Header() {
		$this->SetFont('helvetica', 'B', 20);
		$this->Cell(0, 15, 'EvenRaw Bookings Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(10);
		$this->Line(10, 30, $this->getPageWidth() - 10, 30);
		$this->SetY(35);
	}
	public function Footer() {
		$this->SetY(-15);
		$this->SetFont('helvetica', 'I', 8);
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetY(-25);
		$adminName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'System Administrator';
		$this->Cell(0, 10, 'Generated on: '.date('Y-m-d H:i:s').' by '.$adminName, 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}

try {
	$stmt = $conn->prepare("
		SELECT b.*, u.name as customer_name, u.email as customer_email 
		FROM bookings b 
		LEFT JOIN users u ON b.customer_id = u.id 
		ORDER BY b.id DESC
	");
	$stmt->execute();
	$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$totalBookings = count($bookings);
	$totalRevenue = 0;
	$statusCount = ['pending'=>0,'confirmed'=>0,'cancelled'=>0];
	$paymentCount = ['pending'=>0,'paid'=>0,'failed'=>0];
	$serviceTypes = [];

	foreach ($bookings as $booking) {
		$totalRevenue += (float)$booking['total_amount'];
		if (isset($statusCount[$booking['booking_status']])) $statusCount[$booking['booking_status']]++;
		if (isset($paymentCount[$booking['payment_status']])) $paymentCount[$booking['payment_status']]++;
		$serviceType = $booking['service_type'];
		if (!isset($serviceTypes[$serviceType])) $serviceTypes[$serviceType] = 0;
		$serviceTypes[$serviceType]++;
	}

	$pdf = new BookingReportPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator('EvenRaw Admin');
	$pdf->SetAuthor('EvenRaw System');
	$pdf->SetTitle('Bookings Report');
	$pdf->SetSubject('Bookings Report');
	$pdf->SetHeaderData('', 0, 'EvenRaw Bookings Report', '');

	$pdf->SetMargins(15, 40, 15);
	$pdf->SetHeaderMargin(10);
	$pdf->SetFooterMargin(25);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 10);

	$pdf->SetFont('helvetica', 'B', 14);
	$pdf->Cell(0, 10, 'Booking Summary', 0, 1, 'L');
	$pdf->SetFont('helvetica', '', 10);

	$summary = "Total Bookings: {$totalBookings}\n".
		"Total Revenue: $".number_format($totalRevenue, 2)."\n".
		"Confirmed Bookings: ".($statusCount['confirmed'] ?? 0)."\n".
		"Pending Bookings: ".($statusCount['pending'] ?? 0)."\n".
		"Cancelled Bookings: ".($statusCount['cancelled'] ?? 0)."\n".
		"Paid Payments: ".($paymentCount['paid'] ?? 0)."\n".
		"Pending Payments: ".($paymentCount['pending'] ?? 0)."\n".
		"Failed Payments: ".($paymentCount['failed'] ?? 0);
	$pdf->MultiCell(0, 8, $summary, 0, 'L', 0, 1);
	$pdf->Ln(5);

	$pdf->SetFont('helvetica', 'B', 12);
	$pdf->Cell(0, 10, 'Service Type Breakdown', 0, 1, 'L');
	$pdf->SetFont('helvetica', '', 10);
	if ($totalBookings > 0) {
		foreach ($serviceTypes as $service => $count) {
			$percentage = round(($count / $totalBookings) * 100, 2);
			$pdf->Cell(0, 8, "{$service}: {$count} bookings ({$percentage}%)", 0, 1, 'L');
		}
	} else {
		$pdf->Cell(0, 8, "No bookings available.", 0, 1, 'L');
	}
	$pdf->Ln(5);

	$pdf->SetFont('helvetica', 'B', 12);
	$pdf->Cell(0, 10, 'Detailed Booking Information', 0, 1, 'L');
	$pdf->SetFont('helvetica', 'B', 9);

	$pdf->SetFillColor(245, 245, 245);
	$pdf->Cell(15, 8, 'ID', 1, 0, 'C', 1);
	$pdf->Cell(30, 8, 'Customer', 1, 0, 'C', 1);
	$pdf->Cell(35, 8, 'Service Type', 1, 0, 'C', 1);
	$pdf->Cell(25, 8, 'Event Date', 1, 0, 'C', 1);
	$pdf->Cell(25, 8, 'Amount', 1, 0, 'C', 1);
	$pdf->Cell(25, 8, 'Payment', 1, 0, 'C', 1);
	$pdf->Cell(25, 8, 'Status', 1, 1, 'C', 1);

	$pdf->SetFont('helvetica', '', 8);
	$fill = 0;

	foreach ($bookings as $booking) {
		$fill = !$fill;
		$pdf->SetFillColor($fill ? 240 : 255, $fill ? 240 : 255, $fill ? 240 : 255);

		$pdf->Cell(15, 8, $booking['id'], 1, 0, 'C', $fill);
		$pdf->Cell(30, 8, substr((string)$booking['customer_name'], 0, 12), 1, 0, 'L', $fill);
		$pdf->Cell(35, 8, substr((string)$booking['service_type'], 0, 15), 1, 0, 'L', $fill);
		$pdf->Cell(25, 8, (string)$booking['event_date'], 1, 0, 'C', $fill);
		$pdf->Cell(25, 8, '$'.number_format((float)$booking['total_amount'], 2), 1, 0, 'R', $fill);

		switch ($booking['payment_status']) {
			case 'paid': $pdf->SetTextColor(0, 128, 0); break;
			case 'pending': $pdf->SetTextColor(200, 120, 0); break;
			case 'failed': $pdf->SetTextColor(200, 0, 0); break;
		}
		$pdf->Cell(25, 8, ucfirst((string)$booking['payment_status']), 1, 0, 'C', $fill);
		$pdf->SetTextColor(0, 0, 0);

		switch ($booking['booking_status']) {
			case 'confirmed': $pdf->SetTextColor(0, 128, 0); break;
			case 'pending': $pdf->SetTextColor(200, 120, 0); break;
			case 'cancelled': $pdf->SetTextColor(200, 0, 0); break;
		}
		$pdf->Cell(25, 8, ucfirst((string)$booking['booking_status']), 1, 1, 'C', $fill);
		$pdf->SetTextColor(0, 0, 0);
	}

	$pdf->AddPage();
	$pdf->SetFont('helvetica', 'B', 14);
	$pdf->Cell(0, 10, 'Booking Statistics', 0, 1, 'L');

	$pdf->SetFont('helvetica', '', 10);
	$pdf->Cell(0, 10, 'Booking Status Distribution', 0, 1, 'L');
	$maxCount = max($statusCount ?: [0]);
	$barWidth = 30;
	$x = 20;
	$y = $pdf->GetY() + 5;

	foreach ($statusCount as $status => $count) {
		$height = $maxCount > 0 ? ($count / $maxCount) * 40 : 0;
		$pdf->Rect($x, $y + (40 - $height), $barWidth, $height, 'F', array(), array(100, 100, 200));
		$pdf->Text($x, $y + 45, ucfirst($status) . " ($count)");
		$x += $barWidth + 10;
	}
	$pdf->SetY($y + 50);

	$pdf->Cell(0, 10, 'Payment Status Distribution', 0, 1, 'L');
	$maxCount = max($paymentCount ?: [0]);
	$barWidth = 30;
	$x = 20;
	$y = $pdf->GetY() + 5;

	foreach ($paymentCount as $status => $count) {
		$height = $maxCount > 0 ? ($count / $maxCount) * 40 : 0;
		$pdf->Rect($x, $y + (40 - $height), $barWidth, $height, 'F', array(), array(200, 100, 100));
		$pdf->Text($x, $y + 45, ucfirst($status) . " ($count)");
		$x += $barWidth + 10;
	}

	$pdf->Output('evenraw_bookings_report.pdf', 'D');
} catch (PDOException $e) {
	exit("Database error: " . $e->getMessage());
}