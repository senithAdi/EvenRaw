<?php
include 'db_connectPortfolio.php';

try {
    $stmt = $conn->prepare("SELECT * FROM portfolio ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        foreach ($result as $row) {
            echo '<div class="portfolio-item" data-category="' . htmlspecialchars($row['category'] ?? 'general') . '">';
            echo '<input type="checkbox" class="portfolio-checkbox" value="' . $row['id'] . '">';
            echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="Portfolio Image">';
            echo '<div class="portfolio-info">';
            echo '<h3>' . ucfirst(htmlspecialchars($row['category'])) . ' Photography</h3>';
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