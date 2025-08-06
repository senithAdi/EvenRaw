<?php
include 'db_connectPortfolio.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    try {
        // Get image filename before deleting
        $stmt = $conn->prepare("SELECT image FROM portfolio WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Delete file from server
            $file_path = "uploads/" . $row['image'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM portfolio WHERE id = ?");
            $stmt->execute([$id]);
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

header("Location: portfolioAdmin.html");
exit();
?>