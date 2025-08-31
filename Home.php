<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EvenRaw</title>
  <link rel="stylesheet" href="Home_CSS.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
/* Testimonials Section */
.testimonials-section {
  padding: 80px 50px;
  background-color: #f8f8f8;
  text-align: center;
}

.testimonials-section h2 {
  font-size: 2.5rem;
  margin-bottom: 15px;
  color: #222;
}

.testimonials-section p {
  max-width: 700px;
  margin: 0 auto 40px;
  color: #555;
}

.testimonials-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
  max-width: 1200px;
  margin: 0 auto;
}

.testimonial-card {
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  text-align: left;
  transition: transform 0.3s;
}

.testimonial-card:hover {
  transform: translateY(-10px);
}

.testimonial-rating {
  margin-bottom: 15px;
  color: #FFD700;
}

.testimonial-text {
  font-style: italic;
  color: #444;
  line-height: 1.6;
  margin-bottom: 20px;
  position: relative;
}

.testimonial-text::before,
.testimonial-text::after {
  color: #FFD700;
  font-size: 2rem;
  line-height: 1;
  position: absolute;
}

.testimonial-text::before {
  content: '"';
  top: -10px;
  left: -15px;
}

.testimonial-text::after {
  content: '"';
  bottom: -25px;
  right: -15px;
}

.testimonial-author {
  margin-top: 20px;
}

.testimonial-author strong {
  display: block;
  color: #222;
}

.testimonial-author span {
  font-size: 0.9rem;
  color: #666;
}

.no-testimonials {
  grid-column: 1 / -1;
  padding: 30px;
  color: #666;
  font-style: italic;
}

@media (max-width: 768px) {
  .testimonials-section {
    padding: 60px 20px;
  }
}
  </style>
</head>

<body>
  <header>
    <div class="logo">EvenRaw</div>
    <nav>
      <a href="#">Home</a>
      <a href="About us.html">About Us</a>
      <a href="Portfolio.php">Portfolio</a>
      <a href="contact us.html">Contact Us</a>
      <a href="Get Quote.html" class="btn-yellow">Get a Quote</a>
<a href="<?php echo isset($_SESSION['logged_in']) ? 'User profile.php' : 'Login.html'; ?>" class="btn-yellow">
    <i class="fas fa-user"></i>
</a>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h1>Capturing Moments, Creating Memories</h1>
      <p>We specialize in photography, videography, and graphic design that brings your vision to life.</p>
      <a href="#" class="btn-yellow" style="margin-top:250px;">Get a Quote</a>
    </div>
  </section>

  <section class="creativity-section">
  <div class="creativity-content">
    <div class="text-content">
      <h3>Creativity in every detail</h3>
      <p>At EvenRaw, we don’t just create visuals — we craft experiences. From design to delivery, every detail reflects our bold, vibrant identity.</p>
    </div>
    <div class="image-content">
      <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085" alt="Creative Work">
    </div>
  </div>
</section>

  <section class="gallery-section">
  <h2>Gallery</h2>
  <p>Explore our work through different moments we’ve captured.</p>
  <div class="gallery-grid">
    <div class="gallery-item">
      <a href="portfolio.html">
        <img src="Food.png" alt="Sports Photography">
        <div class="overlay">
          <h3>Sports Photography</h3>
        </div>
      </a>
    </div>
    <div class="gallery-item">
      <a href="portfolio.html">
        <img src="Pic.png" alt="Wedding Photography">
        <div class="overlay">
          <h3>Wedding Photography</h3>
        </div>
      </a>
    </div>
    <div class="gallery-item">
      <a href="portfolio.html">
        <img src="https://images.unsplash.com/photo-1589302168068-964664d93dc0" alt="Graduation Photography">
        <div class="overlay">
          <h3>Graduation Photography</h3>
        </div>
      </a>
    </div>
    <div class="gallery-item">
      <a href="portfolio.html">
        <img src="Food 2.png" alt="Event Photography">
        <div class="overlay">
          <h3>Event Photography</h3>
        </div>
      </a>
    </div>
  </div>
</section>
<section class="services-section">
  <h2>Our Services</h2>
  <p class="services-description">
    We specialize in capturing the essence of your story through stunning photography, cinematic videography, and bold graphic design.
  </p>

  <div class="services-grid">
    <div class="service-card">
      <div class="service-icon">
        <img src="https://cdn-icons-png.flaticon.com/512/2921/2921822.png" alt="Photography">
      </div>
      <h3>Photography</h3>
      <p>From portraits to events, we capture every moment with precision and creativity.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">
        <img src="https://cdn-icons-png.flaticon.com/512/2922/2922688.png" alt="Videography">
      </div>
      <h3>Videography</h3>
      <p>We craft cinematic experiences that breathe life into your stories and brand identity.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">
        <img src="https://cdn-icons-png.flaticon.com/512/167/167707.png" alt="Graphic Design">
      </div>
      <h3>Graphic Design</h3>
      <p>Bold, vibrant designs that elevate your brand across digital and print media.</p>
    </div>
  </div>
</section>


<section class="testimonials-section">
  <h2>What Our Clients Say</h2>
  <p>Hear from our satisfied customers about their experiences with EvenRaw.</p>
  
  <div class="testimonials-container">
    <?php
    require_once 'db_connect.php';
    $stmt = $conn->query("SELECT * FROM feedback WHERE showcase = 1 ORDER BY submission_date DESC LIMIT 3");
    $showcased_feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($showcased_feedbacks)): ?>
      <div class="no-testimonials">
        <p>Check back soon for client testimonials!</p>
      </div>
    <?php else: ?>
      <?php foreach ($showcased_feedbacks as $feedback): ?>
        <div class="testimonial-card">
          <div class="testimonial-rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <i class="fas fa-star <?php echo $i <= $feedback['rating'] ? 'gold' : ''; ?>"></i>
            <?php endfor; ?>
          </div>
          <p class="testimonial-text">"<?php echo htmlspecialchars($feedback['comments']); ?>"</p>
          <div class="testimonial-author">
            <strong><?php echo htmlspecialchars($feedback['name']); ?></strong>
            <span><?php echo htmlspecialchars(ucfirst($feedback['service'])); ?> Service</span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

  <footer>
    <div>
      <h4>Menu</h4>
      <hr>
      <ul>
        <li><a href="Home.php">Home</a></li>
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
        <li><a href="Get Quote.html">Reservations</a></li>
        <li><a href="#">Support</a></li>
        <li><a href="#">Terms of Service</a></li>
      </ul>
    </div>
    <div>
      <h4>Events</h4>
      <hr>
      <ul>
        <li><a href="Portfolio.php">Weddings</a></li>
        <li><a href="Portfolio.php">Birthdays</a></li>
        <li><a href="Portfolio.php">Graduations</a></li>
        <li><a href="Portfolio.php">Corporate Events</a></li>
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
</body>

</html>
