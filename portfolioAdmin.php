<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Portfolio</title>
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
    }

    .portfolio-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .portfolio-item {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      position: relative;
    }

    .portfolio-item:hover {
      transform: translateY(-5px);
    }

    .portfolio-item img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .portfolio-info {
      padding: 15px;
    }

    .portfolio-info h3 {
      margin-bottom: 15px;
      color: #333;
      text-align: center;
    }

    .portfolio-actions {
      display: flex;
      gap: 10px;
      justify-content: center;
    }

    .btn-edit {
      background: #2980b9;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      font-size: 12px;
    }

    .btn-delete {
      background: #e74c3c;
      color: white;
      border: none;
      padding: 8px 15px;
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

    .bulk-actions {
      margin-bottom: 20px;
    }

    .checkbox-item {
      margin-right: 10px;
    }

    .category-filter {
      margin-bottom: 20px;
    }

    .category-filter select {
      padding: 8px 15px;
      border-radius: 5px;
      border: 1px solid #ddd;
      margin-right: 10px;
    }

    .portfolio-checkbox {
      position: absolute;
      top: 10px;
      left: 10px;
      z-index: 10;
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

      .portfolio-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="logo">EvenRaw</div>
      <a href="usersbe.html">Users</a>
      <a href="BookingsBE.php">Bookings</a>
      <a href="portfolioAdmin.html" style="background: rgba(0, 0, 0, 0.1);">Portfolio</a>
      <a href="#">Packages</a>
      <a href="#">Analysis</a>
      <a href="contactlist.html">Contact List</a>
      <a href="#">Feedbacks</a>
    </div>

    <div class="main-content">
      <header>
        <h1>Portfolio Management</h1>
        <div class="user-info">👤 Admin</div>
      </header>

      <div class="portfolio-container">
        <div class="category-filter">
          <select id="categoryFilter">
            <option value="">All Categories</option>
            <option value="commercial">Commercial Photography</option>
            <option value="food">Food Photography</option>
            <option value="hotel">Hotel Photography</option>
            <option value="wedding">Wedding Photography</option>
          </select>
        </div>

        <div class="bulk-actions">
          <input type="checkbox" id="selectAll" class="checkbox-item">
          <label for="selectAll">Select All</label>
          <button class="btn-delete" onclick="deleteSelected()">Delete Selected</button>
        </div>

       <div class="portfolio-grid">
    <?php include 'portfolio_admin_display.php'; ?>
</div>

        <button class="btn-add" onclick="window.location.href='portfolioUpload.html'">
          <i class="fas fa-plus"></i> Add New Photo
        </button>
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
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.portfolio-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });

    // Category filter
    document.getElementById('categoryFilter').addEventListener('change', function() {
      const category = this.value;
      const items = document.querySelectorAll('.portfolio-item');
      
      items.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        if (category === '' || itemCategory === category) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });

    // Delete selected items
    function deleteSelected() {
      const selectedItems = document.querySelectorAll('.portfolio-checkbox:checked');
      if (selectedItems.length === 0) {
        alert('Please select items to delete');
        return;
      }
      
      if (confirm('Are you sure you want to delete the selected items?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'portfolio_bulk_delete.php';
        
        selectedItems.forEach(checkbox => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'selected[]';
          input.value = checkbox.value;
          form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
</body>
</html>