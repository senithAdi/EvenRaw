<?php include 'db_connectPortfolio.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>EvenRaw - Portfolio</title>
	<link rel="stylesheet" href="portfolio.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

	<style>
		.portfolio-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			gap: 20px;
			padding: 20px 0;
		}
		.portfolio-item {
			background: #fff;
			border-radius: 12px;
			overflow: hidden;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
			transition: transform .3s ease;
		}
		.portfolio-item:hover { transform: translateY(-5px); }
		.portfolio-item img {
			width: 100%;
			height: 250px;
			object-fit: cover;
			display: block;
		}
		.portfolio-caption { padding: 12px; text-align: center; }
		.category-section { margin: 30px 0; }
		.category-section .toggle-btn {
			background: #ffd700; border: none; padding: 12px 20px; border-radius: 8px;
			font-size: 16px; font-weight: 600; cursor: pointer; margin-bottom: 12px;
		}
		.category-content { display: none; }
		.see-more-btn {
			background: #ffd700; border: none; padding: 6px 12px; border-radius: 6px;
			font-size: 12px; font-weight: 500; cursor: pointer; color: #333; margin-top: 10px;
		}
		@media (max-width:768px){
			.portfolio-grid { grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 15px; }
			.portfolio-item img { height: 200px; }
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
			<a href="#" class="user"><img src="man.png" style="width:40px;height:40px;"></a>
		</nav>
	</header>

	<section>
		<h1>Explore Our Photography Categories</h1>

		<div class="category-section">
			<button class="toggle-btn" onclick="togglePhotos('commercial')">Commercial Photography ▼</button>
			<div id="commercial" class="category-content">
				<div class="portfolio-grid" data-category="commercial">
					<?php
					$stmt = $conn->prepare("SELECT image FROM portfolio WHERE category='commercial' ORDER BY id DESC LIMIT 12");
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if ($rows) {
						foreach ($rows as $r) {
							$src = 'uploads/' . htmlspecialchars($r['image']);
							echo '<div class="portfolio-item"><img src="'.$src.'" alt="Commercial"><div class="portfolio-caption"><h3>Commercial</h3></div></div>';
						}
					}
					?>
				</div>
				<button class="see-more-btn" onclick="seeMore('commercial')">See More</button>
			</div>
		</div>

		<div class="category-section">
			<button class="toggle-btn" onclick="togglePhotos('food')">Food Photography ▼</button>
			<div id="food" class="category-content">
				<div class="portfolio-grid" data-category="food">
					<?php
					$stmt = $conn->prepare("SELECT image FROM portfolio WHERE category='food' ORDER BY id DESC LIMIT 12");
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if ($rows) {
						foreach ($rows as $r) {
							$src = 'uploads/' . htmlspecialchars($r['image']);
							echo '<div class="portfolio-item"><img src="'.$src.'" alt="Food"><div class="portfolio-caption"><h3>Food</h3></div></div>';
						}
					}
					?>
				</div>
				<button class="see-more-btn" onclick="seeMore('food')">See More</button>
			</div>
		</div>

		<div class="category-section">
			<button class="toggle-btn" onclick="togglePhotos('hotel')">Hotel Photography ▼</button>
			<div id="hotel" class="category-content">
				<div class="portfolio-grid" data-category="hotel">
					<?php
					$stmt = $conn->prepare("SELECT image FROM portfolio WHERE category='hotel' ORDER BY id DESC LIMIT 12");
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if ($rows) {
						foreach ($rows as $r) {
							$src = 'uploads/' . htmlspecialchars($r['image']);
							echo '<div class="portfolio-item"><img src="'.$src.'" alt="Hotel"><div class="portfolio-caption"><h3>Hotel</h3></div></div>';
						}
					}
					?>
				</div>
				<button class="see-more-btn" onclick="seeMore('hotel')">See More</button>
			</div>
		</div>

		<div class="category-section">
			<button class="toggle-btn" onclick="togglePhotos('wedding')">Wedding Photography ▼</button>
			<div id="wedding" class="category-content">
				<div class="portfolio-grid" data-category="wedding">
					<?php
					$stmt = $conn->prepare("SELECT image FROM portfolio WHERE category='wedding' ORDER BY id DESC LIMIT 12");
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if ($rows) {
						foreach ($rows as $r) {
							$src = 'uploads/' . htmlspecialchars($r['image']);
							echo '<div class="portfolio-item"><img src="'.$src.'" alt="Wedding"><div class="portfolio-caption"><h3>Wedding</h3></div></div>';
						}
					}
					?>
				</div>
				<button class="see-more-btn" onclick="seeMore('wedding')">See More</button>
			</div>
		</div>
	</section>

	<footer>
		<!-- your existing footer -->
	</footer>

	<script>
    // Track metrics function with debugging
    async function trackMetric(type, category) {
        console.log('Attempting to track:', type, 'for category:', category);
        
        try {
            const formData = new FormData();
            formData.append('type', type);
            formData.append('category', category);
            
            const response = await fetch('track_event.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            const result = await response.text();
            console.log('Response text:', result);
            
            if (response.ok) {
                console.log('✅ Successfully tracked', type, 'for', category);
            } else {
                console.error('❌ Failed to track', type, 'for', category, 'Status:', response.status);
            }
        } catch (error) {
            console.error('❌ Error tracking metric:', error);
        }
    }

    // Toggle photos and track view
    function togglePhotos(category) {
        console.log('Toggling photos for:', category);
        const div = document.getElementById(category);
        const willShow = (div.style.display === 'none' || !div.style.display);
        div.style.display = willShow ? 'block' : 'none';
        
        if (willShow) {
            console.log('Category opened, tracking view for:', category);
            // Track view only once per session per category
            const key = 'viewed-' + category;
            if (!sessionStorage.getItem(key)) {
                console.log('First time viewing, tracking view');
                trackMetric('view', category);
                sessionStorage.setItem(key, '1');
            } else {
                console.log('Already viewed this session');
            }
        }
    }

    // Track clicks on see more button
    function seeMore(category) {
        console.log('See more clicked for:', category);
        trackMetric('click', category);
        alert('Loading more ' + category + ' photos...');
    }

    // Track clicks on images
    document.addEventListener('click', function(e) {
        const img = e.target.closest('.portfolio-item img');
        if (img) {
            const grid = e.target.closest('.portfolio-grid');
            const category = grid ? grid.getAttribute('data-category') : '';
            if (category) {
                console.log('Image clicked for category:', category);
                trackMetric('click', category);
            }
        }
    });

    // Show first category by default
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded, showing commercial category');
        togglePhotos('commercial');
    });
</script>
</body>
</html>