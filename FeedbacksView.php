<?php
// Start session and check admin authentication
// session_start();
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: admin_login.php');
//     exit();
// }

// Database connection
require_once 'db_connect.php';

// Handle delete feedback
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->execute([$delete_id]);
    header('Location: FeedbacksView.php?deleted=1');
    exit();
}

// Handle toggle showcase status
if (isset($_GET['toggle_showcase'])) {
    $feedback_id = $_GET['toggle_showcase'];
    $stmt = $conn->prepare("UPDATE feedback SET showcase = NOT showcase WHERE id = ?");
    $stmt->execute([$feedback_id]);
    header('Location: FeedbacksView.php');
    exit();
}

// Fetch all feedbacks
$stmt = $conn->query("SELECT * FROM feedback ORDER BY submission_date DESC");
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get admin name for display
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Feedbacks</title>
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
    }

    .user-info {
      font-weight: 500;
      color: #555;
    }

    /* Feedback Cards Styles */
    .feedbacks-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 25px;
      margin-top: 30px;
    }

    .feedback-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      padding: 25px;
      transition: transform 0.3s, box-shadow 0.3s;
      position: relative;
      border-left: 4px solid #FFD700;
    }

    .feedback-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .feedback-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .feedback-name {
      font-size: 1.2rem;
      font-weight: 600;
      color: #222;
    }

    .feedback-rating {
      display: flex;
      gap: 3px;
    }

    .star {
      color: #FFD700;
      font-size: 1.1rem;
    }

    .feedback-service {
      display: inline-block;
      background: #f0f0f0;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      margin-bottom: 10px;
      color: #555;
    }

    .feedback-comments {
      color: #444;
      line-height: 1.6;
      margin-bottom: 15px;
    }

    .feedback-date {
      font-size: 0.8rem;
      color: #888;
      margin-bottom: 15px;
    }

    .feedback-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
      border-top: 1px solid #eee;
      padding-top: 15px;
    }

    .btn {
      padding: 8px 15px;
      border-radius: 5px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s;
      border: none;
      font-size: 0.9rem;
    }

    .btn-delete {
      background: #ff4444;
      color: white;
    }

    .btn-delete:hover {
      background: #cc0000;
    }

    .btn-showcase {
      background: #4CAF50;
      color: white;
    }

    .btn-showcase:hover {
      background: #388E3C;
    }

    .btn-showcase.active {
      background: #FFD700;
      color: #111;
    }

    .showcase-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: #FFD700;
      color: #111;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .no-feedbacks {
      text-align: center;
      padding: 50px;
      color: #666;
      font-size: 1.1rem;
      grid-column: 1 / -1;
    }

    .success-message {
      background: #4CAF50;
      color: white;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
      animation: fadeIn 0.5s;
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

      .feedbacks-container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="logo">EvenRaw</div>
              <a href="usersbe.php"><i class="fas fa-users"></i> Users</a>
      <a href="admin_bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
      <a href="#"><i class="fas fa-images"></i> Portfolio</a>
      <a href="#"><i class="fas fa-box-open"></i> Packages</a>
      <a href="#"><i class="fas fa-chart-line"></i> Analysis</a>
      <a href="#"><i class="fas fa-address-book"></i> Contact List</a>
      <a href="FeedbacksView.php"><i class="fas fa-comment-alt"></i> Feedbacks</a>
    </div>

    <div class="main-content">
      <header>
        <h1>Customer Feedbacks</h1>
        <div class="user-info">👤 <?php echo htmlspecialchars($admin_name); ?></div>
      </header>

      <?php if (isset($_GET['deleted'])): ?>
        <div class="success-message">
          Feedback deleted successfully!
        </div>
      <?php endif; ?>

      <div class="feedbacks-container">
        <?php if (empty($feedbacks)): ?>
          <div class="no-feedbacks">
            <i class="far fa-comment-dots" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
            <p>No feedbacks found. Check back later!</p>
          </div>
        <?php else: ?>
          <?php foreach ($feedbacks as $feedback): ?>
            <div class="feedback-card">
              <?php if ($feedback['showcase']): ?>
                <span class="showcase-badge">Showcased</span>
              <?php endif; ?>
              
              <div class="feedback-header">
                <div class="feedback-name"><?php echo htmlspecialchars($feedback['name']); ?></div>
                <div class="feedback-rating">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star <?php echo $i <= $feedback['rating'] ? 'star' : 'far fa-star'; ?>"></i>
                  <?php endfor; ?>
                </div>
              </div>
              
              <span class="feedback-service"><?php echo htmlspecialchars(ucfirst($feedback['service'])); ?></span>
              
              <p class="feedback-comments"><?php echo htmlspecialchars($feedback['comments']); ?></p>
              
              <div class="feedback-date">
                Submitted on <?php echo date('M j, Y g:i A', strtotime($feedback['submission_date'])); ?>
                <?php if ($feedback['contact_me']): ?>
                  <br><i class="fas fa-check-circle" style="color: #4CAF50;"></i> Wants to be contacted
                <?php endif; ?>
              </div>
              
              <div class="feedback-actions">
                <a href="?delete_id=<?php echo $feedback['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this feedback?');">
                  <i class="fas fa-trash"></i> Delete
                </a>
                <a href="?toggle_showcase=<?php echo $feedback['id']; ?>" class="btn btn-showcase <?php echo $feedback['showcase'] ? 'active' : ''; ?>">
                  <i class="fas fa-<?php echo $feedback['showcase'] ? 'star' : 'star-half-alt'; ?>"></i> 
                  <?php echo $feedback['showcase'] ? 'Showcasing' : 'Showcase'; ?>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
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
</body>
</html>