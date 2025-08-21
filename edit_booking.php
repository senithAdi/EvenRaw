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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("UPDATE bookings SET 
            service_type = ?, 
            event_date = ?, 
            event_time = ?, 
            event_location = ?, 
            total_amount = ?, 
            payment_status = ?, 
            booking_status = ?, 
            special_requirements = ? 
            WHERE id = ?");
        
        $stmt->execute([
            $_POST['service_type'],
            $_POST['event_date'],
            $_POST['event_time'],
            $_POST['event_location'],
            $_POST['total_amount'],
            $_POST['payment_status'],
            $_POST['booking_status'],
            $_POST['special_requirements'],
            $booking_id
        ]);
        
        $success_message = "Booking updated successfully!";
    } catch (PDOException $e) {
        $error_message = "Error updating booking: " . $e->getMessage();
    }
}

// Fetch booking details
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
    <title>Edit Booking - EvenRaw</title>
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
            max-width: 1000px;
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

        .edit-form {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
            border-bottom: 3px solid #fff700;
            padding-bottom: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-input {
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #fff700;
            box-shadow: 0 0 0 3px rgba(255, 247, 0, 0.1);
        }

        .form-select {
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            background-color: white;
            cursor: pointer;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .full-width {
            grid-column: 1 / -1;
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

        .btn-save {
            background: #4CAF50;
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

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .booking-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 4px solid #fff700;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            text-align: center;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        @media (max-width: 768px) {
            .form-grid {
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
            <div class="user-info">👤 <?php echo htmlspecialchars($admin_name); ?></div>
        </div>

        <a href="admin_bookings.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>

        <div class="edit-form">
            <h1 class="section-title">Edit Booking</h1>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="booking-info">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Booking ID</div>
                        <div class="info-value">B<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Customer ID</div>
                        <div class="info-value">A<?php echo str_pad($booking['customer_id'], 4, '0', STR_PAD_LEFT); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Booking Date</div>
                        <div class="info-value"><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></div>
                    </div>
                </div>
            </div>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Service Type</label>
                        <select name="service_type" class="form-select" required>
                            <option value="Wedding Photography" <?php echo ($booking['service_type'] == 'Wedding Photography') ? 'selected' : ''; ?>>Wedding Photography</option>
                            <option value="Food Photography" <?php echo ($booking['service_type'] == 'Food Photography') ? 'selected' : ''; ?>>Food Photography</option>
                            <option value="Hotel Photography" <?php echo ($booking['service_type'] == 'Hotel Photography') ? 'selected' : ''; ?>>Hotel Photography</option>
                            <option value="Commercial Photography" <?php echo ($booking['service_type'] == 'Commercial Photography') ? 'selected' : ''; ?>>Commercial Photography</option>
                            <option value="Model Photography" <?php echo ($booking['service_type'] == 'Model Photography') ? 'selected' : ''; ?>>Model Photography</option>
                            <option value="Event Photography" <?php echo ($booking['service_type'] == 'Event Photography') ? 'selected' : ''; ?>>Event Photography</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Event Date</label>
                        <input type="date" name="event_date" class="form-input" value="<?php echo $booking['event_date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Event Time</label>
                        <input type="time" name="event_time" class="form-input" value="<?php echo $booking['event_time']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Amount ($)</label>
                        <input type="number" name="total_amount" class="form-input" step="0.01" min="0" value="<?php echo $booking['total_amount']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select" required>
                            <option value="pending" <?php echo ($booking['payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo ($booking['payment_status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                            <option value="failed" <?php echo ($booking['payment_status'] == 'failed') ? 'selected' : ''; ?>>Failed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Booking Status</label>
                        <select name="booking_status" class="form-select" required>
                            <option value="pending" <?php echo ($booking['booking_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo ($booking['booking_status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="cancelled" <?php echo ($booking['booking_status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Event Location</label>
                    <input type="text" name="event_location" class="form-input" value="<?php echo htmlspecialchars($booking['event_location']); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Special Requirements</label>
                    <textarea name="special_requirements" class="form-input form-textarea" placeholder="Enter any special requirements or notes..."><?php echo htmlspecialchars($booking['special_requirements'] ?? ''); ?></textarea>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i> Save Changes
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