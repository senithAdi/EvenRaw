<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in

// Get booking ID from URL
$booking_id = $_GET['id'] ?? null;

if (!$booking_id) {
    header("Location: admin_bookings.php");
    exit();
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        
        if ($stmt->rowCount() > 0) {
            header("Location: admin_bookings.php?deleted=1");
            exit();
        } else {
            $error_message = "Booking not found or already deleted.";
        }
    } catch (PDOException $e) {
        $error_message = "Error deleting booking: " . $e->getMessage();
    }
}

// Fetch booking details for confirmation
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header("Location: admin_bookings.php");
    exit();
}

// Get current admin name for display
$admin_name = $_SESSION['name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Booking - EvenRaw</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(to bottom right, #fffbe6, #f6f6f6);
            color: #333;
            min-height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(180deg, #fff700, #ffe600);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 2em;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .user-info {
            font-weight: 500;
            color: #333;
        }

        .back-btn {
            background: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background: #555;
            transform: translateY(-2px);
        }

        .delete-form {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        .warning-icon {
            font-size: 64px;
            color: #f44336;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .warning-text {
            color: #721c24;
            background: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #f5c6cb;
        }

        .booking-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 4px solid #f44336;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            margin-right: 10px;
        }

        .info-value {
            color: #333;
        }

        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-delete {
            background: #f44336;
            color: white;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">EvenRaw</div>
            <div class="user-info">👤 <?php echo htmlspecialchars($admin_name); ?></div>
        </div>

        <a href="admin_bookings.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>

        <div class="delete-form">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1 class="section-title">Delete Booking</h1>
            
            <div class="warning-text">
                <strong>Warning:</strong> This action cannot be undone. The booking will be permanently removed from the system.
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="booking-info">
                <div class="info-item">
                    <span class="info-label">Booking ID:</span>
                    <span class="info-value">B<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Service Type:</span>
                    <span class="info-value"><?php echo htmlspecialchars($booking['service_type']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Event Date:</span>
                    <span class="info-value"><?php echo date('F j, Y', strtotime($booking['event_date'])); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Amount:</span>
                    <span class="info-value">$<?php echo number_format($booking['total_amount'], 2); ?></span>
                </div>
            </div>

            <form method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete this booking? This action cannot be undone.');">
                <div class="actions">
                    <button type="submit" name="confirm_delete" class="btn btn-delete">
                        <i class="fas fa-trash-alt"></i> Delete Booking
                    </button>
                    <a href="view_booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 