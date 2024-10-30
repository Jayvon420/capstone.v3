<?php
session_start();
require 'dbcon.php'; // Ensure this uses PDO

$product_id = $_POST['product_id'];
function logTransaction($con, $productId, $productName, $action, $quantity, $price, $retailPrice, $userId) {
    $query = "INSERT INTO transaction_history (product_id, product_name, action, quantity, price, retail_price, user_id, date_time) 
              VALUES (:product_id, :product_name, :action, :quantity, :price, :retail_price, :user_id, NOW())";
    $stmt = $con->prepare($query);
    $stmt->execute([
        'product_id' => $productId,
        'product_name' => $productName,
        'action' => $action,
        'quantity' => $quantity,
        'price' => $price,
        'retail_price' => $retailPrice,
        'user_id' => $userId
    ]);
}

// Handle form submission for adding a product
if (isset($_POST['add_product'])) {
    // Gather input data and sanitize
    $name = trim($_POST['name']);
    $quantity = trim($_POST['quantity']);
    $price = trim($_POST['price']);
    $retailPrice = trim($_POST['retail_price']);
    $categoryId = isset($_POST['category_id']) ? trim($_POST['category_id']) : null;
    $newCategory = trim($_POST['new_category']);
    $userId = $_SESSION['user_id']; // Assuming user ID is stored in session upon login

    // Check if a new category is provided
    if (!empty($newCategory)) {
        // Check if the category already exists for the logged-in user
        $check_query = "SELECT * FROM categories WHERE cat_name = :cat_name AND user_id = :user_id";
        $stmt = $con->prepare($check_query);
        $stmt->execute(['cat_name' => $newCategory, 'user_id' => $userId]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Category already exists for this user!";
            header("Location: addProduct.php");
            exit(0);
        } else {
            // Insert new category with user association
            $query = "INSERT INTO categories (cat_name, user_id) VALUES (:cat_name, :user_id)";
            $stmt = $con->prepare($query);
            if ($stmt->execute(['cat_name' => $newCategory, 'user_id' => $userId])) {
                $_SESSION['message'] = "Category added successfully!";
                $categoryId = $con->lastInsertId(); // Get the ID of the newly added category
            } else {
                $_SESSION['error'] = "Failed to add category!";
                header("Location: addProduct.php");
                exit(0);
            }
        }
    }

    // Check if no category is selected or added
    if (empty($newCategory) && empty($categoryId)) {
        $_SESSION['error'] = "Please select an existing category or add a new one.";
        header("Location: addProduct.php");
        exit(0);
    }

    // Check if the product already exists within the same category
    $checkQuery = "SELECT * FROM products WHERE name = :name AND category_id = :category_id AND user_id = :user_id";
    $stmt = $con->prepare($checkQuery);
    $stmt->execute(['name' => $name, 'category_id' => $categoryId, 'user_id' => $userId]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Product already exists in the selected category.";
        header("Location: addProduct.php");
        exit(0);
    }

    // Insert new product with user association
    $query = "INSERT INTO products (name, price, retail_price, quantity, category_id, user_id) VALUES (:name, :price, :retail_price, :quantity, :category_id, :user_id)";
    $stmt = $con->prepare($query);
    if ($stmt->execute(['name' => $name, 'price' => $price, 'retail_price' => $retailPrice, 'quantity' => $quantity, 'category_id' => $categoryId, 'user_id' => $userId])) {
        $productId = $con->lastInsertId(); // Get the product ID of the newly added product

        // Log the product addition in transaction history
        logTransaction($con, $productId, $name, 'Product Added', $quantity, $price, $retailPrice, $userId);

        $_SESSION['message'] = "Product added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add product!";
    }

    // Redirect to the add product page
    header("Location: addProduct.php");
    exit(0);
}

// Handle form submission for updating a product
if (isset($_POST['updateProduct'])) {
    $product_id = trim($_POST['product_id']);
    $product_name = trim($_POST['name']);
    $product_quantity = trim($_POST['quantity']);
    $product_price = trim($_POST['price']);
    $retail_price = trim($_POST['retail_price']);
    $category_id = trim($_POST['category_id']);
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session upon login

    // Fetch current category of the product and check user association
    $query = "SELECT * FROM products WHERE id = :product_id AND user_id = :user_id";
    $stmt = $con->prepare($query);
    $stmt->execute(['product_id' => $product_id, 'user_id' => $user_id]);

    if ($stmt->rowCount() == 0) {
        $_SESSION['error'] = "You do not have permission to update this product.";
        header("Location: updateProduct.php?id=$product_id");
        exit(0);
    }

    // Check if the product name already exists in the same category for the user
    $duplicate_query = "SELECT * FROM products WHERE name = :name AND category_id = :category_id AND id != :product_id AND user_id = :user_id";
    $stmt = $con->prepare($duplicate_query);
    $stmt->execute(['name' => $product_name, 'category_id' => $category_id, 'product_id' => $product_id, 'user_id' => $user_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Product name already exists in this category!";
        header("Location: updateProduct.php?id=$product_id");
        exit(0);
    }

    // Update the product
    $update_query = "UPDATE products SET name = :name, price = :price, retail_price = :retail_price, quantity = :quantity, category_id = :category_id WHERE id = :product_id AND user_id = :user_id";
    $stmt = $con->prepare($update_query);
    if ($stmt->execute(['name' => $product_name, 'price' => $product_price, 'retail_price' => $retail_price, 'quantity' => $product_quantity, 'category_id' => $category_id, 'product_id' => $product_id, 'user_id' => $user_id])) {

        // Log the product update in transaction history
        logTransaction($con, $product_id, $product_name, 'Product Updated', $product_quantity, $product_price, $retail_price, $user_id);

        $_SESSION['message'] = "Product updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update product!";
    }

    header("Location: addProduct.php");
    exit(0);
}



// Handles deleting product.
if (isset($_POST['deleteProduct'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id']; // Retrieve logged-in user ID from session

    try {
        $con->beginTransaction();

        // Fetch product details
        $get_product_query = "SELECT * FROM products WHERE id = :product_id AND user_id = :user_id";
        $stmt = $con->prepare($get_product_query);
        $stmt->execute(['product_id' => $product_id, 'user_id' => $user_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Log the deletion in transaction history
            logTransaction($con, $product['id'], $product['name'], 'Product Deleted', $product['quantity'], $product['price'], $product['retail_price'], $user_id);
            
            // Insert product details into the archived_products table
            $archive_query = "INSERT INTO archived_products (product_id, name, price, retail_price, quantity, category_id, user_id) 
                              VALUES (:product_id, :name, :price, :retail_price, :quantity, :category_id, :user_id)";
            $stmt = $con->prepare($archive_query);
            $stmt->execute([
                'product_id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'retail_price' => $product['retail_price'],
                'quantity' => $product['quantity'],
                'category_id' => $product['category_id'],
                'user_id' => $user_id
            ]);

            // Proceed with deleting the product
            $delete_query = "DELETE FROM products WHERE id = :product_id";
            $stmt = $con->prepare($delete_query);
            $stmt->execute(['product_id' => $product_id]);

            $con->commit();

            $_SESSION['message'] = "Product deleted successfully!";
        } else {
            throw new Exception("You do not have permission to delete this product or product not found.");
        }
    } catch (Exception $e) {
        $con->rollBack();
        $_SESSION['error'] = "Error deleting product: " . $e->getMessage();
    }

    header("Location: addProduct.php");
    exit(0);
}

// End of product deletion =======================================================>

?>