<?php
session_start();
require 'dbcon.php';  // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Use prepared statements to prevent SQL injection
        $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $con->prepare($query);
        $stmt->execute(['username' => $username]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

            // Debugging: Check the fetched user details
            // Uncomment the next line to see what you get
            // print_r($user);

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['authenticated'] = true;  
                $_SESSION['user_id'] = $user['id'];  
                $_SESSION['username'] = $user['username'];  
                header('Location: index.php');  
                exit();
            } else {
                $_SESSION['error'] = "Invalid password!";
                header('Location: login.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Username not found!";
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Please fill in all fields.";
        header('Location: login.php');
        exit();
    }
}

?>
