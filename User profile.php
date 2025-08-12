<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $nic_number = $_POST['nic_number'];
    
    // Password update only if new password is provided
    $password = $user['password'];
    if (!empty($_POST['new_password'])) {
        $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    }
    
    try {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, contact_number = ?, nic_number = ? WHERE id = ?");
        $stmt->execute([$name, $email, $password, $contact_number, $nic_number, $user_id]);
        
        // Refresh user data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $success_message = "Profile updated successfully!";
    } catch (PDOException $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>
  <link rel="stylesheet" href="Home_CSS.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #fff;
      color: #333;
      line-height: 1.6;
    }

    /* Header Styles */
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 50px;
      background: #111;
      color: white;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .logo {
      font-size: 1.8em;
      font-weight: 700;
      letter-spacing: 2px;
      background: linear-gradient(45deg, #FFD700, #FFA500);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    nav {
      display: flex;
      gap: 25px;
      align-items: center;
    }

    nav a {
      text-decoration: none;
      color: white;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
    }

    nav a:hover {
      color: #FFD700;
      transform: translateY(-2px);
    }

    nav a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -5px;
      left: 50%;
      background: #FFD700;
      transition: all 0.3s ease;
      transform: translateX(-50%);
    }

    nav a:hover::after {
      width: 100%;
    }

    .btn-yellow {
      background: linear-gradient(45deg, #FFD700, #FFA500);
      color: black;
      padding: 12px 25px;
      border-radius: 30px;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
    }

    .btn-yellow:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
    }

    /* User Profile Section */
    .user-profile-section {
      background: linear-gradient(135deg, #FFD700, #FFF59D);
      min-height: 80vh;
      padding: 60px 50px;
      position: relative;
    }

    .breadcrumb {
      color: #666;
      font-size: 0.9rem;
      margin-bottom: 30px;
    }

    .profile-container {
      max-width: 600px;
      margin: 0 auto;
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    /* User Header */
    .user-header {
      display: flex;
      align-items: center;
      padding: 30px;
      background: #f0f0f0;
      border-radius: 15px;
      margin-bottom: 30px;
    }

    .user-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: #333;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 30px;
      position: relative;
      overflow: hidden;
    }

    .user-avatar::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: #333;
      border-radius: 50%;
    }

    .user-avatar i {
      font-size: 2.5rem;
      color: white;
      position: relative;
      z-index: 1;
    }

    .user-info h2 {
      font-size: 1.8rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 5px;
    }

    .user-info p {
      color: #666;
      font-size: 1rem;
    }

    /* Menu Items */
    .profile-menu {
      list-style: none;
    }

    .profile-menu li {
      margin-bottom: 15px;
    }

    .profile-menu a {
      display: flex;
      align-items: center;
      padding: 20px 25px;
      background: #f8f8f8;
      border-radius: 12px;
      text-decoration: none;
      color: #666;
      font-weight: 500;
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }

    .profile-menu a:hover {
      background: #e8e8e8;
      color: #333;
      transform: translateX(5px);
    }

    .profile-menu a i {
      font-size: 1.2rem;
      margin-right: 20px;
      width: 20px;
      text-align: center;
      color: #888;
    }

    .profile-menu a:hover i {
      color: #FFD700;
    }

    .logout-link {
      color: #e74c3c !important;
    }

    .logout-link:hover {
      background: #ffeaea !important;
      color: #c0392b !important;
    }

    .logout-link i {
      color: #e74c3c !important;
    }

    /* Edit Profile Form */
    .edit-form {
      display: none;
      padding: 20px;
      background: #f9f9f9;
      border-radius: 10px;
      margin-top: 20px;
    }

    .edit-form.active {
      display: block;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
    }

    .form-group input {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }

    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: #FFD700;
      color: #333;
    }

    .btn-primary:hover {
      background: #FFA500;
    }

    .btn-secondary {
      background: #ddd;
      color: #333;
    }

    .btn-secondary:hover {
      background: #ccc;
    }

    .edit-btn {
      background: #FFD700;
      color: #333;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
      margin-left: 10px;
      transition: all 0.3s ease;
    }

    .edit-btn:hover {
      background: #FFA500;
    }

    .message {
      padding: 10px 15px;
      margin-bottom: 15px;
      border-radius: 5px;
      font-weight: 500;
    }

    .success {
      background: #d4edda;
      color: #155724;
    }

    .error {
      background: #f8d7da;
      color: #721c24;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      nav {
        flex-direction: column;
        gap: 10px;
      }

      .user-profile-section {
        padding: 40px 20px;
      }

      .profile-container {
        padding: 30px 20px;
      }

      .user-header {
        flex-direction: column;
        text-align: center;
      }

      .user-avatar {
        margin-right: 0;
        margin-bottom: 20px;
      }

      .profile-menu a {
        padding: 15px 20px;
      }
    }

    @media (max-width: 480px) {
      .profile-container {
        padding: 20px 15px;
      }

      .user-header {
        padding: 20px;
      }

      .profile-menu a {
        padding: 12px 15px;
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>
  <!-- Header -->
  <header>
    <div class="logo">Evenraw</div>
    <nav>
      <a href="Home.php">Home</a>
      <a href="About us.html">About Us</a>
      <a href="Portfolio.html">Portfolio</a>
      <a href="contact us.html">Contact Us</a>
      <a href="Get Quote.html" class="btn-yellow">Get a Quote</a>
      <a href="Login.html" class="btn-yellow">
        <i class="fas fa-user"></i>
      </a>
    </nav>
  </header>

  <!-- User Profile Section -->
  <section class="user-profile-section">
    <div class="breadcrumb">
      <h2>User Profile</h2>
    </div>
    
    <div class="profile-container">
      <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
      <?php endif; ?>
      
      <!-- User Header -->
      <div class="user-header">
        <div class="user-avatar">
          <i class="fas fa-user"></i>
        </div>
        <div class="user-info">
          <h2><?php echo htmlspecialchars($user['name']); ?></h2>
          <p><?php echo $user['is_admin'] ? 'Admin' : ''; ?></p>
          <button class="edit-btn" onclick="toggleEditForm()">Edit Profile</button>
        </div>
      </div>

      <!-- Edit Profile Form -->
      <div id="editForm" class="edit-form">
        <form method="POST" action="">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
          </div>
          <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="tel" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label for="nic_number">NIC Number</label>
            <input type="text" id="nic_number" name="nic_number" value="<?php echo htmlspecialchars($user['nic_number'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label for="new_password">New Password (leave blank to keep current)</label>
            <input type="password" id="new_password" name="new_password">
          </div>
          <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="toggleEditForm()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>

      <!-- Profile Menu -->
      <ul class="profile-menu">
        <li>
          <a href="#">
            <i class="fas fa-envelope"></i>
            <?php echo htmlspecialchars($user['email']); ?>
          </a>
        </li>
        <?php if (!empty($user['contact_number'])): ?>
        <li>
          <a href="#">
            <i class="fas fa-phone"></i>
            <?php echo htmlspecialchars($user['contact_number']); ?>
          </a>
        </li>
        <?php endif; ?>
        <?php if (!empty($user['nic_number'])): ?>
        <li>
          <a href="#">
            <i class="fas fa-id-card"></i>
            <?php echo htmlspecialchars($user['nic_number']); ?>
          </a>
        </li>
        <?php endif; ?>
        <li>
          <a href="#">
            <i class="fas fa-star"></i>
            Evenraw Points
          </a>
        </li>
        <li>
          <a href="#">
            <i class="fas fa-tag"></i>
            Offers
          </a>
        </li>
        <li>
          <a href="#">
            <i class="fas fa-clock"></i>
            Previous Bookings
          </a>
        </li>
        <li>
          <a href="logout.php" class="logout-link" onclick="return confirmLogout()">
            <i class="fas fa-sign-out-alt"></i>
            Log out
          </a>
        </li>
      </ul>
    </div>
  </section>
<script>
function confirmLogout() {
  return confirm('Are you sure you want to log out?');
}
</script>
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
        <li><a href="#">Support</a></li>
        <li><a href="#">Terms of Service</a></li>
      </ul>
    </div>
    <div>
      <h4>Events</h4>
      <hr>
      <ul>
        <li><a href="#">Weddings</a></li>
        <li><a href="#">Birthdays</a></li>
        <li><a href="#">Graduations</a></li>
        <li><a href="#">Corporate Events</a></li>
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
    <div class="copyright">
      <p>© 2025 EvenRaw Photography. All rights reserved. | Professional Photography Services</p>
    </div>
  </footer>

  <script>
    function toggleEditForm() {
      const form = document.getElementById('editForm');
      form.classList.toggle('active');
    }
  </script>
</body>

</html>