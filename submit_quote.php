<?php
// submit_quote.php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $service_type = $_POST['service_type'];
    $project_details = $_POST['project_details'];
    
    try {
        $stmt = $conn->prepare("INSERT INTO quote_requests (full_name, email, phone, service_type, project_details) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $phone, $service_type, $project_details]);
        
        // Success response
        echo "<script>
                alert('Thank you for your interest. We will contact you soon with your custom quote!');
                window.location.href = 'Get Quote.html';
              </script>";
    } catch(PDOException $e) {
        // Error response
        echo "<script>
                alert('Sorry, there was an error submitting your request. Please try again later.');
                window.location.href = 'Get Quote.html';
              </script>";
    }
} else {
    header("Location: Get Quote.html");
}
?>