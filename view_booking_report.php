<?php
require_once 'db_connect.php';
session_start();

// Allow any logged-in user (matches how the app uses sessions)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
	header('Location: Login.html');
	exit();
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
} catch (PDOException $e) {
	exit('Error fetching bookings: '.$e->getMessage());
}

$totalBookings = count($bookings);
$totalRevenue = 0;
$statusCount = ['pending'=>0,'confirmed'=>0,'cancelled'=>0];
$paymentCount = ['pending'=>0,'paid'=>0,'failed'=>0];
$serviceTypes = [];

foreach ($bookings as $booking) {
	$totalRevenue += (float)$booking['total_amount'];
	if (isset($statusCount[$booking['booking_status']])) $statusCount[$booking['booking_status']]++;
	if (isset($paymentCount[$booking['payment_status']])) $paymentCount[$booking['payment_status']]++;
	$service = $booking['service_type'];
	if (!isset($serviceTypes[$service])) $serviceTypes[$service] = 0;
	$serviceTypes[$service]++;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>EvenRaw Bookings Report (View)</title>
	<style>
		body { font-family: Arial, sans-serif; margin: 30px; color:#333; }
		h1 { color:#ffcc00; margin-bottom: 5px; }
		.subtitle { color:#666; margin-bottom:20px; }
		.section-title { margin-top:25px; }
		table { width:100%; border-collapse: collapse; margin-top:10px; }
		th, td { border:1px solid #ddd; padding:8px; font-size:14px; }
		th { background:#f7f7f7; }
		.kpi { display:flex; gap:12px; margin:15px 0; }
		.kpi .box { flex:1; background:#fffce6; padding:12px; border-radius:8px; text-align:center; }
		.actions { text-align:right; margin-bottom:15px; }
		.btn { display:inline-block; padding:8px 12px; background:#2196F3; color:#fff; text-decoration:none; border-radius:4px; margin-left:6px; }
		.btn-secondary { background:#6c757d; }
		.mono { font-family: Consolas, monospace; }
	</style>
</head>
<body>
	<h1>EvenRaw Bookings Report</h1>
	<div class="subtitle">Generated on: <?php echo date('Y-m-d H:i:s'); ?></div>

	<div class="actions">
		<a class="btn btn-secondary" href="admin_bookings.php">Back</a>
		<a class="btn" href="generate_booking_report_admin.php">Download PDF</a>
	</div>

	<h2 class="section-title">Summary</h2>
	<div class="kpi">
		<div class="box"><div>Total Bookings</div><div class="mono"><?php echo $totalBookings; ?></div></div>
		<div class="box"><div>Total Revenue</div><div class="mono">$<?php echo number_format($totalRevenue, 2); ?></div></div>
		<div class="box"><div>Confirmed</div><div class="mono"><?php echo $statusCount['confirmed']; ?></div></div>
		<div class="box"><div>Pending</div><div class="mono"><?php echo $statusCount['pending']; ?></div></div>
		<div class="box"><div>Cancelled</div><div class="mono"><?php echo $statusCount['cancelled']; ?></div></div>
	</div>

	<h2 class="section-title">Service Type Breakdown</h2>
	<table>
		<tr><th>Service</th><th>Count</th><th>Percentage</th></tr>
		<?php if ($totalBookings > 0): ?>
			<?php foreach ($serviceTypes as $service => $count): ?>
				<tr>
					<td><?php echo htmlspecialchars($service); ?></td>
					<td><?php echo $count; ?></td>
					<td><?php echo round(($count/$totalBookings)*100, 2); ?>%</td>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr><td colspan="3" style="text-align:center;">No bookings available.</td></tr>
		<?php endif; ?>
	</table>

	<h2 class="section-title">Detailed Bookings</h2>
	<table>
		<tr>
			<th>ID</th>
			<th>Customer</th>
			<th>Email</th>
			<th>Service</th>
			<th>Event Date</th>
			<th>Amount</th>
			<th>Payment</th>
			<th>Status</th>
		</tr>
		<?php if (!empty($bookings)): ?>
			<?php foreach ($bookings as $b): ?>
				<tr>
					<td><?php echo (int)$b['id']; ?></td>
					<td><?php echo htmlspecialchars($b['customer_name']); ?></td>
					<td><?php echo htmlspecialchars($b['customer_email']); ?></td>
					<td><?php echo htmlspecialchars($b['service_type']); ?></td>
					<td><?php echo htmlspecialchars($b['event_date']); ?></td>
					<td>$<?php echo number_format((float)$b['total_amount'], 2); ?></td>
					<td><?php echo htmlspecialchars(ucfirst($b['payment_status'])); ?></td>
					<td><?php echo htmlspecialchars(ucfirst($b['booking_status'])); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr><td colspan="8" style="text-align:center;">No bookings found.</td></tr>
		<?php endif; ?>
	</table>
</body>
</html>