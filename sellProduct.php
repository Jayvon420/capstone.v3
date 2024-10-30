<?php
// sellProduct.php

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

    // Get the product ID and quantity to sell from the form
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantityToSell = isset($_POST['sell_quantity']) ? intval($_POST['sell_quantity']) : 0;

    if ($productId > 0 && $quantityToSell > 0) {
        // Fetch current product data and check if the product belongs to the logged-in user
        $stmt = $con->prepare("SELECT name, quantity, price, retail_price FROM products WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $productId, 'user_id' => $userId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['quantity'] >= $quantityToSell) {
            $newQuantity = $product['quantity'] - $quantityToSell;

            // Update the product quantity
            $stmt = $con->prepare("UPDATE products SET quantity = :newQuantity WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['newQuantity' => $newQuantity, 'id' => $productId, 'user_id' => $userId]);

            // Log the sale in the transaction history
            $stmt = $con->prepare("INSERT INTO transaction_history (product_id, product_name, action, quantity, price, retail_price, date_time, user_id) 
                                   VALUES (:product_id, :product_name, 'sell', :quantity, :price, :retail_price, NOW(), :user_id)");
            $stmt->execute([
                'product_id' => $productId,
                'product_name' => $product['name'],
                'quantity' => $quantityToSell,
                'price' => $product['price'], // Product price
                'retail_price' => $product['retail_price'], // Retail price
                'user_id' => $userId
            ]);

            // Set a success message
            $_SESSION['message'] = 'Product sold successfully!';

            // Redirect to addProduct.php
            header("Location: addProduct.php");
            exit();
        } else {
            // Product not found, doesn't belong to the user, or insufficient quantity
            $_SESSION['error'] = 'Product not found, does not belong to you, or insufficient quantity.';
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
