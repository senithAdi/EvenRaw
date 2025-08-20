<?php
session_start();
require_once 'db_connect.php';



// Fetch all bookings data
try {
    $stmt = $conn->prepare("SELECT id, customer_id, booking_date, payment_status, booking_status, service_type, total_amount FROM bookings ORDER BY id DESC");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
    $bookings = [];
}

// Get current admin name for display
$admin_name = $_SESSION['name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Bookings</title>
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
      display: flex;
      flex-direction: column;
    }

    .container {
      display: flex;
      flex: 1;
    }

    .sidebar {
      width: 220px;
      background: linear-gradient(180deg, #fff700, #ffe600);
      padding: 30px 20px;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      display: flex;
      flex-direction: column;
      gap: 25px;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
      z-index: 2;
    }
    .logo {
      font-size: 1.8em;
      font-weight: 700;
      letter-spacing: 2px;
    }
    .sidebar h2 {
      font-size: 20px;
      font-weight: bold;
      color: black;
      margin-bottom: 20px;
    }

    .sidebar a {
      text-decoration: none;
      color: #111;
      font-weight: 500;
      padding: 12px 10px;
      border-radius: 8px;
      transition: background 0.3s;
    }

    .sidebar a:hover {
      background: rgba(0, 0, 0, 0.05);
    }

    .sidebar a.active {
      background: rgba(0, 0, 0, 0.1);
      font-weight: 600;
    }

    .main-content {
      margin-left: 220px;
      padding: 40px;
      flex: 1;
      animation: fadeIn 0.5s ease;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    header h1 {
      font-size: 26px;
      margin-left: 520px;
    }

    .user-info {
      font-weight: 500;
      color: #555;
    }

    .bookings-container {
      background: rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
      backdrop-filter: blur(10px);
      padding: 30px;
      overflow-x: auto;
      transition: all 0.3s ease-in-out;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
    }

    th, td {
      padding: 16px 18px;
      text-align: left;
    }

    th {
      background-color: #f1f1f1;
      font-weight: 600;
    }

    tr:nth-child(even) {
      background-color: #fafafa;
    }

    tr:hover {
      background-color: #f0f8ff;
      transition: 0.3s;
    }

    .status-dropdown {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      background-color: white;
      font-size: 14px;
      cursor: pointer;
      min-width: 120px;
    }

    .status-dropdown:focus {
      outline: none;
      border-color: #4CAF50;
      box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
    }

    .payment-paid {
      background-color: #d4edda;
      color: #155724;
      border-color: #c3e6cb;
    }

    .payment-pending {
      background-color: #fff3cd;
      color: #856404;
      border-color: #ffeaa7;
    }

    .payment-failed {
      background-color: #f8d7da;
      color: #721c24;
      border-color: #f5c6cb;
    }

    .booking-confirmed {
      background-color: #d4edda;
      color: #155724;
      border-color: #c3e6cb;
    }

    .booking-pending {
      background-color: #fff3cd;
      color: #856404;
      border-color: #ffeaa7;
    }

    .booking-cancelled {
      background-color: #f8d7da;
      color: #721c24;
      border-color: #f5c6cb;
    }

    .action-btns {
      display: flex;
      gap: 8px;
    }

    .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
      transition: all 0.2s;
    }

    .btn-edit {
      background-color: #4CAF50;
      color: white;
    }

    .btn-delete {
      background-color: #f44336;
      color: white;
    }

    .btn-view {
      background-color: #2196F3;
      color: white;
    }

    .btn:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }

    footer {
      background: #4e4e4e;
      color: white;
      padding: 25px 30px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 30px;
      margin-left: 220px;
    }

    footer h4 {
      margin-bottom: 10px;
    }

    footer hr {
      width: 30px;
      border: 2px solid #FFD700;
      margin-bottom: 15px;
    }

    footer ul {
      list-style: none;
    }

    footer ul li {
      margin-bottom: 10px;
    }

    footer ul li a {
      color: #ccc;
      text-decoration: none;
      transition: color 0.3s;
    }

    footer ul li a:hover {
      color: #FFD700;
    }

    .footer-col .social-links a {
      display: inline-block;
      height: 40px;
      width: 40px;
      background-color: rgba(255,255,255,0.2);
      margin:0 10px 10px 0;
      text-align: center;
      line-height: 40px;
      border-radius: 50%;
      color: #ffffff;
      transition: all 0.5s ease;
    }
    
    .footer-col .social-links a:hover {
      color: #24262b;
      background-color: #ffffff;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media screen and (max-width: 768px) {
      .sidebar {
        position: relative;
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
        justify-content: space-around;
        padding: 15px;
      }

      .main-content {
        margin-left: 0;
        padding: 20px;
      }

      header h1 {
        margin-left: 0;
        font-size: 22px;
      }

      .bookings-container {
        padding: 20px;
      }

      th, td {
        padding: 10px;
        font-size: 13px;
      }

      footer {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="logo">EvenRaw</div>
      <a href="usersbe.php"><i class="fas fa-users"></i> Users</a>
      <a href="admin_bookings.php" class="active"><i class="fas fa-calendar-check"></i> Bookings</a>
      <a href="#"><i class="fas fa-images"></i> Portfolio</a>
      <a href="#"><i class="fas fa-box-open"></i> Packages</a>
      <a href="#"><i class="fas fa-chart-line"></i> Analysis</a>
      <a href="#"><i class="fas fa-address-book"></i> Contact List</a>
      <a href="#"><i class="fas fa-comment-alt"></i> Feedbacks</a>
    </div>

    <div class="main-content">
      <header>
        <h1>Bookings</h1>
        <div class="user-info">👤 <?php echo htmlspecialchars($admin_name); ?></div>
      </header>

      <?php if (isset($error_message)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
          <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
          <i class="fas fa-check-circle"></i> Booking deleted successfully!
        </div>
      <?php endif; ?>
      
      <div class="bookings-container">
        <table>
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Customer ID</th>
              <th>Date</th>
              <th>Payment Status</th>
              <th>Booking Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($bookings)): ?>
              <?php foreach ($bookings as $booking): ?>
                <tr>
                  <td>B<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></td>
                  <td>A<?php echo str_pad($booking['customer_id'], 4, '0', STR_PAD_LEFT); ?></td>
                  <td><?php echo date('Y.m.d', strtotime($booking['booking_date'])); ?></td>
                  <td>
                    <select class="status-dropdown payment-status" data-booking-id="<?php echo $booking['id']; ?>" data-type="payment">
                      <option value="Select Status">Select Status</option>
                      <option value="paid" <?php echo ($booking['payment_status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                      <option value="pending" <?php echo ($booking['payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                      <option value="failed" <?php echo ($booking['payment_status'] == 'failed') ? 'selected' : ''; ?>>Failed</option>
                    </select>
                  </td>
                  <td>
                    <select class="status-dropdown booking-status" data-booking-id="<?php echo $booking['id']; ?>" data-type="booking">
                      <option value="Select Status">Select Status</option>
                      <option value="confirmed" <?php echo ($booking['booking_status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                      <option value="pending" <?php echo ($booking['booking_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                      <option value="cancelled" <?php echo ($booking['booking_status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                  </td>
                  <td class="action-btns">
                    <button class="btn btn-view" onclick="viewBooking(<?php echo $booking['id']; ?>)">
                      <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-edit" onclick="editBooking(<?php echo $booking['id']; ?>)">
                      <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-delete" onclick="confirmDelete(<?php echo $booking['id']; ?>)">
                      <i class="fas fa-trash-alt"></i> Delete
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                  <i class="fas fa-calendar-times" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
                  <p>No bookings found</p>
                  <p style="font-size: 14px; margin-top: 10px;">Bookings will appear here once customers make reservations</p>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <footer>
    <div>
      <h4>Menu</h4>
      <hr>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Portfolio</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Contact Us</a></li>
        <li><a href="#">Quote</a></li>
      </ul>
    </div>
    <div>
      <h4>Get Help</h4>
      <hr>
      <ul>
        <li><a href="#">FAQ</a></li>
        <li><a href="#">Reservations</a></li>
      </ul>
    </div>
    <div>
      <h4>Events</h4>
      <hr>
      <ul>
        <li><a href="#">Weddings</a></li>
        <li><a href="#">Birthdays</a></li>
        <li><a href="#">Graduations</a></li>
      </ul>
    </div>
    <div>
      <h4>Follow Us</h4>
      <hr>
      <div class="footer-col">
        <div class="social-links">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-whatsapp"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Update status styles when dropdowns change
    document.addEventListener('DOMContentLoaded', function() {
      const paymentDropdowns = document.querySelectorAll('.payment-status');
      const bookingDropdowns = document.querySelectorAll('.booking-status');
      
      // Set initial styles
      updateStatusStyles();
      
      // Add change event listeners
      paymentDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
          updatePaymentStatus(this.value, this.dataset.bookingId);
          updateStatusStyles();
        });
      });
      
      bookingDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
          updateBookingStatus(this.value, this.dataset.bookingId);
          updateStatusStyles();
        });
      });
    });

    function updateStatusStyles() {
      // Update payment status styles
      document.querySelectorAll('.payment-status').forEach(dropdown => {
        dropdown.className = 'status-dropdown payment-status';
        if (dropdown.value === 'paid') {
          dropdown.classList.add('payment-paid');
        } else if (dropdown.value === 'pending') {
          dropdown.classList.add('payment-pending');
        } else if (dropdown.value === 'failed') {
          dropdown.classList.add('payment-failed');
        }
      });
      
      // Update booking status styles
      document.querySelectorAll('.booking-status').forEach(dropdown => {
        dropdown.className = 'status-dropdown booking-status';
        if (dropdown.value === 'confirmed') {
          dropdown.classList.add('booking-confirmed');
        } else if (dropdown.value === 'pending') {
          dropdown.classList.add('booking-pending');
        } else if (dropdown.value === 'cancelled') {
          dropdown.classList.add('booking-cancelled');
        }
      });
    }

    function updatePaymentStatus(status, bookingId) {
      // Send AJAX request to update payment status
      fetch('update_booking_status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          booking_id: bookingId,
          payment_status: status,
          type: 'payment'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log('Payment status updated successfully');
        } else {
          console.error('Error updating payment status:', data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }

    function updateBookingStatus(status, bookingId) {
      // Send AJAX request to update booking status
      fetch('update_booking_status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          booking_id: bookingId,
          booking_status: status,
          type: 'booking'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log('Booking status updated successfully');
        } else {
          console.error('Error updating booking status:', data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }

    function viewBooking(bookingId) {
      // Redirect to booking details page
      window.location.href = 'view_booking.php?id=' + bookingId;
    }

    function editBooking(bookingId) {
      // Redirect to edit booking page
      window.location.href = 'edit_booking.php?id=' + bookingId;
    }

    function confirmDelete(bookingId) {
      if (confirm('Are you sure you want to delete this booking?')) {
        // Redirect to delete handler
        window.location.href = 'delete_booking.php?id=' + bookingId;
      }
    }
  </script>
</body>
</html> 