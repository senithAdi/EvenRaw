<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Fetch all users data
$stmt = $conn->prepare("SELECT id, name, email, nic_number, contact_number FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current admin name for display
$admin_name = $_SESSION['name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Users</title>
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
      <a href="usersbe.php" class="active"><i class="fas fa-users"></i> Users</a>
      <a href="admin_bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
      <a href="#"><i class="fas fa-images"></i> Portfolio</a>
      <a href="#"><i class="fas fa-box-open"></i> Packages</a>
      <a href="#"><i class="fas fa-chart-line"></i> Analysis</a>
      <a href="#"><i class="fas fa-address-book"></i> Contact List</a>
      <a href="#"><i class="fas fa-comment-alt"></i> Feedbacks</a>
    </div>

    <div class="main-content">
      <header>
        <h1>Users Management</h1>
        <div class="user-info">👤 <?php echo htmlspecialchars($admin_name); ?></div>
      </header>

      <div class="bookings-container">
        <table>
          <thead>
            <tr>
              <th>User ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>NIC</th>
              <th>Contact Number</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['nic_number'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['contact_number'] ?? 'N/A'); ?></td>
                <td class="action-btns">
                  <button class="btn btn-edit" onclick="editUser(<?php echo $user['id']; ?>)">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                  <button class="btn btn-delete" onclick="confirmDelete(<?php echo $user['id']; ?>)">
                    <i class="fas fa-trash-alt"></i> Delete
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
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
    function editUser(userId) {
      // You can implement edit functionality here
      // For example, redirect to an edit page:
      window.location.href = 'edit_user.php?id=' + userId;
    }

    function confirmDelete(userId) {
      if (confirm('Are you sure you want to delete this user?')) {
        // You can implement delete functionality here
        // For example, redirect to a delete handler:
        window.location.href = 'delete_user.php?id=' + userId;
      }
    }
  </script>
</body>
</html>