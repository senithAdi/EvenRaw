<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require_once 'db_connect.php';

// Start session and check admin privileges
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    // Clear any previous output
    if (ob_get_length()) ob_clean();
    exit('Unauthorized access');
}

// Clear any previous output
if (ob_get_length()) ob_clean();

// Get report parameters
$type = $_GET['type'] ?? 'detailed';
$includeCharts = isset($_GET['charts']) && $_GET['charts'] == 1;

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('EvenRaw Admin');
$pdf->SetTitle('User Report - ' . ucfirst($type));
$pdf->SetSubject('User Management Report');
$pdf->SetKeywords('TCPDF, PDF, user, report');

// Set default header data
$pdf->SetHeaderData('', 0, 'EvenRaw Users Report - ' . ucfirst($type), 'Generated on ' . date('Y-m-d H:i:s'));

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

// Fetch users data
try {
    $stmt = $conn->prepare("SELECT id, name, email, nic_number, contact_number, created_at FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error
    error_log("Database error: " . $e->getMessage());
    if (ob_get_length()) ob_clean();
    exit('Error fetching user data');
}

// Calculate statistics
$totalUsers = count($users);
$usersWithNIC = 0;
$usersWithContact = 0;

foreach ($users as $user) {
    if (!empty($user['nic_number'])) $usersWithNIC++;
    if (!empty($user['contact_number'])) $usersWithContact++;
}

// Group registrations by date
$registrationsByDate = [];
foreach ($users as $user) {
    $date = date('Y-m-d', strtotime($user['created_at']));
    if (!isset($registrationsByDate[$date])) {
        $registrationsByDate[$date] = 0;
    }
    $registrationsByDate[$date]++;
}

// Add a page
$pdf->AddPage();

// Set content based on report type
$html = '<h1 style="text-align:center;color:#ffcc00;">EvenRaw Users Report - ' . ucfirst($type) . '</h1>';
$html .= '<p style="text-align:center;">Generated on: ' . date('Y-m-d H:i:s') . '</p>';

// Statistics section for all report types
$html .= '<table border="1" cellpadding="4" style="margin-bottom:20px;">
    <tr style="background-color:#fffce6;">
        <th width="33%" style="text-align:center;"><b>Total Users</b></th>
        <th width="33%" style="text-align:center;"><b>With NIC</b></th>
        <th width="33%" style="text-align:center;"><b>With Contact</b></th>
    </tr>
    <tr>
        <td width="33%" style="text-align:center;">' . $totalUsers . '</td>
        <td width="33%" style="text-align:center;">' . $usersWithNIC . ' (' . ($totalUsers > 0 ? round(($usersWithNIC/$totalUsers)*100, 1) : 0) . '%)</td>
        <td width="33%" style="text-align:center;">' . $usersWithContact . ' (' . ($totalUsers > 0 ? round(($usersWithContact/$totalUsers)*100, 1) : 0) . '%)</td>
    </tr>
</table>';

// Generate charts if requested (simple HTML tables as charts)
if ($includeCharts) {
    $html .= generateCharts($usersWithNIC, $usersWithContact, $totalUsers, $registrationsByDate);
}

// Detailed user list for detailed and analysis reports
if ($type === 'detailed' || $type === 'analysis') {
    $html .= '<h2>User Details</h2>
    <table border="1" cellpadding="4">
        <tr style="background-color:#f2f2f2;">
            <th width="10%"><b>ID</b></th>
            <th width="20%"><b>Name</b></th>
            <th width="25%"><b>Email</b></th>
            <th width="15%"><b>NIC</b></th>
            <th width="15%"><b>Contact</b></th>
            <th width="15%"><b>Registered</b></th>
        </tr>';

    foreach ($users as $user) {
        $html .= '
        <tr>
            <td width="10%">' . htmlspecialchars($user['id']) . '</td>
            <td width="20%">' . htmlspecialchars($user['name']) . '</td>
            <td width="25%">' . htmlspecialchars($user['email']) . '</td>
            <td width="15%">' . (!empty($user['nic_number']) ? htmlspecialchars($user['nic_number']) : 'N/A') . '</td>
            <td width="15%">' . (!empty($user['contact_number']) ? htmlspecialchars($user['contact_number']) : 'N/A') . '</td>
            <td width="15%">' . htmlspecialchars($user['created_at']) . '</td>
        </tr>';
    }

    $html .= '</table>';
}

// Registration timeline for summary and analysis reports
if ($type === 'summary' || $type === 'analysis') {
    $html .= '<h2>Registration Timeline</h2>';
    
    if (!empty($registrationsByDate)) {
        $html .= '<table border="1" cellpadding="4">
            <tr style="background-color:#f2f2f2;">
                <th width="70%"><b>Date</b></th>
                <th width="30%"><b>Registrations</b></th>
            </tr>';

        foreach ($registrationsByDate as $date => $count) {
            $html .= '
            <tr>
                <td width="70%">' . htmlspecialchars($date) . '</td>
                <td width="30%">' . htmlspecialchars($count) . '</td>
            </tr>';
        }

        $html .= '</table>';
    } else {
        $html .= '<p>No registration data available.</p>';
    }
}

// Additional analysis for analysis report type
if ($type === 'analysis') {
    $html .= '<h2>User Analysis</h2>';
    
    // Calculate registration growth rate
    $registrationDates = array_keys($registrationsByDate);
    sort($registrationDates);
    $growthRate = count($registrationDates) > 1 ? 
        round((end($registrationsByDate) - reset($registrationsByDate)) / count($users) * 100, 2) : 0;
    
    $html .= '<table border="1" cellpadding="4">
        <tr style="background-color:#f2f2f2;">
            <th width="50%"><b>Metric</b></th>
            <th width="50%"><b>Value</b></th>
        </tr>
        <tr>
            <td>Average Registrations Per Day</td>
            <td>' . round($totalUsers / max(1, count($registrationsByDate)), 2) . '</td>
        </tr>
        <tr>
            <td>Registration Growth Rate</td>
            <td>' . $growthRate . '%</td>
        </tr>
        <tr>
            <td>Data Completeness (NIC)</td>
            <td>' . round(($usersWithNIC/$totalUsers)*100, 1) . '%</td>
        </tr>
        <tr>
            <td>Data Completeness (Contact)</td>
            <td>' . round(($usersWithContact/$totalUsers)*100, 1) . '%</td>
        </tr>
    </table>';
}

// Footer - Safely get admin name with a fallback
$adminName = isset($_SESSION['name']) ? $_SESSION['name'] : 'System Administrator';

$html .= '<div style="margin-top:40px;font-size:10px;color:#777;">
    <p>Report generated by: ' . htmlspecialchars($adminName) . '</p>
    <p>EvenRaw User Management System</p>
</div>';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// Close and output PDF document
$pdf->Output('evenraw_users_report_' . $type . '_' . date('Y-m-d') . '.pdf', 'D');
exit;

// Function to generate simple chart visualizations
function generateCharts($usersWithNIC, $usersWithContact, $totalUsers, $registrationsByDate) {
    $html = '<h2>Data Visualization</h2>';
    
    // Chart 1: User data completeness
    $nicPercentage = $totalUsers > 0 ? round(($usersWithNIC/$totalUsers)*100) : 0;
    $contactPercentage = $totalUsers > 0 ? round(($usersWithContact/$totalUsers)*100) : 0;
    
    $html .= '<h3>Data Completeness</h3>';
    $html .= '<table width="100%" style="margin-bottom: 20px;">
        <tr>
            <td width="30%">NIC Information:</td>
            <td width="70%">
                <div style="background:#e0e0e0; height:20px; width:100%; border-radius:10px;">
                    <div style="background:#4CAF50; height:20px; width:' . $nicPercentage . '%; border-radius:10px; text-align:center; color:white; line-height:20px;">' . $nicPercentage . '%</div>
                </div>
            </td>
        </tr>
        <tr>
            <td>Contact Information:</td>
            <td>
                <div style="background:#e0e0e0; height:20px; width:100%; border-radius:10px;">
                    <div style="background:#2196F3; height:20px; width:' . $contactPercentage . '%; border-radius:10px; text-align:center; color:white; line-height:20px;">' . $contactPercentage . '%</div>
                </div>
            </td>
        </tr>
    </table>';
    
    // Chart 2: Registration timeline bar chart
    if (!empty($registrationsByDate)) {
        $html .= '<h3>Registration Timeline</h3>';
        $maxRegistrations = max($registrationsByDate);
        
        foreach ($registrationsByDate as $date => $count) {
            $barWidth = $maxRegistrations > 0 ? round(($count/$maxRegistrations) * 100) : 0;
            $html .= '<table width="100%" style="margin-bottom: 5px;">
                <tr>
                    <td width="20%">' . $date . '</td>
                    <td width="10%">' . $count . '</td>
                    <td width="70%">
                        <div style="background:#FFD700; height:15px; width:' . $barWidth . '%; border-radius:3px;"></div>
                    </td>
                </tr>
            </table>';
        }
    }
    
    // Chart 3: User distribution pie chart (simulated with table)
    $html .= '<h3>User Distribution</h3>';
    $html .= '<table width="100%"><tr>';
    $html .= '<td width="50%" style="vertical-align:top;">';
    $html .= '<table border="1" cellpadding="4">
        <tr style="background-color:#f2f2f2;">
            <th>Category</th>
            <th>Count</th>
            <th>Percentage</th>
        </tr>
        <tr>
            <td>With NIC</td>
            <td>' . $usersWithNIC . '</td>
            <td>' . ($totalUsers > 0 ? round(($usersWithNIC/$totalUsers)*100, 1) : 0) . '%</td>
        </tr>
        <tr>
            <td>Without NIC</td>
            <td>' . ($totalUsers - $usersWithNIC) . '</td>
            <td>' . ($totalUsers > 0 ? round((($totalUsers - $usersWithNIC)/$totalUsers)*100, 1) : 0) . '%</td>
        </tr>
        <tr>
            <td>With Contact</td>
            <td>' . $usersWithContact . '</td>
            <td>' . ($totalUsers > 0 ? round(($usersWithContact/$totalUsers)*100, 1) : 0) . '%</td>
        </tr>
        <tr>
            <td>Without Contact</td>
            <td>' . ($totalUsers - $usersWithContact) . '</td>
            <td>' . ($totalUsers > 0 ? round((($totalUsers - $usersWithContact)/$totalUsers)*100, 1) : 0) . '%</td>
        </tr>
    </table>';
    $html .= '</td><td width="50%" style="text-align:center; vertical-align:top;">';
    $html .= '<div style="display:inline-block; position:relative; width:150px; height:150px; border-radius:50%; background:conic-gradient(
        #4CAF50 0% ' . ($usersWithNIC/$totalUsers*100) . '%, 
        #F44336 ' . ($usersWithNIC/$totalUsers*100) . '% ' . (($usersWithNIC + ($totalUsers - $usersWithNIC))/$totalUsers*100) . '%
    );"></div>';
    $html .= '<div style="margin-top:10px;">
        <span style="display:inline-block; width:10px; height:10px; background:#4CAF50;"></span> With NIC
        <span style="display:inline-block; width:10px; height:10px; background:#F44336; margin-left:10px;"></span> Without NIC
    </div>';
    $html .= '</td></tr></table>';
    
    return $html;
}
?>