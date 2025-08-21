<?php
session_start();
require_once 'db_connect.php';



// Get booking ID from URL
$booking_id = $_GET['id'] ?? null;

if (!$booking_id) {
    header("Location: admin_bookings.php");
    exit();
}

// Fetch booking details (without users table join for now)
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header("Location: admin_bookings.php");
    exit();
}

// Get current user name for display
$user_name = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking - EvenRaw</title>
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
            max-width: 1200px;
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
        }

        .back-btn:hover {
            background: #555;
            transform: translateY(-2px);
        }

        .booking-details {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 3px solid #fff700;
            padding-bottom: 10px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .detail-group {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #fff700;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
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

        .btn-edit {
            background: #4CAF50;
            color: white;
        }

        .btn-delete {
            background: #f44336;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .special-requirements {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #fff700;
        }

        .requirements-text {
            font-style: italic;
            color: #666;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
                align-items: center;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">EvenRaw</div>
            <div class="user-info">👤 <?php echo htmlspecialchars($user_name); ?></div>
        </div>

        <a href="admin_bookings.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>

        <div class="booking-details">
            <h1 class="section-title">Booking Details</h1>
            
            <div class="details-grid">
                <div class="detail-group">
                    <div class="detail-label">Booking ID</div>
                    <div class="detail-value">B<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Customer ID</div>
                    <div class="detail-value">A<?php echo str_pad($booking['customer_id'], 4, '0', STR_PAD_LEFT); ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Service Type</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['service_type']); ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Total Amount</div>
                    <div class="detail-value">$<?php echo number_format($booking['total_amount'], 2); ?></div>
                </div>
            </div>

            <div class="details-grid">
                <div class="detail-group">
                    <div class="detail-label">Customer Name</div>
                    <div class="detail-value">Customer #<?php echo $booking['customer_id']; ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Customer Email</div>
                    <div class="detail-value">N/A</div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Customer Phone</div>
                    <div class="detail-value">N/A</div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Payment Status</div>
                    <div class="detail-value">
                        <span class="status-badge status-<?php echo $booking['payment_status']; ?>">
                            <?php echo ucfirst($booking['payment_status']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="details-grid">
                <div class="detail-group">
                    <div class="detail-label">Booking Date</div>
                    <div class="detail-value"><?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Event Date</div>
                    <div class="detail-value"><?php echo date('F j, Y', strtotime($booking['event_date'])); ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Event Time</div>
                    <div class="detail-value"><?php echo date('g:i A', strtotime($booking['event_time'])); ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Booking Status</div>
                    <div class="detail-value">
                        <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                            <?php echo ucfirst($booking['booking_status']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Event Location</div>
                <div class="detail-value"><?php echo htmlspecialchars($booking['event_location']); ?></div>
            </div>

            <?php if (!empty($booking['special_requirements'])): ?>
            <div class="special-requirements">
                <div class="detail-label">Special Requirements</div>
                <div class="requirements-text"><?php echo htmlspecialchars($booking['special_requirements']); ?></div>
            </div>
            <?php endif; ?>

            <div class="actions">
                <a href="edit_booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-edit">
                    <i class="fas fa-edit"></i> Edit Booking
                </a>
                <button class="btn btn-delete" onclick="confirmDelete(<?php echo $booking['id']; ?>)">
                    <i class="fas fa-trash-alt"></i> Delete Booking
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(bookingId) {
            if (confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
                window.location.href = 'delete_booking.php?id=' + bookingId;
            }
        }
    </script>
</body>
</html> 