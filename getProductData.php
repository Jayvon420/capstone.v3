<?php
// Include authentication and database connection
include("auth.php");
include("dbcon.php"); // Adjust to your actual DB connection file

$user_id = $_SESSION['user_id'];

// this code use for fetch product in bargraph

try {
    // Fetch product name and quantity for the logged-in user
    $query = "SELECT name, quantity FROM products WHERE user_id = :user_id";
    $stmt = $con->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return data as JSON
    echo json_encode($data);

} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
