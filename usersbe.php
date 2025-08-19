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
    <!-- Add this CSS in the style section -->
.btn-generate-report {
  background: linear-gradient(to right, #ffcc00, #ff9900);
  color: #333;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.btn-generate-report:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

/* Report Modal Styles */
.report-modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  animation: fadeIn 0.3s ease;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.2);
  width: 50%;
  max-width: 600px;
  position: relative;
  animation: slideIn 0.4s ease;
}

.close-modal {
  color: #aaa;
  position: absolute;
  top: 15px;
  right: 25px;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close-modal:hover {
  color: #000;
}

.report-options {
  margin: 20px 0;
}

.option-group {
  margin-bottom: 20px;
}

.option-group label { 
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
  font-weight: 600;
  cursor: pointer;
}

.option-group input[type="checkbox"] {
  width: auto;
  margin: 0;
  cursor: pointer;
}

.option-group select, .option-group input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.format-options {
  display: flex;
  gap: 15px;
  margin-top: 10px;
}

.format-option {
  flex: 1;
  text-align: center;
  padding: 15px;
  border: 2px solid #eee;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.format-option:hover, .format-option.selected {
  border-color: #ffcc00;
  background-color: #fffce6;
}

.format-option i {
  font-size: 24px;
  margin-bottom: 8px;
  display: block;
}

.generate-btn {
  background: linear-gradient(to right, #ffcc00, #ff9900);
  color: #333;
  border: none;
  padding: 12px 25px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  width: 100%;
  margin-top: 20px;
  transition: all 0.3s;
}

.generate-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

@keyframes slideIn {
  from {transform: translateY(-50px); opacity: 0;}
  to {transform: translateY(0); opacity: 1;}
}

/* Loading spinner */
.loading {
  display: none;
  text-align: center;
  margin: 20px 0;
}

.spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #ffcc00;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="logo">EvenRaw</div>
      <a href="admin_users.php"><i class="fas fa-users"></i> Users</a>
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
        <button class="btn-generate-report" onclick="openReportModal()">
        <i class="fas fa-chart-pie"></i> Generate Report
        </button>
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
<!-- Report Generation Modal -->
<div id="reportModal" class="report-modal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeReportModal()">&times;</span>
    <h2>Generate User Report</h2>
    
    <div class="report-options">
      <div class="option-group">
        <label for="reportType">Report Type</label>
        <select id="reportType">
          <option value="detailed">Detailed User List</option>
          <option value="summary">Statistical Summary</option>
          <option value="analysis">User Analysis</option>
        </select>
      </div>
      
      <div class="option-group">
        <label>Format</label>
        <div class="format-options">
          <div class="format-option selected" data-format="pdf">
            <i class="fas fa-file-pdf"></i>
            <span>PDF</span>
          </div>
          <div class="format-option" data-format="excel">
            <i class="fas fa-file-excel"></i>
            <span>Excel</span>
          </div>
          <div class="format-option" data-format="csv">
            <i class="fas fa-file-csv"></i>
            <span>CSV</span>
          </div>
        </div>
      </div>
      
      <div class="option-group">
        <label for="includeCharts">
          <input type="checkbox" id="includeCharts" checked >
          Include Charts & Graphs
        </label>
      </div>
    </div>
    
    <div class="loading" id="reportLoading">
      <div class="spinner"></div>
      <p>Generating your report...</p>
    </div>
    
    <button class="generate-btn" onclick="generateReport()">
      Generate Report Now
    </button>
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
  <script>
// Report generation functions
let selectedFormat = 'pdf';

function openReportModal() {
  document.getElementById('reportModal').style.display = 'block';
}

function closeReportModal() {
  document.getElementById('reportModal').style.display = 'none';
}

// Select format option
document.querySelectorAll('.format-option').forEach(option => {
  option.addEventListener('click', function() {
    document.querySelectorAll('.format-option').forEach(opt => {
      opt.classList.remove('selected');
    });
    this.classList.add('selected');
    selectedFormat = this.getAttribute('data-format');
  });
});

// Close modal if clicked outside
window.onclick = function(event) {
  const modal = document.getElementById('reportModal');
  if (event.target == modal) {
    closeReportModal();
  }
}

function generateReport() {
  const reportType = document.getElementById('reportType').value;
  const includeCharts = document.getElementById('includeCharts').checked;
  
  // Show loading animation
  document.getElementById('reportLoading').style.display = 'block';
  
  // Simulate processing time
  setTimeout(function() {
    // Based on the selected format, trigger download
    switch(selectedFormat) {
      case 'pdf':
        generatePDFReport(reportType);
        break;
      case 'excel':
        generateExcelReport(reportType);
        break;
      case 'csv':
        generateCSVReport(reportType);
        break;
    }
    
    // Hide loading animation
    document.getElementById('reportLoading').style.display = 'none';
    
    // Close the modal
    closeReportModal();
    
    // Show success message
    alert('Report generated successfully!');
  }, 2000);
}

function generatePDFReport(type, includeCharts) {
    try {
        window.open('generate_pdf_report.php?type=' + type + '&charts=' + (includeCharts ? 1 : 0), '_blank');
    } catch (error) {
        console.error('Error generating PDF:', error);
        // Fallback to HTML report
        window.open('generate_html_report.php?type=' + type + '&charts=' + (includeCharts ? 1 : 0), '_blank');
    }
}

function generateExcelReport(type) {
    window.open('generate_excel_report.php?type=' + type, '_blank');
}

function generateCSVReport(type) {
    window.open('generate_csv_report.php?type=' + type, '_blank');
}
</script>
</body>
</html>