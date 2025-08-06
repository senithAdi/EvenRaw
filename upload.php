<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "evenraw";

// Create connection
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category = $_POST['category'];
$imageName = $_FILES['image']['name'];
$imageTmp = $_FILES['image']['tmp_name'];
$uploadDir = "uploads/" . basename($imageName);

if (move_uploaded_file($imageTmp, $uploadDir)) {
    $stmt = $conn->prepare("INSERT INTO portfolio (category, image_path) VALUES (?, ?)");
    $stmt->bind_param("ss", $category, $uploadDir);
    if ($stmt->execute()) {
        echo "Photo uploaded and saved to database!";
    } else {
        echo "Database error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "File upload failed.";
}

$conn->close();
?>
