<?php
// generate_feedback_report.php
require_once('tcpdf/tcpdf.php');
require_once 'db_connect.php';

// Extend TCPDF with custom header and footer
class FeedbackPDF extends TCPDF {
    // Page header
    public function Header() {
        // Logo
        $image_file = 'path/to/your/logo.png'; // Update with your logo path
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 25, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, 'EvenRaw Feedback Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        // Line break
        $this->Ln(20);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // Generated date
        $this->Cell(0, 10, 'Generated on: '.date('Y-m-d H:i:s'), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new FeedbackPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('EvenRaw Admin');
$pdf->SetTitle('Feedback Report');
$pdf->SetSubject('Customer Feedback Analysis');
$pdf->SetKeywords('TCPDF, PDF, feedback, report, EvenRaw');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Customer Feedback Report', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Generated on: '.date('Y-m-d H:i:s'), 0, 1, 'C');
$pdf->Ln(10);

// Fetch feedback data
try {
    $stmt = $conn->query("SELECT * FROM feedback ORDER BY submission_date DESC");
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate statistics
    $total_feedbacks = count($feedbacks);
    $avg_rating = 0;
    $rating_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    $service_counts = [];
    $contact_requests = 0;
    $showcased_feedbacks = 0;
    
    foreach ($feedbacks as $feedback) {
        $avg_rating += $feedback['rating'];
        $rating_counts[$feedback['rating']]++;
        
        if (!isset($service_counts[$feedback['service']])) {
            $service_counts[$feedback['service']] = 0;
        }
        $service_counts[$feedback['service']]++;
        
        if ($feedback['contact_me']) {
            $contact_requests++;
        }
        
        if ($feedback['showcase']) {
            $showcased_feedbacks++;
        }
    }
    
    $avg_rating = $total_feedbacks > 0 ? round($avg_rating / $total_feedbacks, 2) : 0;
    
    // Display statistics
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Feedback Statistics', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    
    $stats_html = '
    <table border="1" cellpadding="5">
        <tr style="background-color:#f2f2f2;">
            <th width="60%">Metric</th>
            <th width="40%">Value</th>
        </tr>
        <tr>
            <td>Total Feedbacks</td>
            <td>'.$total_feedbacks.'</td>
        </tr>
        <tr>
            <td>Average Rating</td>
            <td>'.$avg_rating.'/5</td>
        </tr>
        <tr>
            <td>Contact Requests</td>
            <td>'.$contact_requests.' ('.round(($contact_requests/$total_feedbacks)*100, 1).'%)</td>
        </tr>
        <tr>
            <td>Showcased Feedbacks</td>
            <td>'.$showcased_feedbacks.' ('.round(($showcased_feedbacks/$total_feedbacks)*100, 1).'%)</td>
        </tr>
    </table>
    ';
    
    $pdf->writeHTML($stats_html, true, false, true, false, '');
    $pdf->Ln(10);
    
    // Rating distribution
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Rating Distribution', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    
    $rating_html = '
    <table border="1" cellpadding="5">
        <tr style="background-color:#f2f2f2;">
            <th width="20%">Rating</th>
            <th width="40%">Count</th>
            <th width="40%">Percentage</th>
        </tr>
    ';
    
    for ($i = 5; $i >= 1; $i--) {
        $percentage = $total_feedbacks > 0 ? round(($rating_counts[$i]/$total_feedbacks)*100, 1) : 0;
        $rating_html .= '
        <tr>
            <td>'.$i.' Star'.($i > 1 ? 's' : '').'</td>
            <td>'.$rating_counts[$i].'</td>
            <td>'.$percentage.'%</td>
        </tr>
        ';
    }
    
    $rating_html .= '</table>';
    $pdf->writeHTML($rating_html, true, false, true, false, '');
    $pdf->Ln(10);
    
    // Service distribution
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Service Distribution', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    
    $service_html = '
    <table border="1" cellpadding="5">
        <tr style="background-color:#f2f2f2;">
            <th width="40%">Service Type</th>
            <th width="30%">Count</th>
            <th width="30%">Percentage</th>
        </tr>
    ';
    
    foreach ($service_counts as $service => $count) {
        $percentage = $total_feedbacks > 0 ? round(($count/$total_feedbacks)*100, 1) : 0;
        $service_html .= '
        <tr>
            <td>'.ucfirst($service).'</td>
            <td>'.$count.'</td>
            <td>'.$percentage.'%</td>
        </tr>
        ';
    }
    
    $service_html .= '</table>';
    $pdf->writeHTML($service_html, true, false, true, false, '');
    $pdf->Ln(10);
    
    // Detailed feedback list
    if ($total_feedbacks > 0) {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Detailed Feedback List', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        
        $details_html = '
        <table border="1" cellpadding="5">
            <tr style="background-color:#f2f2f2;">
                <th width="10%">ID</th>
                <th width="15%">Name</th>
                <th width="20%">Email</th>
                <th width="15%">Service</th>
                <th width="10%">Rating</th>
                <th width="20%">Date</th>
                <th width="10%">Contact</th>
            </tr>
        ';
        
        foreach ($feedbacks as $feedback) {
            $contact = $feedback['contact_me'] ? 'Yes' : 'No';
            $details_html .= '
            <tr>
                <td>'.$feedback['id'].'</td>
                <td>'.$feedback['name'].'</td>
                <td>'.$feedback['email'].'</td>
                <td>'.ucfirst($feedback['service']).'</td>
                <td>'.$feedback['rating'].'/5</td>
                <td>'.date('M j, Y', strtotime($feedback['submission_date'])).'</td>
                <td>'.$contact.'</td>
            </tr>
            ';
        }
        
        $details_html .= '</table>';
        $pdf->writeHTML($details_html, true, false, true, false, '');
    }
    
    // Add a page for comments
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Customer Comments', 0, 1);
    $pdf->SetFont('helvetica', '', 11);
    
    foreach ($feedbacks as $index => $feedback) {
        if (!empty(trim($feedback['comments']))) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Feedback #'.$feedback['id'].' from '.$feedback['name'].' (Rating: '.$feedback['rating'].'/5)', 0, 1);
            $pdf->SetFont('helvetica', '', 11);
            
            // MultiCell for comments with text wrapping
            $pdf->MultiCell(0, 8, $feedback['comments'], 0, 'L', false, 1, '', '', true, 0, false, true, 0, 'T');
            $pdf->Ln(5);
        }
    }
    
} catch (PDOException $e) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Error generating report: '.$e->getMessage(), 0, 1);
}

// Close and output PDF document
$pdf->Output('evenraw_feedback_report_'.date('Y-m-d').'.pdf', 'D');

exit();