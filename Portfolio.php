<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EvenRaw</title>
  <link rel="stylesheet" href="portfolio.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  
  <style>
    /* Grid styles for portfolio photos */
    .portfolio-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      padding: 20px 0;
    }
    
    .portfolio-item {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    
    .portfolio-item:hover {
      transform: translateY(-5px);
    }
    
    .portfolio-item img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      display: block;
    }
    
    .portfolio-caption {
      padding: 15px;
      text-align: center;
    }
    
    .portfolio-caption h3 {
      margin: 0 0 10px 0;
      color: #333;
      font-size: 16px;
    }
    
    /* Category section styles */
    .category-section {
      margin: 30px 0;
    }
    
    .category-section button {
      background: #000000ff;
      border: none;
      padding: 12px 25px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-bottom: 15px;
      transition: background 0.3s;
    }
    
    .category-section button:hover {
      background: #ffed4e;
    }
    
    .category-content {
      display: none;
    }
    
    .category-content.show {
      display: block;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
      .portfolio-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
      }
      
      .portfolio-item img {
        height: 200px;
      }
       button.see-more-btn {
    background: #ffd700;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    margin-top: 15px;
    transition: background 0.3s;
    color: #333;
  }
  
  button.see-more-btn:hover {
    background: #ffed4e;
  }
  
    }
  </style>
</head>

<body>
  <header>
    <div class="logo">EvenRaw</div>
    <nav>
      <a href="Home.html">Home</a>
      <a href="About us.html">About Us</a>
      <a href="Portfolio.php">Portfolio</a>
      <a href="contact us.html">Contact Us</a>
      <a href="Get Quote.html" class="btn-yellow">Get a Quote</a>
      <a href="#" class="user"> <img src="man.png" style="width: 40px; height:40px;"></a>
    </nav>
  </header>

  <section>
    <h1>Explore Our Photography Categories</h1>
    
    <!-- Commercial Photography -->
    <div class="category-section">
      <button onclick="togglePhotos('commercial')">Commercial Photography ▼</button>
      <div id="commercial" class="category-content">
        <div class="portfolio-grid">
          <?php
          include 'db_connectPortfolio.php';
          try {
              $stmt = $conn->prepare("SELECT * FROM portfolio WHERE category = 'commercial' ORDER BY id DESC LIMIT 6");
              $stmt->execute();
              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              if (count($result) > 0) {
                  foreach ($result as $row) {
                      $image_path = "uploads/" . htmlspecialchars($row['image']);
                      echo '<div class="portfolio-item">';
                      echo '<img src="' . $image_path . '" alt="Commercial Photography">';
                      echo '<div class="portfolio-caption">';
                      echo '<h3>Commercial Photography</h3>';
                      echo '</div>';
                      echo '</div>';
                  }
              } else {
                  // Show default images if no database images
                  echo '<div class="portfolio-item">';
                  echo '<img src="uploads/Com1.jpg" alt="Commercial 1">';
                  echo '<div class="portfolio-caption"><h3>Commercial Photography</h3></div>';
                  echo '</div>';
                  echo '<div class="portfolio-item">';
                  echo '<img src="uploads/Com2.jpg" alt="Commercial 2">';
                  echo '<div class="portfolio-caption"><h3>Commercial Photography</h3></div>';
                  echo '</div>';
                  echo '<div class="portfolio-item">';
                  echo '<img src="uploads/Com3.jpg" alt="Commercial 3">';
                  echo '<div class="portfolio-caption"><h3>Commercial Photography</h3></div>';
                  echo '</div>';
              }
          } catch(PDOException $e) {
              // Show default images on error
              echo '<div class="portfolio-item">';
              echo '<img src="uploads/Com1.jpg" alt="Commercial 1">';
              echo '<div class="portfolio-caption"><h3>Commercial Photography</h3></div>';
              echo '</div>';
              echo '<div class="portfolio-item">';
              echo '<img src="uploads/Com2.jpg" alt="Commercial 2">';
              echo '<div class="portfolio-caption"><h3>Commercial Photography</h3></div>';
              echo '</div>';
              echo '<div class="portfolio-item">';
              echo '<img src="uploads/Com3.jpg" alt="Commercial 3">';
              echo '<div class="portfolio-caption"><h3>Commercial Photography</h3></div>';
              echo '</div>';
          }
          ?>
        </div>
        <button onclick="seeMore('commercial')" style="margin-top: 20px;">See More</button>
      </div>
    </div>

    <!-- Food Photography -->
    <div class="category-section">
      <button onclick="togglePhotos('food')">Food Photography ▼</button>
      <div id="food" class="category-content">
        <div class="portfolio-grid">
          <?php
          try {
              $stmt = $conn->prepare("SELECT * FROM portfolio WHERE category = 'food' ORDER BY id DESC LIMIT 6");
              $stmt->execute();
              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              if (count($result) > 0) {
                  foreach ($result as $row) {
                      $image_path = "uploads/" . htmlspecialchars($row['image']);
                      echo '<div class="portfolio-item">';
                      echo '<img src="' . $image_path . '" alt="Food Photography">';
                      echo '<div class="portfolio-caption">';
                      echo '<h3>Food Photography</h3>';
                      echo '</div>';
                      echo '</div>';
                  }
              } else {
                  // Show default images if no database images
                  echo '<div class="portfolio-item">';
                  echo '<img src="/Users/macbook/Desktop/Evenraw/DSC08252.JPG" alt="Food 1">';
                  echo '<div class="portfolio-caption"><h3>Food Photography</h3></div>';
                  echo '</div>';
                  echo '<div class="portfolio-item">';
                  echo '<img src="/Users/macbook/Desktop/Evenraw/DSC07995.jpg" alt="Food 2">';
                  echo '<div class="portfolio-caption"><h3>Food Photography</h3></div>';
                  echo '</div>';
                  echo '<div class="portfolio-item">';
                  echo '<img src="/Users/macbook/Desktop/Evenraw/_DSC6212-Enhanced-NR.JPG" alt="Food 3">';
                  echo '<div class="portfolio-caption"><h3>Food Photography</h3></div>';
                  echo '</div>';
              }
          } catch(PDOException $e) {
              // Show default images on error
              echo '<div class="portfolio-item">';
              echo '<img src="/Users/macbook/Desktop/Evenraw/DSC08252.JPG" alt="Food 1">';
              echo '<div class="portfolio-caption"><h3>Food Photography</h3></div>';
              echo '</div>';
              echo '<div class="portfolio-item">';
              echo '<img src="/Users/macbook/Desktop/Evenraw/DSC07995.jpg" alt="Food 2">';
              echo '<div class="portfolio-caption"><h3>Food Photography</h3></div>';
              echo '</div>';
              echo '<div class="portfolio-item">';
              echo '<img src="/Users/macbook/Desktop/Evenraw/_DSC6212-Enhanced-NR.JPG" alt="Food 3">';
              echo '<div class="portfolio-caption"><h3>Food Photography</h3></div>';
              echo '</div>';
          }
          ?>
        </div>
        <button onclick="seeMore('food')" style="margin-top: 20px;">See More</button>
      </div>
    </div>

    <!-- Hotel Photography -->
    <div class="category-section">
      <button onclick="togglePhotos('hotel')">Hotel Photography ▼</button>
      <div id="hotel" class="category-content">
        <div class="portfolio-grid">
          <?php
          try {
              $stmt = $conn->prepare("SELECT * FROM portfolio WHERE category = 'hotel' ORDER BY id DESC LIMIT 6");
              $stmt->execute();
              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              if (count($result) > 0) {
                  foreach ($result as $row) {
                      $image_path = "uploads/" . htmlspecialchars($row['image']);
                      echo '<div class="portfolio-item">';
                      echo '<img src="' . $image_path . '" alt="Hotel Photography">';
                      echo '<div class="portfolio-caption">';
                      echo '<h3>Hotel Photography</h3>';
                      echo '</div>';
                      echo '</div>';
                  }
              } else {
                  // Show default images if no database images
                  echo '<div class="portfolio-item">';
                  echo '<img src="uploads/hotel1.jpg" alt="Hotel 1">';
                  echo '<div class="portfolio-caption"><h3>Hotel Photography</h3></div>';
                  echo '</div>';
                  echo '<div class="portfolio-item">';
                  echo '<img src="uploads/hotel2.jpg" alt="Hotel 2">';
                  echo '<div class="portfolio-caption"><h3>Hotel Photography</h3></div>';
                  echo '</div>';
                  echo '<div class="portfolio-item">';
                  echo '<img src="uploads/hotel3.jpg" alt="Hotel 3">';
                  echo '<div class="portfolio-caption"><h3>Hotel Photography</h3></div>';
                  echo '</div>';
              }
          } catch(PDOException $e) {
              // Show default images on error
              echo '<div class="portfolio-item">';
              echo '<img src="uploads/hotel1.jpg" alt="Hotel 1">';
              echo '<div class="portfolio-caption"><h3>Hotel Photography</h3></div>';
              echo '</div>';
              echo '<div class="portfolio-item">';
              echo '<img src="uploads/hotel2.jpg" alt="Hotel 2">';
              echo '<div class="portfolio-caption"><h3>Hotel Photography</h3></div>';
              echo '</div>';
              echo '<div class="portfolio-item">';
              echo '<img src="uploads/hotel3.jpg" alt="Hotel 3">';
              echo '<div class="portfolio-caption"><h3>Hotel Photography</h3></div>';
              echo '</div>';
          }
          ?>
        </div>
        <button onclick="seeMore('hotel')" style="margin-top: 20px;">See More</button>
      </div>
    </div>

    <!-- Wedding Photography -->
    <div class="category-section">
      <button onclick="togglePhotos('wedding')">Wedding Photography ▼</button>
      <div id="wedding" class="category-content">
        <div class="portfolio-grid">
          <?php
          try {
              $stmt = $conn->prepare("SELECT * FROM portfolio WHERE category = 'wedding' ORDER BY id DESC LIMIT 6");
              $stmt->execute();
              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              if (count($result) > 0) {
                  foreach ($result as $row) {
                      $image_path = "uploads/" . htmlspecialchars($row['image']);
                      echo '<div class="portfolio-item">';
                      echo '<img src="' . $image_path . '" alt="Wedding Photography">';
                      echo '<div class="portfolio-caption">';
                      echo '<h3>Wedding Photography</h3>';
                      echo '</div>';
                      echo '</div>';
                  }
              } else {
                  // Show default images if no database images
                  echo '<div class="portfolio-item">';
                  echo '<img src="uploads/wedding1.jpg" alt="Wedding 1">';
                  echo '<div class="portfolio-caption"><h3>Wedding Photography</h3></div>';
                  echo '</div>';
                  echo '<div class="portfolio-item">';
                  echo '<img src="uploads/wedding2.jpg" alt="Wedding 2">';
                  echo '<div class="portfolio-caption"><h3>Wedding Photography</h3></div>';
                  echo '</div>';
                  echo '<div class="portfolio-item">';
                  echo '<img src="uploads/wedding3.jpg" alt="Wedding 3">';
                  echo '<div class="portfolio-caption"><h3>Wedding Photography</h3></div>';
                  echo '</div>';
              }
          } catch(PDOException $e) {
              // Show default images on error
              echo '<div class="portfolio-item">';
              echo '<img src="uploads/wedding1.jpg" alt="Wedding 1">';
              echo '<div class="portfolio-caption"><h3>Wedding Photography</h3></div>';
              echo '</div>';
              echo '<div class="portfolio-item">';
              echo '<img src="uploads/wedding2.jpg" alt="Wedding 2">';
              echo '<div class="portfolio-caption"><h3>Wedding Photography</h3></div>';
              echo '</div>';
              echo '<div class="portfolio-item">';
              echo '<img src="uploads/wedding3.jpg" alt="Wedding 3">';
              echo '<div class="portfolio-caption"><h3>Wedding Photography</h3></div>';
              echo '</div>';
          }
          ?>
        </div>
        <button onclick="seeMore('wedding')" style="margin-top: 20px;">See More</button>
      </div>
    </div>
  </section>

  <footer>
    <div>
      <h4>Menu</h4>
      <hr>
      <ul>
        <li><a href="Home.html">Home</a></li>
        <li><a href="Portfolio.php">Portfolio</a></li>
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
    function togglePhotos(category) {
      const div = document.getElementById(category);
      if (div.style.display === 'none') {
        div.style.display = 'block';
      } else {
        div.style.display = 'none';
      }
    }

    function seeMore(category) {
      console.log('See more clicked for ' + category);
    }
  </script>
</body>
</html>