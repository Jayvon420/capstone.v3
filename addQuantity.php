<?php
// Database connection (replace with your own connection code)
require 'dbcon.php';
session_start(); // Start session to access user_id

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the logged-in user's ID
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } else {
        header("Location: login.php"); // Redirect to login if not logged in
        exit();
    }

    // Get the product ID and quantity to add from the form
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantityToAdd = isset($_POST['new_quantity']) ? intval($_POST['new_quantity']) : 0; // Updated variable name

    if ($productId > 0 && $quantityToAdd > 0) {
        // Fetch current product data and check if the product belongs to the logged-in user
        $stmt = $con->prepare("SELECT name, quantity, price, retail_price FROM products WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $productId, 'user_id' => $userId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $newQuantity = $product['quantity'] + $quantityToAdd;

            // Update the product quantity
            $stmt = $con->prepare("UPDATE products SET quantity = :newQuantity WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['newQuantity' => $newQuantity, 'id' => $productId, 'user_id' => $userId]);

            // Log the addition in the transaction history
            $stmt = $con->prepare("INSERT INTO transaction_history (product_id, product_name, action, quantity, price, retail_price, date_time, user_id) 
                                   VALUES (:product_id, :product_name, 'add', :quantity, :price, :retail_price, NOW(), :user_id)");
            $stmt->execute([
                'product_id' => $productId,
                'product_name' => $product['name'],
                'quantity' => $quantityToAdd,
                'price' => $product['price'], // Product price
                'retail_price' => $product['retail_price'], // Retail price
                'user_id' => $userId
            ]);

            // Set a success message
            $_SESSION['message'] = 'Product quantity added successfully!';

            // Redirect to addProduct.php
            header("Location: addProduct.php");
            exit();
        } else {
            // Product not found or doesn't belong to the user
            $_SESSION['error'] = 'Product not found or does not belong to you.';
            header("Location: addProduct.php");
            exit();
        }
    } else {
        // Invalid product ID or quantity
        $_SESSION['error'] = 'Invalid product ID or quantity.';
        header("Location: addProduct.php");
        exit();
    }
} else {
    // Invalid request method
    $_SESSION['error'] = 'Invalid request method.';
    header("Location: addProduct.php");
    exit();
}
?>
