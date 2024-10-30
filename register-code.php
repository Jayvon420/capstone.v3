<?php
session_start();
require 'dbcon.php';  // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);

    // Check if passwords match
    if ($password === $confirm_password) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists
        $query = "SELECT * FROM users WHERE username = :username OR email = :email";
        $stmt = $con->prepare($query);
        $stmt->execute(['username' => $username, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Username or email already exists!";
            header('Location: register.php');
        } else {
            // Insert the new user into the database
            $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $con->prepare($query);

            if ($stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password])) {
                // Set success message
                $_SESSION['message'] = "Registration successful! Please login.";
                header('Location: login.php');  // Redirect to login page after successful registration
                exit();
            } else {
                $_SESSION['error'] = "Registration failed!";
                header('Location: register.php');
            }
        }
    } else {
        $_SESSION['error'] = "Passwords do not match!";
        header('Location: register.php');
    }
}
?>
