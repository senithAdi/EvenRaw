<?php
session_start();
require_once 'db_connect.php';
require_once 'db_connectPortfolio.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Get current admin name for display
$admin_name = $_SESSION['name'] ?? 'Admin';

// Fetch comprehensive data from all admin pages (same as admin_dashboard.php)
try {
    // Users data (from usersbe.php)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE is_admin = 0");
    $stmt->execute();
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->prepare("SELECT id, name, email, created_at FROM users WHERE is_admin = 0 ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Bookings data (from admin_bookings.php)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings");
    $stmt->execute();
    $total_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->prepare("SELECT id, customer_id, booking_date, payment_status, booking_status, service_type, total_amount FROM bookings ORDER BY id DESC LIMIT 5");
    $stmt->execute();
    $recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Portfolio data (from portfolioAdmin.php)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM portfolio");
    $stmt->execute();
    $total_portfolio = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->prepare("SELECT id, image, category FROM portfolio ORDER BY id DESC LIMIT 5");
    $stmt->execute();
    $recent_portfolio = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Feedback data (from FeedbacksView.php)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM feedback");
    $stmt->execute();
    $total_feedback = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->prepare("SELECT id, name, email, message, submission_date, showcase FROM feedback ORDER BY submission_date DESC LIMIT 5");
    $stmt->execute();
    $recent_feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contact data (from contactlist.html - assuming contacts table)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM contacts");
    $stmt->execute();
    $total_contacts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->prepare("SELECT id, name, email, message, created_at FROM contacts ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Packages data (from packages.html)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM packages");
    $stmt->execute();
    $total_packages = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->prepare("SELECT id, name, category, price FROM packages ORDER BY id DESC LIMIT 5");
    $stmt->execute();
    $recent_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Portfolio metrics
    $current_month = date('Y-m');
    $stmt = $conn->prepare("SELECT SUM(total_views) as total_views, SUM(total_clicks) as total_clicks FROM portfolio_metrics WHERE month_year = ?");
    $stmt->execute([$current_month]);
    $portfolio_metrics = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Summary Dashboard - EvenRaw</title>
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

    .portfolio-container {
      background: rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
      backdrop-filter: blur(10px);
      padding: 30px;
      overflow-x: auto;
      transition: all 0.3s ease-in-out;
      margin-bottom: 30px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      position: relative;
      padding: 25px;
      text-align: center;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-card i {
      font-size: 2.5em;
      color: #ffd700;
      margin-bottom: 15px;
    }

    .stat-card h3 {
      font-size: 2em;
      color: #333;
      margin-bottom: 10px;
    }

    .stat-card p {
      color: #666;
      font-size: 14px;
    }

    .content-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .content-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      position: relative;
    }

    .content-card:hover {
      transform: translateY(-5px);
    }

    .content-card h3 {
      background: #2980b9;
      color: white;
      padding: 15px;
      margin: 0;
      font-size: 18px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .content-info {
      padding: 15px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #333;
    }

    tr:hover {
      background-color: #f8f9fa;
    }

    .btn-edit {
      background: #2980b9;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      font-size: 12px;
    }

    .btn-delete {
      background: #e74c3c;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 12px;
    }

    .btn-add {
      background: #27ae60;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      margin-top: 20px;
    }

    .btn-add:hover {
      background: #229954;
    }

    .report-actions {
      display: flex;
      gap: 12px;
      align-items: center;
      margin: 0 0 18px 0;
    }

    .btn-report {
      background: #ffd700;
      color: #111;
      text-decoration: none;
      padding: 10px 16px;
      border-radius: 8px;
      font-weight: 600;
      border: 1px solid rgba(0,0,0,0.08);
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      transition: background 0.2s, transform 0.05s;
    }

    .btn-report:hover { 
      background: #ffea6c; 
    }
    
    .btn-report:active { 
      transform: translateY(1px); 
    }

    .status {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 500;
    }

    .status.pending {
      background: #fff3cd;
      color: #856404;
    }

    .status.confirmed {
      background: #d4edda;
      color: #155724;
    }

    .status.completed {
      background: #cce5ff;
      color: #004085;
    }

    .status.showcase {
      background: #e8f5e8;
      color: #2d5a2d;
    }

    .status.paid {
      background: #d4edda;
      color: #155724;
    }

    .status.failed {
      background: #f8d7da;
      color: #721c24;
    }

    .status.cancelled {
      background: #f8d7da;
      color: #721c24;
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

    .footer-col .social-links a{
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
    .footer-col .social-links a:hover{
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

      .portfolio-container {
        padding: 20px;
      }

      .stats-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
      }

      .content-grid {
        grid-template-columns: 1fr;
      }

      header h1 {
        margin-left: 0;
        font-size: 22px;
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
      <a href="#" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
      <a href="usersbe.php"><i class="fas fa-users"></i> Users</a>
      <a href="admin_bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
      <a href="portfolioAdmin.php"><i class="fas fa-images"></i> Portfolio</a>
      <a href="packages.html"><i class="fas fa-box-open"></i> Packages</a>
      <a href="contactlist.html"><i class="fas fa-address-book"></i> Contact List</a>
      <a href="FeedbacksView.php"><i class="fas fa-comment-alt"></i> Feedbacks</a>
    </div>


    <div class="main-content">
      <header>
        <h1>Admin Summary Dashboard</h1>
        <div class="user-info">👤 <?php echo htmlspecialchars($admin_name); ?></div>
      </header>

      <div class="report-actions">
        <a href="generate_summary_report.php" class="btn-report">Generate Report</a>
        <a href="download_summary_report.php" class="btn-report">Download PDF</a>
      </div>

      <!-- Statistics Overview -->
      <div class="portfolio-container">
        <h3 style="margin-bottom: 20px; color: #333; font-size: 20px;">Overview Statistics</h3>
        <div class="stats-grid">
          <div class="stat-card">
            <i class="fas fa-users"></i>
            <h3><?php echo $total_users ?? 0; ?></h3>
            <p>Total Users</p>
          </div>
          
          <div class="stat-card">
            <i class="fas fa-calendar-check"></i>
            <h3><?php echo $total_bookings ?? 0; ?></h3>
            <p>Total Bookings</p>
          </div>
          
          <div class="stat-card">
            <i class="fas fa-images"></i>
            <h3><?php echo $total_portfolio ?? 0; ?></h3>
            <p>Portfolio Items</p>
          </div>
          
          <div class="stat-card">
            <i class="fas fa-comments"></i>
            <h3><?php echo $total_feedback ?? 0; ?></h3>
            <p>Feedback Received</p>
          </div>

          <div class="stat-card">
            <i class="fas fa-address-book"></i>
            <h3><?php echo $total_contacts ?? 0; ?></h3>
            <p>Contact Messages</p>
          </div>

          <div class="stat-card">
            <i class="fas fa-box"></i>
            <h3><?php echo $total_packages ?? 0; ?></h3>
            <p>Service Packages</p>
          </div>
        </div>
      </div>

      <!-- Recent Activity Grid -->
      <div class="content-grid">
        <!-- Recent Users -->
        <div class="content-card">
          <h3><i class="fas fa-user-plus"></i> Recent Users</h3>
          <div class="content-info">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recent_users)): ?>
                  <?php foreach ($recent_users as $user): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></td>
                      <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                      <td><?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" style="text-align: center; color: #666;">No recent users</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Bookings -->
        <div class="content-card">
          <h3><i class="fas fa-calendar-alt"></i> Recent Bookings</h3>
          <div class="content-info">
            <table>
              <thead>
                <tr>
                  <th>Customer ID</th>
                  <th>Service</th>
                  <th>Status</th>
                  <th>Payment</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recent_bookings)): ?>
                  <?php foreach ($recent_bookings as $booking): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($booking['customer_id'] ?? 'N/A'); ?></td>
                      <td><?php echo htmlspecialchars($booking['service_type'] ?? 'N/A'); ?></td>
                      <td>
                        <span class="status <?php echo strtolower($booking['booking_status'] ?? 'pending'); ?>">
                          <?php echo htmlspecialchars($booking['booking_status'] ?? 'Pending'); ?>
                        </span>
                      </td>
                      <td>
                        <span class="status <?php echo strtolower($booking['payment_status'] ?? 'pending'); ?>">
                          <?php echo htmlspecialchars($booking['payment_status'] ?? 'Pending'); ?>
                        </span>
                      </td>
                      <td>$<?php echo number_format($booking['total_amount'] ?? 0, 2); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" style="text-align: center; color: #666;">No recent bookings</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Portfolio -->
        <div class="content-card">
          <h3><i class="fas fa-images"></i> Recent Portfolio Items</h3>
          <div class="content-info">
            <table>
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Category</th>
                  <th>ID</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recent_portfolio)): ?>
                  <?php foreach ($recent_portfolio as $item): ?>
                    <tr>
                      <td>
                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="Portfolio" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                      </td>
                      <td><?php echo htmlspecialchars($item['category'] ?? 'N/A'); ?></td>
                      <td><?php echo $item['id']; ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" style="text-align: center; color: #666;">No portfolio items</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Feedback -->
        <div class="content-card">
          <h3><i class="fas fa-comment-dots"></i> Recent Feedback</h3>
          <div class="content-info">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Showcase</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recent_feedback)): ?>
                  <?php foreach ($recent_feedback as $feedback): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($feedback['name'] ?? 'N/A'); ?></td>
                      <td><?php echo htmlspecialchars($feedback['email'] ?? 'N/A'); ?></td>
                      <td>
                        <span class="status <?php echo $feedback['showcase'] ? 'showcase' : 'pending'; ?>">
                          <?php echo $feedback['showcase'] ? 'Yes' : 'No'; ?>
                        </span>
                      </td>
                      <td><?php echo date('M d, Y', strtotime($feedback['submission_date'] ?? 'now')); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" style="text-align: center; color: #666;">No recent feedback</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Contacts -->
        <div class="content-card">
          <h3><i class="fas fa-envelope"></i> Recent Contact Messages</h3>
          <div class="content-info">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recent_contacts)): ?>
                  <?php foreach ($recent_contacts as $contact): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($contact['name'] ?? 'N/A'); ?></td>
                      <td><?php echo htmlspecialchars($contact['email'] ?? 'N/A'); ?></td>
                      <td><?php echo date('M d, Y', strtotime($contact['created_at'] ?? 'now')); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" style="text-align: center; color: #666;">No recent contacts</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Packages -->
        <div class="content-card">
          <h3><i class="fas fa-box"></i> Recent Packages</h3>
          <div class="content-info">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Category</th>
                  <th>Price</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recent_packages)): ?>
                  <?php foreach ($recent_packages as $package): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($package['name'] ?? 'N/A'); ?></td>
                      <td><?php echo htmlspecialchars($package['category'] ?? 'N/A'); ?></td>
                      <td><?php echo htmlspecialchars($package['price'] ?? 'N/A'); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" style="text-align: center; color: #666;">No packages available</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Portfolio Analytics -->
      <?php if (isset($portfolio_metrics) && ($portfolio_metrics['total_views'] > 0 || $portfolio_metrics['total_clicks'] > 0)): ?>
        <div class="portfolio-container">
          <h3 style="margin-bottom: 20px; color: #333; font-size: 20px;"><i class="fas fa-chart-line"></i> Portfolio Analytics (This Month)</h3>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="text-align: center;">
              <h4 style="color: #333; margin-bottom: 5px; font-size: 24px;"><?php echo $portfolio_metrics['total_views'] ?? 0; ?></h4>
              <p style="color: #666;">Total Views</p>
            </div>
            <div style="text-align: center;">
              <h4 style="color: #333; margin-bottom: 5px; font-size: 24px;"><?php echo $portfolio_metrics['total_clicks'] ?? 0; ?></h4>
              <p style="color: #666;">Total Clicks</p>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

<footer>
    <div>
      <h4>Menu</h4>
      <hr>
      <ul>
        <li><a href="Home.php">Home</a></li>
        <li><a href="Portfolio.html">Portfolio</a></li>
        <li><a href="About us.html">About Us</a></li>
        <li><a href="contact us.html">Contact Us</a></li>
        <li><a href="Get Quote.html">Quote</a></li>
      </ul>
    </div>
    <div>
      <h4>Get Help</h4>
      <hr>
      <ul>
        <li><a href="FAQ.html">FAQ</a></li>
        <li><a href="Get Quote.html">Reservations</a></li>
        <li><a href="#">Support</a></li>
        <li><a href="#">Terms of Service</a></li>
      </ul>
    </div>
    <div>
      <h4>Events</h4>
      <hr>
      <ul>
        <li><a href="Portfolio.html">Weddings</a></li>
        <li><a href="Portfolio.html">Birthdays</a></li>
        <li><a href="Portfolio.html">Graduations</a></li>
        <li><a href="Portfolio.html">Corporate Events</a></li>
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
</body>
</html>