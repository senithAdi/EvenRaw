<?php
session_start();
require_once('tcpdf/tcpdf.php');
require_once 'db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Access denied.');
}

// Extend TCPDF with custom header and footer
class BookingReportPDF extends TCPDF {
    // Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, 'EvenRaw Bookings Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        // Line break
        $this->Ln(10);
        // Add a horizontal line
        $this->Line(10, 30, $this->getPageWidth() - 10, 30);
        // Move pointer down
        $this->SetY(35);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // Generated info
        $this->SetY(-25);
        $this->Cell(0, 10, 'Generated on: '.date('Y-m-d H:i:s').' by '.$_SESSION['name'], 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

try {
    // Fetch all bookings data with more details
    $stmt = $conn->prepare("
        SELECT b.*, u.name as customer_name, u.email as customer_email 
        FROM bookings b 
        LEFT JOIN users u ON b.customer_id = u.id 
        ORDER BY b.id DESC
    ");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate statistics
    $totalBookings = count($bookings);
    $totalRevenue = 0;
    $statusCount = [
        'pending' => 0,
        'confirmed' => 0,
        'cancelled' => 0
    ];
    $paymentCount = [
        'pending' => 0,
        'paid' => 0,
        'failed' => 0
    ];
    $serviceTypes = [];
    
    foreach ($bookings as $booking) {
        $totalRevenue += $booking['total_amount'];
        $statusCount[$booking['booking_status']]++;
        $paymentCount[$booking['payment_status']]++;
        
        $serviceType = $booking['service_type'];
        if (!isset($serviceTypes[$serviceType])) {
            $serviceTypes[$serviceType] = 0;
        }
        $serviceTypes[$serviceType]++;
    }
    
    // Create new PDF document
    $pdf = new BookingReportPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('EvenRaw Admin');
    $pdf->SetAuthor('EvenRaw System');
    $pdf->SetTitle('Bookings Report');
    $pdf->SetSubject('Bookings Report');
    
    // Set default header data
    $pdf->SetHeaderData('', 0, 'EvenRaw Bookings Report', '');
    
    // Set margins
    $pdf->SetMargins(15, 40, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(25);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font for the content
    $pdf->SetFont('helvetica', '', 10);
    
    // Report summary section
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Booking Summary', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    
    $summary = <<<EOD
    Total Bookings: $totalBookings
    Total Revenue: $$totalRevenue
    Confirmed Bookings: {$statusCount['confirmed']}
    Pending Bookings: {$statusCount['pending']}
    Cancelled Bookings: {$statusCount['cancelled']}
    Paid Payments: {$paymentCount['paid']}
    Pending Payments: {$paymentCount['pending']}
    Failed Payments: {$paymentCount['failed']}
    EOD;
    
    $pdf->MultiCell(0, 8, $summary, 0, 'L', 0, 1);
    $pdf->Ln(5);
    
    // Service type breakdown
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Service Type Breakdown', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    
    foreach ($serviceTypes as $service => $count) {
        $percentage = round(($count / $totalBookings) * 100, 2);
        $pdf->Cell(0, 8, "$service: $count bookings ($percentage%)", 0, 1, 'L');
    }
    $pdf->Ln(5);
    
    // Detailed bookings table
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Detailed Booking Information', 0, 1, 'L');
    $pdf->SetFont('helvetica', 'B', 9);
    
    // Table header
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
        // Alternate row background
        $fill = !$fill;
        $pdf->SetFillColor($fill ? 240 : 255, $fill ? 240 : 255, $fill ? 240 : 255);
        
        $pdf->Cell(15, 8, $booking['id'], 1, 0, 'C', $fill);
        $pdf->Cell(30, 8, substr($booking['customer_name'], 0, 12), 1, 0, 'L', $fill);
        $pdf->Cell(35, 8, substr($booking['service_type'], 0, 15), 1, 0, 'L', $fill);
        $pdf->Cell(25, 8, $booking['event_date'], 1, 0, 'C', $fill);
        $pdf->Cell(25, 8, '$'.$booking['total_amount'], 1, 0, 'R', $fill);
        
        // Color code payment status
        switch ($booking['payment_status']) {
            case 'paid':
                $pdf->SetTextColor(0, 128, 0);
                break;
            case 'pending':
                $pdf->SetTextColor(200, 120, 0);
                break;
            case 'failed':
                $pdf->SetTextColor(200, 0, 0);
                break;
        }
        $pdf->Cell(25, 8, ucfirst($booking['payment_status']), 1, 0, 'C', $fill);
        $pdf->SetTextColor(0, 0, 0);
        
        // Color code booking status
        switch ($booking['booking_status']) {
            case 'confirmed':
                $pdf->SetTextColor(0, 128, 0);
                break;
            case 'pending':
                $pdf->SetTextColor(200, 120, 0);
                break;
            case 'cancelled':
                $pdf->SetTextColor(200, 0, 0);
                break;
        }
        $pdf->Cell(25, 8, ucfirst($booking['booking_status']), 1, 1, 'C', $fill);
        $pdf->SetTextColor(0, 0, 0);
    }
    
    // Add a page for charts (if you want to add visual elements)
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Booking Statistics', 0, 1, 'L');
    
    // Add some simple bar charts using rectangles
    $pdf->SetFont('helvetica', '', 10);
    
    // Booking status chart
    $pdf->Cell(0, 10, 'Booking Status Distribution', 0, 1, 'L');
    $maxCount = max($statusCount);
    $barWidth = 30;
    $x = 20;
    $y = $pdf->GetY() + 5;
    
    foreach ($statusCount as $status => $count) {
        $height = ($count / $maxCount) * 40;
        $pdf->Rect($x, $y + (40 - $height), $barWidth, $height, 'F', array(), array(100, 100, 200));
        $pdf->Text($x, $y + 45, ucfirst($status) . " ($count)");
        $x += $barWidth + 10;
    }
    $pdf->SetY($y + 50);
    
    // Payment status chart
    $pdf->Cell(0, 10, 'Payment Status Distribution', 0, 1, 'L');
    $maxCount = max($paymentCount);
    $barWidth = 30;
    $x = 20;
    $y = $pdf->GetY() + 5;
    
    foreach ($paymentCount as $status => $count) {
        $height = ($count / $maxCount) * 40;
        $pdf->Rect($x, $y + (40 - $height), $barWidth, $height, 'F', array(), array(200, 100, 100));
        $pdf->Text($x, $y + 45, ucfirst($status) . " ($count)");
        $x += $barWidth + 10;
    }
    
    // Close and output PDF document
    $pdf->Output('evenraw_bookings_report.pdf', 'D');
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}