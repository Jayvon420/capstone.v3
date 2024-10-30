<?php include("auth.php")?>
<?php include("includes/header.php") ?>
<?php include("includes/navBar.php") ?>
<?php include("includes/sideNav.php") ?>

<?php
// Get the user ID from the session
$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session upon login

// Using PDO to fetch transaction history for the logged-in user
try {
    $history_query = "SELECT * FROM transaction_history WHERE user_id = :user_id ORDER BY date_time DESC";
    $stmt = $con->prepare($history_query);
    $stmt->execute(['user_id' => $user_id]); // Bind the user ID to the query
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>

<div class="container-fluid px-4">
    <!-- Transaction History Section -->
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="my-5">Transaction History</h1>
        <a href="javascript:void(0);" class="my-5 button" onclick="window.history.back();">
            <i class="h1 text-dark fa-solid fa-arrow-left"></i>
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="myTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Retail Price</th>
                    <th>Action</th>
                    <th>Quantity</th>
                    <th>Date and Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($stmt->rowCount() > 0) {
                    while ($history = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($history['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($history['product_name']) . "</td>";
                        echo "<td>&#8369;" . number_format($history['price'], 2) . "</td>";
                        echo "<td>&#8369;" . number_format($history['retail_price'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($history['action']) . "</td>";
                        echo "<td>" . htmlspecialchars($history['quantity']) . "</td>";
                        echo "<td>" . htmlspecialchars($history['date_time']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    // echo "<tr><td colspan='7'>No transaction history found.</td></tr>"; // Message for no transactions
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php") ?>
