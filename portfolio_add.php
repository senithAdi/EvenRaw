<?php
include 'db_connectPortfolio.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);

    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            try {
                $stmt = $conn->prepare("INSERT INTO portfolio (image, category) VALUES (?, ?)");
                $stmt->execute([$image, $category]);
                
                // Redirect to the .php version
                header("Location: portfolioAdmin.php");
                exit();
            } catch(PDOException $e) {
                echo "Database Error: " . $e->getMessage();
            }
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "File upload failed with error code: " . $_FILES['image']['error'];
    }
}
?>