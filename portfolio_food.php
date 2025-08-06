<?php
include 'db_connectPortfolio.php';

try {
    $stmt = $conn->prepare("SELECT * FROM portfolio WHERE category = 'food' ORDER BY id DESC LIMIT 6");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($result) > 0) {
        foreach ($result as $row) {
            $image_path = "uploads/" . htmlspecialchars($row['image']);
            echo '<img src="' . $image_path . '" alt="Food Photography" width="200" style="border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">';
        }
    } else {
        // Show default images if no database images
        echo '<img src="/Users/macbook/Desktop/Evenraw/DSC08252.JPG" alt="Food 1" width="200">';
        echo '<img src="/Users/macbook/Desktop/Evenraw/DSC07995.jpg" alt="Food 2" width="200">';
        echo '<img src="/Users/macbook/Desktop/Evenraw/_DSC6212-Enhanced-NR.JPG" alt="Food 3" width="200">';
    }
} catch(PDOException $e) {
    // Show default images on error
    echo '<img src="/Users/macbook/Desktop/Evenraw/DSC08252.JPG" alt="Food 1" width="200">';
    echo '<img src="/Users/macbook/Desktop/Evenraw/DSC07995.jpg" alt="Food 2" width="200">';
    echo '<img src="/Users/macbook/Desktop/Evenraw/_DSC6212-Enhanced-NR.JPG" alt="Food 3" width="200">';
}
?>