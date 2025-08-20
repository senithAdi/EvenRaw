<?php
session_start();
echo "<h1>Debug Bookings Page</h1>";

echo "<h2>Session Information:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Database Connection Test:</h2>";
try {
    require_once 'db_connect.php';
    echo "✅ Database connection successful!<br>";
    
    // Test if bookings table exists
    $stmt = $conn->prepare("SHOW TABLES LIKE 'bookings'");
    $stmt->execute();
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "✅ Bookings table exists!<br>";
        
        // Count bookings
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings");
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 Total bookings: " . $count['count'] . "<br>";
        
        // Show sample data
        $stmt = $conn->prepare("SELECT * FROM bookings LIMIT 3");
        $stmt->execute();
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($bookings)) {
            echo "<h3>Sample Bookings:</h3>";
            echo "<pre>";
            print_r($bookings);
            echo "</pre>";
        } else {
            echo "⚠️ No bookings found in database<br>";
        }
        
    } else {
        echo "❌ Bookings table does not exist!<br>";
        echo "You need to run the setup_bookings_table.sql file first.<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<h2>PHP Info:</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current working directory: " . getcwd() . "<br>";

echo "<h2>File Check:</h2>";
$files_to_check = [
    'db_connect.php',
    'admin_bookings.php',
    'setup_bookings_table.sql'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Make sure XAMPP is running (Apache + MySQL)</li>";
echo "<li>Import setup_bookings_table.sql into your database</li>";
echo "<li>Check if you're logged in as admin</li>";
echo "<li>Try accessing admin_bookings.php again</li>";
echo "</ol>";

echo "<p><a href='admin_bookings.php'>Try Admin Bookings Page</a></p>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?> 