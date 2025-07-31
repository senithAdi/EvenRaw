<?php
$target_dir = "/Users/macbook/Desktop/Evenraw";
$target_file = $target_dir . basename($_FILES["foodImage"]["name"]);
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if the file is an actual image
$check = getimagesize($_FILES["foodImage"]["tmp_name"]);
if($check !== false) {
    if (move_uploaded_file($_FILES["foodImage"]["tmp_name"], $target_file)) {
        echo "✅ Photo uploaded successfully.";
    } else {
        echo "❌ Error uploading the photo.";
    }
} else {
    echo "⚠️ File is not a valid image.";
}
?>
