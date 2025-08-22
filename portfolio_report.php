<?php
include 'db_connectPortfolio.php';

$categories = ['commercial','food','hotel','wedding'];
$thisMonth = date('Y-m');
$prevMonth = date('Y-m', strtotime('first day of -1 month'));

function getCounts($conn, $cat, $month) {
	$stmt = $conn->prepare("SELECT total_views, total_clicks FROM portfolio_metrics WHERE category=? AND month_year=?");
	$stmt->execute([$cat, $month]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return [
		'views' => $row ? (int)$row['total_views'] : 0,
		'clicks' => $row ? (int)$row['total_clicks'] : 0,
	];
}

$rows = [];
foreach ($categories as $cat) {
	$curr = getCounts($conn, $cat, $thisMonth);
	$prev = getCounts($conn, $cat, $prevMonth);
	$currTotal = $curr['views'] + $curr['clicks'];
	$prevTotal = $prev['views'] + $prev['clicks'];
	$delta = $currTotal - $prevTotal;
	$pct = $prevTotal > 0 ? round(($delta / $prevTotal) * 100) : ($currTotal > 0 ? 100 : 0);
	$rows[] = [
		'category' => $cat,
		'views' => $curr['views'],
		'clicks' => $curr['clicks'],
		'prev_total' => $prevTotal,
		'curr_total' => $currTotal,
		'delta' => $delta,
		'pct' => $pct
	];
}

$mostPopular = null;
$needsUpgrade = null;
if ($rows) {
	$mostPopular = $rows[0]; $needsUpgrade = $rows[0];
	foreach ($rows as $r) {
		if ($r['curr_total'] > $mostPopular['curr_total']) $mostPopular = $r;
		if ($r['curr_total'] < $needsUpgrade['curr_total']) $needsUpgrade = $r;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Portfolio Report</title>
	<style>
		body { font-family: Arial, sans-serif; margin: 30px; color: #333; }
		h1 { margin-bottom: 10px; }
		.muted { color: #666; margin-bottom: 20px; }
		table { width: 100%; border-collapse: collapse; margin-top: 10px; }
		th, td { padding: 12px 10px; border-bottom: 1px solid #eee; text-align: left; }
		th { background: #f7f7f7; }
		.pos { color: #1e8449; }
		.neg { color: #c0392b; }
		.summary { margin: 20px 0; padding: 15px; background: #f9f9f9; border: 1px solid #eee; border-radius: 8px; }
		.actions { margin-top: 20px; }
		.btn { background: #ffd700; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; text-decoration: none; color: #111; }
		.btn:hover { background: #ffea6c; }
		.mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
	</style>
</head>
<body>
	<h1>Portfolio Performance Report</h1>
	<div class="muted">Period: <span class="mono"><?php echo htmlspecialchars($thisMonth); ?></span> vs <span class="mono"><?php echo htmlspecialchars($prevMonth); ?></span></div>

	<table>
		<thead>
			<tr>
				<th>Category</th>
				<th>Views (this)</th>
				<th>Clicks (this)</th>
				<th>Total (this)</th>
				<th>Total (last)</th>
				<th>Change</th>
				<th>%</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($rows as $r): ?>
				<tr>
					<td><?php echo ucfirst(htmlspecialchars($r['category'])); ?></td>
					<td><?php echo $r['views']; ?></td>
					<td><?php echo $r['clicks']; ?></td>
					<td><?php echo $r['curr_total']; ?></td>
					<td><?php echo $r['prev_total']; ?></td>
					<td class="<?php echo $r['delta'] >= 0 ? 'pos' : 'neg'; ?>">
						<?php echo ($r['delta'] >= 0 ? '+' : '') . $r['delta']; ?>
					</td>
					<td class="<?php echo $r['pct'] >= 0 ? 'pos' : 'neg'; ?>">
						<?php echo ($r['pct'] >= 0 ? '+' : '') . $r['pct']; ?>%
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="summary">
		<div><strong>Most popular:</strong> <?php echo $mostPopular ? ucfirst(htmlspecialchars($mostPopular['category'])) . ' (' . $mostPopular['curr_total'] . ')' : 'N/A'; ?></div>
		<div><strong>Needs upgrade:</strong> <?php echo $needsUpgrade ? ucfirst(htmlspecialchars($needsUpgrade['category'])) . ' (' . $needsUpgrade['curr_total'] . ')' : 'N/A'; ?></div>
	</div>

	<div class="actions">
		<a class="btn" href="download_portfolio_report.php?month=<?php echo urlencode($thisMonth); ?>">Download PDF</a>
	</div>
</body>
</html>