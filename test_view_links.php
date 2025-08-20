<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test View Booking Links</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .test-link { display: block; margin: 10px 0; padding: 10px; background: #f0f0f0; text-decoration: none; color: #333; border-radius: 5px; }
        .test-link:hover { background: #e0e0e0; }
    </style>
</head>
<body>
    <h1>Test View Booking Links</h1>
    <p>Click these links to test if the view booking page works:</p>
    
    <a href="view_booking.php?id=1" class="test-link">View Booking ID 1</a>
    <a href="view_booking.php?id=2" class="test-link">View Booking ID 2</a>
    <a href="view_booking.php?id=3" class="test-link">View Booking ID 3</a>
    <a href="view_booking.php?id=4" class="test-link">View Booking ID 4</a>
    <a href="view_booking.php?id=5" class="test-link">View Booking ID 5</a>
    
    <h2>Other Test Pages:</h2>
    <a href="test_bookings.php" class="test-link">Test Bookings (Simple Table)</a>
    <a href="admin_bookings.php" class="test-link">Admin Bookings Page</a>
    <a href="debug_bookings.php" class="test-link">Debug Page</a>
    
    <h2>What to Test:</h2>
    <ol>
        <li>Click on any "View Booking ID X" link</li>
        <li>The view_booking.php page should load and show booking details</li>
        <li>You should see: Booking ID, Customer ID, Service Type, Amount, etc.</li>
        <li>No admin login should be required</li>
    </ol>
</body>
</html> 