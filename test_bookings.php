<?php
session_start();
require_once 'db_connect.php';

// Simple test page - no admin check for now
echo "<h1>Test Bookings Page</h1>";

try {
    // Fetch all bookings data
    $stmt = $conn->prepare("SELECT id, customer_id, booking_date, payment_status, booking_status, service_type, total_amount FROM bookings ORDER BY id DESC");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Database Connection: ✅ Working</h2>";
    echo "<h2>Total Bookings: " . count($bookings) . "</h2>";
    
    if (!empty($bookings)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Customer ID</th><th>Service</th><th>Date</th><th>Payment</th><th>Status</th>";
        echo "</tr>";
        
        foreach ($bookings as $booking) {
            echo "<tr>";
            echo "<td>B" . str_pad($booking['id'], 4, '0', STR_PAD_LEFT) . "</td>";
            echo "<td>A" . str_pad($booking['customer_id'], 4, '0', STR_PAD_LEFT) . "</td>";
            echo "<td>" . htmlspecialchars($booking['service_type']) . "</td>";
            echo "<td>" . date('Y.m.d', strtotime($booking['booking_date'])) . "</td>";
            echo "<td>" . ucfirst($booking['payment_status']) . "</td>";
            echo "<td>" . ucfirst($booking['booking_status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Error: " . $e->getMessage() . "</h2>";
}

echo "<br><br>";
echo "<h2>Test Links:</h2>";
echo "<p><a href='admin_bookings.php'>Admin Bookings Page</a></p>";
echo "<p><a href='usersbe.php'>Users Page</a></p>";
echo "<p><a href='debug_bookings.php'>Debug Page</a></p>";
echo "<p><a href='login.php'>Login Page</a></p>";
?> 