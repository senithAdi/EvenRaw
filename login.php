<?php
require_once 'db_connect.php';

// Define admin credentials (alternative to database check)
$admin_email = "admin@example.com";
$admin_password = "admin123"; // Change this to a secure password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        die("Please fill all fields");
    }

    // Check for admin login first
    if ($email === $admin_email && $password === $admin_password) {
        session_start();
        $_SESSION['user_id'] = 0; // Special ID for admin
        $_SESSION['user_name'] = "Administrator";
        $_SESSION['user_email'] = $admin_email;
        $_SESSION['logged_in'] = true;
        $_SESSION['is_admin'] = true;

        header("Location: admin.html");
        exit();
    }

    try {
        // Get user by email
        $stmt = $conn->prepare("SELECT id, name, email, password, is_admin FROM user WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() === 1) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verify password
            if (password_verify($password, $userData['password'])) {
                session_start();
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_name'] = $userData['name'];
                $_SESSION['user_email'] = $userData['email'];
                $_SESSION['logged_in'] = true;
                $_SESSION['is_admin'] = $userData['is_admin'];

                // Redirect based on admin status
                if ($userData['is_admin'] == 1) {
                    header("Location: BookingsBE.php");
                } else {
                    header("Location: Home.html");
                }
                exit();
            } else {
                die("Invalid password");
            }
        } else {
            die("Email not found");
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: index.html");
    exit();
}
?>
