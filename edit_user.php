<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Get user ID from URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error_message'] = "User not found!";
    header("Location: usersbe.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $nic_number = trim($_POST['nic_number']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if email already exists (excluding current user)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        $errors[] = "Email already exists";
    }
    
    if (empty($errors)) {
        try {
            // Update user data
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, contact_number = ?, nic_number = ?, is_admin = ? WHERE id = ?");
            $stmt->execute([$name, $email, $contact_number, $nic_number, $is_admin, $user_id]);
            
            $_SESSION['success_message'] = "User updated successfully!";
            header("Location: usersbe.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Get current admin name for display
$admin_name = $_SESSION['name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit User - Admin</title>
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
      display: flex;
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

    .main-content {
      margin-left: 220px;
      padding: 40px;
      flex: 1;
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

    .edit-form-container {
      background: rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
      padding: 30px;
      max-width: 800px;
      margin: 0 auto;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    .form-group input, 
    .form-group select {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 16px;
      transition: border 0.3s;
    }

    .form-group input:focus, 
    .form-group select:focus {
      border-color: #FFD700;
      outline: none;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .checkbox-group input {
      width: auto;
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 30px;
    }

    .btn {
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.3s;
    }

    .btn-primary {
      background: #FFD700;
      color: #333;
    }

    .btn-primary:hover {
      background: #FFC000;
      transform: translateY(-2px);
    }

    .btn-secondary {
      background: #ddd;
      color: #333;
    }

    .btn-secondary:hover {
      background: #ccc;
    }

    .error-message {
      color: #e74c3c;
      margin-bottom: 20px;
      padding: 10px;
      background: #f8d7da;
      border-radius: 5px;
    }

    .error-message ul {
      margin-left: 20px;
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
        font-size: 22px;
      }

      .edit-form-container {
        padding: 20px;
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
      <a href="#"><i class="fas fa-comment-alt"></i> Feedbacks</a>
    </div>

    <div class="main-content">
      <header>
        <h1>Edit User</h1>
        <div class="user-info">👤 <?php echo htmlspecialchars($admin_name); ?></div>
      </header>

      <div class="edit-form-container">
        <?php if (!empty($errors)): ?>
          <div class="error-message">
            <ul>
              <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

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

          <div class="form-group checkbox-group">
            <input type="checkbox" id="is_admin" name="is_admin" value="1" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
            <label for="is_admin">Admin User</label>
          </div>

          <div class="form-actions">
            <a href="usersbe.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>