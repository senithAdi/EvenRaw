<?php
include 'db_connectPortfolio.php';

$category = $_GET['category'] ?? 'commercial';

try {
    $stmt = $conn->prepare("SELECT * FROM portfolio WHERE category = ? ORDER BY id DESC LIMIT 3");
    $stmt->execute([$category]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($result) > 0) {
        foreach ($result as $row) {
            echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="' . ucfirst($category) . '" width="200">';
        }
    } else {
        // Show default images based on category
        switch($category) {
            case 'commercial':
                echo '<img src="uploads/Com1.jpg" alt="Com 1" width="200">';
                echo '<img src="uploads/Com2.jpg" alt="Com 2" width="200">';
                echo '<img src="uploads/Com3.jpg" alt="Com 3" width="200">';
                break;
            case 'food':
                echo '<img src="/Users/macbook/Desktop/Evenraw/DSC08252.JPG" alt="Food 1" width="200">';
                echo '<img src="/Users/macbook/Desktop/Evenraw/DSC07995.jpg" alt="Food 2" width="200">';
                echo '<img src="/Users/macbook/Desktop/Evenraw/_DSC6212-Enhanced-NR.JPG" alt="Food 3" width="200">';
                break;
            case 'hotel':
                echo '<img src="uploads/hotel1.jpg" alt="Hotel 1" width="200">';
                echo '<img src="uploads/hotel2.jpg" alt="Hotel 2" width="200">';
                echo '<img src="uploads/hotel3.jpg" alt="Hotel 3" width="200">';
                break;
            case 'wedding':
                echo '<img src="uploads/wedding1.jpg" alt="Wedding 1" width="200">';
                echo '<img src="uploads/wedding2.jpg" alt="Wedding 2" width="200">';
                echo '<img src="uploads/wedding3.jpg" alt="Wedding 3" width="200">';
                break;
        }
    }
} catch(PDOException $e) {
    // Show default images if database error
    echo '<img src="uploads/Com1.jpg" alt="Default" width="200">';
    echo '<img src="uploads/Com2.jpg" alt="Default" width="200">';
    echo '<img src="uploads/Com3.jpg" alt="Default" width="200">';
}
?>
<?php 
$_GET['category'] = 'commercial';
include 'portfolio_images.php'; 
?>