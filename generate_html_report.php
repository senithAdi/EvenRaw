<?php
require_once 'db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    exit('Unauthorized access');
}

$type = $_GET['type'] ?? 'detailed';
$includeCharts = isset($_GET['charts']) && $_GET['charts'] == 1;

// Fetch users data
try {
    $stmt = $conn->prepare("SELECT id, name, email, nic_number, contact_number, created_at FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('Error fetching user data: ' . $e->getMessage());
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

// Safely get admin name with a fallback
$adminName = isset($_SESSION['name']) ? $_SESSION['name'] : 'System Administrator';

// Output HTML report
header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Report - EvenRaw</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #ffcc00; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .stats-box { display: flex; margin: 20px 0; }
        .stat { flex: 1; background: #fffce6; padding: 15px; margin: 0 10px; border-radius: 8px; text-align: center; }
        .footer { margin-top: 40px; font-size: 12px; color: #777; }
        .print-btn { margin: 20px 0; padding: 10px 15px; background: #ffcc00; border: none; border-radius: 4px; cursor: pointer; }
        .chart-bar { background: #FFD700; height: 15px; border-radius: 3px; }
        .progress-bar { background: #e0e0e0; height: 20px; width: 100%; border-radius: 10px; }
        .progress-fill { height: 20px; border-radius: 10px; text-align: center; color: white; line-height: 20px; }
    </style>
</head>
<body>
    <h1>EvenRaw Users Report - ' . ucfirst($type) . '</h1>
    <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    
    <button class="print-btn" onclick="window.print()">Print Report</button>
    
    <div class="stats-box">
        <div class="stat">
            <h3>Total Users</h3>
            <p>' . $totalUsers . '</p>
        </div>
        <div class="stat">
            <h3>With NIC</h3>
            <p>' . $usersWithNIC . ' (' . ($totalUsers > 0 ? round(($usersWithNIC/$totalUsers)*100, 1) : 0) . '%)</p>
        </div>
        <div class="stat">
            <h3>With Contact</h3>
            <p>' . $usersWithContact . ' (' . ($totalUsers > 0 ? round(($usersWithContact/$totalUsers)*100, 1) : 0) . '%)</p>
        </div>
    </div>';

// Generate charts if requested
if ($includeCharts) {
    echo '<h2>Data Visualization</h2>';
    
    // Chart 1: User data completeness
    $nicPercentage = $totalUsers > 0 ? round(($usersWithNIC/$totalUsers)*100) : 0;
    $contactPercentage = $totalUsers > 0 ? round(($usersWithContact/$totalUsers)*100) : 0;
    
    echo '<h3>Data Completeness</h3>';
    echo '<table width="100%" style="margin-bottom: 20px;">
        <tr>
            <td width="30%">NIC Information:</td>
            <td width="70%">
                <div class="progress-bar">
                    <div class="progress-fill" style="background:#4CAF50; width:' . $nicPercentage . '%;">' . $nicPercentage . '%</div>
                </div>
            </td>
        </tr>
        <tr>
            <td>Contact Information:</td>
            <td>
                <div class="progress-bar">
                    <div class="progress-fill" style="background:#2196F3; width:' . $contactPercentage . '%;">' . $contactPercentage . '%</div>
                </div>
            </td>
        </tr>
    </table>';
    
    // Chart 2: Registration timeline bar chart
    if (!empty($registrationsByDate)) {
        echo '<h3>Registration Timeline</h3>';
        $maxRegistrations = max($registrationsByDate);
        
        foreach ($registrationsByDate as $date => $count) {
            $barWidth = $maxRegistrations > 0 ? round(($count/$maxRegistrations) * 100) : 0;
            echo '<table width="100%" style="margin-bottom: 5px;">
                <tr>
                    <td width="20%">' . $date . '</td>
                    <td width="10%">' . $count . '</td>
                    <td width="70%">
                        <div class="chart-bar" style="width:' . $barWidth . '%;"></div>
                    </td>
                </tr>
            </table>';
        }
    }
}

// Detailed user list for detailed and analysis reports
if ($type === 'detailed' || $type === 'analysis') {
    echo '
    <h2>User Details</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>NIC</th>
                <th>Contact</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($users as $user) {
        echo '
            <tr>
                <td>' . htmlspecialchars($user['id']) . '</td>
                <td>' . htmlspecialchars($user['name']) . '</td>
                <td>' . htmlspecialchars($user['email']) . '</td>
                <td>' . (!empty($user['nic_number']) ? htmlspecialchars($user['nic_number']) : 'N/A') . '</td>
                <td>' . (!empty($user['contact_number']) ? htmlspecialchars($user['contact_number']) : 'N/A') . '</td>
                <td>' . htmlspecialchars($user['created_at']) . '</td>
            </tr>';
    }
    
    echo '
        </tbody>
    </table>';
}

// Registration timeline for summary and analysis reports
if ($type === 'summary' || $type === 'analysis') {
    echo '
    <h2>Registration Timeline</h2>
    <p>User registrations by date:</p>';
    
    if (!empty($registrationsByDate)) {
        echo '
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Registrations</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($registrationsByDate as $date => $count) {
            echo '
                <tr>
                    <td>' . htmlspecialchars($date) . '</td>
                    <td>' . htmlspecialchars($count) . '</td>
                </tr>';
        }
        
        echo '
            </tbody>
        </table>';
    } else {
        echo '<p>No registration data available.</p>';
    }
}

// Additional analysis for analysis report type
if ($type === 'analysis') {
    echo '<h2>User Analysis</h2>';
    
    // Calculate registration growth rate
    $registrationDates = array_keys($registrationsByDate);
    sort($registrationDates);
    $growthRate = count($registrationDates) > 1 ? 
        round((end($registrationsByDate) - reset($registrationsByDate)) / count($users) * 100, 2) : 0;
    
    echo '<table border="1" cellpadding="4">
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

echo '
    <div class="footer">
        <p>Report generated by: ' . htmlspecialchars($adminName) . '</p>
        <p>EvenRaw User Management System</p>
    </div>
</body>
</html>';
?>