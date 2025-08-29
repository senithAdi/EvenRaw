<?php
// view_quotes.php
require_once 'db_connect.php';

// Check if user is logged in (you should implement proper authentication)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM quote_requests ORDER BY submission_date DESC");
    $stmt->execute();
    $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $quotes = [];
    $error = "Error fetching quotes: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quote Requests - EvenRaw</title>
    <style>
        /* Add your admin styling here */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-pending { color: orange; }
        .status-contacted { color: blue; }
        .status-quoted { color: green; }
        .status-completed { color: purple; }
    </style>
</head>
<body>
    <h1>Quote Requests</h1>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Service</th>
                <th>Submission Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quotes as $quote): ?>
            <tr>
                <td><?php echo $quote['id']; ?></td>
                <td><?php echo htmlspecialchars($quote['full_name']); ?></td>
                <td><?php echo htmlspecialchars($quote['email']); ?></td>
                <td><?php echo htmlspecialchars($quote['phone']); ?></td>
                <td><?php echo htmlspecialchars($quote['service_type']); ?></td>
                <td><?php echo $quote['submission_date']; ?></td>
                <td class="status-<?php echo $quote['status']; ?>">
                    <?php echo ucfirst($quote['status']); ?>
                </td>
                <td>
                    <a href="view_quote_details.php?id=<?php echo $quote['id']; ?>">View Details</a>
                    <a href="update_quote_status.php?id=<?php echo $quote['id']; ?>&status=contacted">Mark as Contacted</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>