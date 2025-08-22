<?php
// Include the TCPDF library
require_once('tcpdf/tcpdf.php');

// Include database connection
include 'db_connectPortfolio.php';

// Extend TCPDF with custom header and footer
class PortfolioPDF extends TCPDF {
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
        $this->Cell(0, 15, 'Portfolio Analytics Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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

// Get current month data
$currentMonth = date('Y-m');
$lastMonth = date('Y-m', strtotime('-1 month'));

try {
    // Get current month metrics
    $stmt = $conn->prepare("SELECT * FROM portfolio_metrics WHERE month_year = ? ORDER BY total_views DESC");
    $stmt->execute([$currentMonth]);
    $currentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get last month metrics for comparison
    $stmt->execute([$lastMonth]);
    $lastMonthData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Create new PDF document
$pdf = new PortfolioPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Portfolio Admin');
$pdf->SetTitle('Portfolio Analytics Report');
$pdf->SetSubject('Portfolio Performance Analysis');
$pdf->SetKeywords('TCPDF, PDF, portfolio, analytics, report');

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
$pdf->Cell(0, 10, 'Portfolio Analytics Report', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Generated on: '.date('F j, Y'), 0, 1, 'C');
$pdf->Cell(0, 10, 'Reporting Period: '.date('F Y'), 0, 1, 'C');
$pdf->Ln(10);

// Current Month Performance section
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Current Month Performance', 0, 1);
$pdf->SetFont('helvetica', '', 10);

if (!empty($currentData)) {
    // Create table header
    $tbl = '
    <table border="1" cellpadding="4">
        <tr style="background-color:#f2f2f2;">
            <th width="25%"><b>Category</b></th>
            <th width="15%"><b>Views</b></th>
            <th width="15%"><b>Clicks</b></th>
            <th width="45%"><b>Change from Last Month</b></th>
        </tr>
    ';
    
    foreach ($currentData as $row) {
        $category = $row['category'];
        $views = $row['total_views'];
        $clicks = $row['total_clicks'];
        
        // Find last month data for comparison
        $lastViews = 0;
        $lastClicks = 0;
        foreach ($lastMonthData as $last) {
            if ($last['category'] === $category) {
                $lastViews = $last['total_views'];
                $lastClicks = $last['total_clicks'];
                break;
            }
        }
        
        $viewChange = $views - $lastViews;
        $clickChange = $clicks - $lastClicks;
        
        $viewChangeText = '';
        if ($viewChange > 0) {
            $viewChangeText = '<span style="color: #28a745;">Views: +' . $viewChange . '</span>';
        } elseif ($viewChange < 0) {
            $viewChangeText = '<span style="color: #dc3545;">Views: ' . $viewChange . '</span>';
        } else {
            $viewChangeText = '<span style="color: #6c757d;">No change</span>';
        }
        
        $tbl .= '
        <tr>
            <td width="25%">' . ucfirst($category) . '</td>
            <td width="15%">' . $views . '</td>
            <td width="15%">' . $clicks . '</td>
            <td width="45%">' . $viewChangeText . '</td>
        </tr>
        ';
    }
    
    $tbl .= '</table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->Ln(10);
    
    // Summary Statistics section
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Summary Statistics', 0, 1);
    $pdf->SetFont('helvetica', '', 11);
    
    $totalViews = array_sum(array_column($currentData, 'total_views'));
    $totalClicks = array_sum(array_column($currentData, 'total_clicks'));
    $avgViews = round($totalViews / count($currentData), 1);
    $mostPopular = $currentData[0];
    
    $statsTbl = '
    <table cellpadding="5">
        <tr>
            <td width="25%" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; text-align: center;">
                <div style="font-size: 16px; font-weight: bold; color: #ffd700;">' . $totalViews . '</div>
                <div style="font-size: 10px; color: #666;">Total Views</div>
            </td>
            <td width="25%" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; text-align: center;">
                <div style="font-size: 16px; font-weight: bold; color: #ffd700;">' . $totalClicks . '</div>
                <div style="font-size: 10px; color: #666;">Total Clicks</div>
            </td>
            <td width="25%" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; text-align: center;">
                <div style="font-size: 16px; font-weight: bold; color: #ffd700;">' . $avgViews . '</div>
                <div style="font-size: 10px; color: #666;">Avg Views/Category</div>
            </td>
            <td width="25%" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; text-align: center;">
                <div style="font-size: 16px; font-weight: bold; color: #ffd700;">' . ucfirst($mostPopular['category']) . '</div>
                <div style="font-size: 10px; color: #666;">Most Popular</div>
            </td>
        </tr>
    </table>
    ';
    
    $pdf->writeHTML($statsTbl, true, false, false, false, '');
    $pdf->Ln(10);
    
    // Most Popular Category section
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Most Popular Category', 0, 1);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 10, ucfirst($mostPopular['category']) . ' leads with ' . $mostPopular['total_views'] . ' views this month.', 0, 'L', false, 1, '', '', true);
    $pdf->Ln(5);
    
    // Recommendations section
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Recommendations', 0, 1);
    $pdf->SetFont('helvetica', '', 11);
    
    $lowestViews = $currentData[count($currentData) - 1];
    
    $recommendations = '
    <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 10px;">
        <h3 style="color: #856404; margin-top: 0; font-size: 12px;">Strategic Insights</h3>
        <ul>
            <li style="color: #856404;"><strong>Consider upgrading:</strong> ' . ucfirst($lowestViews['category']) . ' category shows lower engagement</li>
            <li style="color: #856404;"><strong>Focus marketing efforts</strong> on high-performing categories</li>
            <li style="color: #856404;"><strong>Monitor trends monthly</strong> to identify patterns</li>
            <li style="color: #856404;"><strong>Use analytics data</strong> to optimize content strategy</li>
            <li style="color: #856404;"><strong>Consider A/B testing</strong> for underperforming categories</li>
        </ul>
    </div>
    ';
    
    $pdf->writeHTML($recommendations, true, false, false, false, '');
} else {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'No data available for current month.', 0, 1);
}

// Close and output PDF document
$pdf->Output('portfolio_analytics_report_' . date('Y-m-d') . '.pdf', 'D');

exit();