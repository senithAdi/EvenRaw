<?php
include 'db_connectPortfolio.php';

// Add a test message to see if this file is being loaded
echo "<!-- PHP file is loading -->";

try {
    $stmt = $conn->prepare("SELECT * FROM portfolio ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        echo "<!-- Found " . count($result) . " images -->";
        foreach ($result as $row) {
            echo '<div class="portfolio-item" data-category="' . htmlspecialchars($row['category'] ?? 'general') . '">';
            echo '<input type="checkbox" class="portfolio-checkbox" value="' . $row['id'] . '">';
            
            // Use simple relative path
            $image_path = "uploads/" . htmlspecialchars($row['image']);
            echo '<img src="' . $image_path . '" alt="Portfolio Image" style="width: 100%; height: 200px; object-fit: cover;">';
            
            echo '<div class="portfolio-info">';
            echo '<h3>' . ucfirst(htmlspecialchars($row['category'])) . ' Photography</h3>';
            echo '<p>ID: ' . $row['id'] . '</p>';
            echo '<p>File: ' . htmlspecialchars($row['image']) . '</p>';
            echo '<div class="portfolio-actions">';
            echo '<a href="portfolioEdit.html?id=' . $row['id'] . '" class="btn-edit">Edit</a>';
            echo '<form method="POST" action="portfolio_delete.php" style="display:inline;">
                    <input type="hidden" name="id" value="' . $row['id'] . '">
                    <button type="submit" class="btn-delete" onclick="return confirm(\'Are you sure?\')">Delete</button>
                  </form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p style="text-align: center; color: #666; grid-column: 1 / -1;">No portfolio items available. Click "Add New Photo" to get started.</p>';
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>