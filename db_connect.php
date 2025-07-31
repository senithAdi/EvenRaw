<?php
$host = 'localhost';
$dbname = 'evenraw';
$username = 'root'; // Change as per your setup
$password = ''; // Change as per your setup

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>