<?php
// Include database connection
require_once 'db_connect.php';

// Initialize variables and error messages
$errors = [];
$success = false;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (empty($firstName)) {
        $errors['firstName'] = 'First name is required';
    }
    if (empty($lastName)) {
        $errors['lastName'] = 'Last name is required';
    }
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    if (empty($message)) {
        $errors['message'] = 'Message is required';
    }

    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO contact_submissions 
                                  (first_name, last_name, email, phone, message) 
                                  VALUES (:firstName, :lastName, :email, :phone, :message)");
            
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':message', $message);
            
            $stmt->execute();
            
            $success = true;
            
            // Redirect to prevent form resubmission
            header("Location: contact us.html?success=1");
            exit();
            
        } catch(PDOException $e) {
            $errors['database'] = 'Error submitting form: ' . $e->getMessage();
        }
    }
}

// If we get here, either there were errors or we're displaying the form
// You can include your HTML form here or redirect back with error messages
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission</title>
    <style>
        .success-message {
            color: green;
            font-weight: bold;
            margin: 20px 0;
            padding: 15px;
            background-color: #e6ffe6;
            border: 1px solid #a3e8a3;
            border-radius: 5px;
        }
        .error-message {
            color: red;
            font-weight: bold;
            margin: 20px 0;
            padding: 15px;
            background-color: #ffe6e6;
            border: 1px solid #ffa3a3;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php if ($success): ?>
        <div class="success-message">
            Thank you for your message! We'll get back to you within 24 hours.
        </div>
    <?php elseif (!empty($errors)): ?>
        <div class="error-message">
            <p>There were errors with your submission:</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <p>Please go back and correct these errors.</p>
        </div>
    <?php endif; ?>
</body>
</html>