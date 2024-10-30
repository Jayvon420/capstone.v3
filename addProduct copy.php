<?php include ("auth.php")?>
<?php include("includes/header.php") ?>
<?php include("includes/navBar.php") ?>
<?php include("includes/sideNav.php") ?>


<div class="px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="my-5">Inventory System</h1>
        <a href="javascript:void(0);" class="my-5 button" onclick="window.history.back();">
            <i class="h1 text-dark fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <!-- Add Product Form -->
    <form action="code.php" method="POST" onsubmit="return validateForm()">
        <?php include('alerts/alert.php'); ?>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="productName"><strong>Product Name</strong></label>
                    <input type="text" class="form-control" id="productName" name="name" required>
                </div>
            </div>
            <div class="col-md-2"> 
                <div class="form-group">  
                    <label for="productPrice"><strong>Price</strong></label>
                    <input type="number" class="form-control" id="productPrice" name="price" required step="0.01">
                </div>
            </div>

            <div class="col-md-2"> 
                <div class="form-group">  
                    <label for="retailPrice"><strong>Retail Price</strong></label>
                    <input type="number" class="form-control" id="retailPrice" name="retail_price" required step="0.01">
                </div>
            </div>

            <div class="col-md-1"> 
                <div class="form-group">  
                    <label for="productQty"><strong>Quantity</strong></label>
                    <input type="number" class="form-control" id="productQty" name="quantity" required>
                </div>
            </div>
        </div>
        
      <!-- Category Selection -->
      <div class="row">
          <div class="col-md-4">
              <div class="form-group">
                  <label for="productCategory"><strong>Category</strong></label>
                  <select class="form-control" id="productCategory" name="category_id">
                      <option value="" disabled selected>Select a Category</option>
                      <?php
                      // Check if user is logged in
                      if (isset($_SESSION['user_id'])) {
                          // Fetch categories from the database using PDO
                          try {
                              // Fetch categories added by the logged-in user
                              $stmt = $con->prepare("SELECT * FROM categories WHERE user_id = :user_id"); // Ensure column matches your schema
                              $stmt->execute(['user_id' => $_SESSION['user_id']]);
                              
                              // Check if any categories were fetched
                              if ($stmt->rowCount() > 0) {
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                      echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['cat_name']) . "</option>";
                                  }
                              } else {
                                  echo "<option value='' disabled>No categories available for this user.</option>";
                              }
                          } catch (PDOException $e) {
                              echo "<option value='' disabled>Error fetching categories: " . htmlspecialchars($e->getMessage()) . "</option>";
                          }
                      } else {
                          echo "<option value='' disabled>User must be logged in to see categories.</option>";
                      }
                      ?>
                  </select>
              </div>
          </div>
          <div class="col-md-4">
              <div class="form-group">
                  <label for="newCategory"><strong>Or Add New Category</strong></label>
                  <input type="text" class="form-control" id="newCategory" name="new_category" placeholder="Enter New Category">
              </div>
          </div>
      </div>

        <!-- Submit Button -->
        <div class="my-4">
            <button type="submit" name="add_product" class="btn btn-success">Add Item</button>
        </div>
    </form>

    <!-- end of product adding -->

    <h2>Product List</h2>
      <div class="table-responsive">
          <table class="table table-striped table-bordered" id="myTable">
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Product Name</th>
                      <th>Price</th>
                      <th>Retail Price</th>
                      <th>Quantity</th>
                      <th>Category</th>
                      <th class="text-center">Action</th>
                  </tr>
              </thead>
              <tbody>

              
                  <?php
                  // Ensure the user ID is available from the session
                  if (isset($_SESSION['user_id'])) {
                      $user_id = $_SESSION['user_id'];

                      // Query to include category information and filter by user_id
                      try {
                          $query = "SELECT products.id, products.name, products.quantity, products.price, products.retail_price, categories.cat_name
                                    FROM products 
                                    INNER JOIN categories ON products.category_id = categories.id
                                    WHERE products.user_id = :user_id"; // Filter by user_id
                          $stmt = $con->prepare($query);
                          $stmt->execute(['user_id' => $user_id]); // Bind the user ID
                          
                          while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                              ?>
                              <tr>
                                  <td><?= htmlspecialchars($product['id']) ?></td>
                                  <td><strong><?= htmlspecialchars($product['name']) ?></strong></td>
                                  <td>&#8369;<?= htmlspecialchars($product['price']) ?></td>
                                  <td>&#8369;<?= htmlspecialchars($product['retail_price']) ?></td>
                                  <td><?= htmlspecialchars($product['quantity']) ?></td>
                                  <td><?= htmlspecialchars($product['cat_name']) ?></td>
                                  <td class="text-center">
                                      <!-- Example Button to Open Update Product Modal -->
                                        <!-- Example button for opening the modal -->
<button type="button" class="btn btn-primary" 
        data-bs-toggle="modal" 
        data-bs-target="#updateProductModal"
        data-id="<?= htmlspecialchars($product['id']) ?>"
        data-name="<?= htmlspecialchars($product['name']) ?>"
        data-price="<?= htmlspecialchars($product['price']) ?>"
        data-retail_price="<?= htmlspecialchars($product['retail_price']) ?>"
        data-quantity="<?= htmlspecialchars($product['quantity']) ?>">
    Update
</button>


                                      <form action="code.php" method="POST" class="d-inline deleteForm">
                                          <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']); ?>">
                                          <button type="button" class="btn btn-danger btn-sm deleteBtn">Delete</button>
                                      </form>
                                      <!-- Add Button -->
                                      <button type="button" class="btn btn-info btn-sm addItemBtn" 
                                              data-id="<?= $product['id'] ?>" 
                                              data-name="<?= htmlspecialchars($product['name']) ?>" 
                                              data-quantity="<?= htmlspecialchars($product['quantity']) ?>" 
                                              data-bs-toggle="modal" 
                                              data-bs-target="#addItemModal">Add
                                      </button>
                                      <!-- Sell Button -->
                                      <button type="button" class="btn btn-warning btn-sm sellBtn" 
                                              data-id="<?= $product['id'] ?>" 
                                              data-name="<?= htmlspecialchars($product['name']) ?>" 
                                              data-quantity="<?= htmlspecialchars($product['quantity']) ?>" 
                                              data-bs-toggle="modal" 
                                              data-bs-target="#sellModal">Sell
                                      </button>
                                  </td>
                              </tr>
                              <?php
                          }
                      } catch (PDOException $e) {
                          echo "Error fetching products: " . htmlspecialchars($e->getMessage());
                      }
                  } else {
                      echo "<tr><td colspan='7'>No products found. Please log in.</td></tr>";
                  }
                ?>
              </tbody>

          </table>
      </div>


     <!-- Add Item Modal -->
      <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">

            <div class="modal-header">
              <h5 class="modal-title" id="addItemModalLabel">Product name: <span id="addProductName"></span></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="addQuantity.php" method="POST">
              <div class="modal-body">
                <input type="hidden" id="addProductId" name="product_id">
                <div class="form-group">
                  <label for="addProductQuantity">Current Quantity:</label>
                  <input type="text" id="addProductQuantity" class="form-control" readonly>
                </div>
                <div class="form-group">
                  <label for="newQuantity">New Quantity:</label>
                  <input type="number" id="newQuantity" name="new_quantity" class="form-control" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Quantity</button>
              </div>
            </form>

          </div>
        </div>
      </div>
  

      <!-- Sell Item Modal -->
      <div class="modal fade" id="sellModal" tabindex="-1" aria-labelledby="sellModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="sellModalLabel">Sell Product <span id="sellProductName"></span></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="sellProduct.php" method="POST">
              <div class="modal-body">
                <input type="hidden" id="sellProductId" name="product_id">
                <div class="form-group">
                  <label for="sellProductQuantity">Current Quantity:</label>
                  <input type="text" id="sellProductQuantity" class="form-control" readonly>
                </div>
                <div class="form-group">
                  <label for="sellQuantity">Quantity to Sell:</label>
                  <input type="number" id="sellQuantity" name="sell_quantity" class="form-control" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Sell</button>
              </div>
            </form>
          </div>
        </div>
      </div>


    <!-- Update Product Modal -->
    <div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProductModalLabel">Update Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateProductForm" action="code.php" method="POST">
                        <div id="modalAlerts">
                            <?php include('alerts/fail.php'); ?>
                            <?php include('alerts/message.php'); ?>
                        </div>
                        <input type="hidden" name="product_id" id="modalProductId">

                        <div class="mb-3">
                            <label for="modalProductName" class="form-label">Product Name: <span id="currentProductName"></span></label>
                            <input type="text" class="form-control" id="modalProductName" name="name" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalProductPrice" class="form-label">Price: <span id="currentProductPrice"></span></label>
                            <input type="number" step="0.01" class="form-control" id="modalProductPrice" name="price" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalRetailPrice" class="form-label">Retail Price: <span id="currentRetailPrice"></span></label>
                            <input type="number" step="0.01" class="form-control" id="modalRetailPrice" name="retail_price" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalProductQty" class="form-label">Quantity: <span id="currentProductQty"></span></label>
                            <input type="number" class="form-control" id="modalProductQty" name="quantity" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalProductCategory" class="form-label">Category: <span id="currentProductCategory"></span></label>
                            <select class="form-select" id="modalProductCategory" name="category_id" required>
                                <option value="" disabled>Select a Category</option>
                                <?php
                                // Fetch categories again to populate the modal dropdown
                                $categories_query = "SELECT * FROM categories WHERE user_id = :user_id"; // Filter by user_id
                                $categories_stmt = $con->prepare($categories_query);
                                $categories_stmt->execute([':user_id' => $_SESSION['user_id']]);
                                $categories_result = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($categories_result as $row) {
                                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['cat_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="updateProduct" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle the Add Item Modal
            const addItemModal = document.getElementById('addItemModal');
            document.querySelectorAll('.addItemBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-id');
                    const productName = this.getAttribute('data-name');
                    const productQuantity = this.getAttribute('data-quantity');

                    // Set the product name and other data in the modal
                    addItemModal.querySelector('#addProductName').textContent = productName;
                    addItemModal.querySelector('#addProductQuantity').value = productQuantity;
                    addItemModal.querySelector('#addProductId').value = productId;
                });
            });

            // Handle the Sell Item Modal
            const sellItemModal = document.getElementById('sellModal');
            document.querySelectorAll('.sellBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-id');
                    const productName = this.getAttribute('data-name');
                    const productQuantity = this.getAttribute('data-quantity');

                    // Set the product name and other data in the modal
                    sellItemModal.querySelector('#sellProductName').textContent = productName;
                    sellItemModal.querySelector('#sellProductQuantity').value = productQuantity;
                    sellItemModal.querySelector('#sellProductId').value = productId;
                });
            });
        });
    </script>

    <script>
    // Event listener for the modal show event
    var updateProductModal = document.getElementById('updateProductModal');
    updateProductModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal

        // Extract the data attributes from the button
        var productId = button.getAttribute('data-id');
        var productName = button.getAttribute('data-name');
        var productPrice = button.getAttribute('data-price');
        var productRetailPrice = button.getAttribute('data-retail_price');
        var productQty = button.getAttribute('data-quantity');
        var productCategoryId = button.getAttribute('data-category_id');

        // Update the modal's content
        var modalProductId = updateProductModal.querySelector('#modalProductId');
        var modalProductName = updateProductModal.querySelector('#modalProductName');
        var modalProductPrice = updateProductModal.querySelector('#modalProductPrice');
        var modalRetailPrice = updateProductModal.querySelector('#modalRetailPrice');
        var modalProductQty = updateProductModal.querySelector('#modalProductQty');
        var modalProductCategory = updateProductModal.querySelector('#modalProductCategory');

        modalProductId.value = productId;
        modalProductName.value = productName;
        modalProductPrice.value = productPrice;
        modalRetailPrice.value = productRetailPrice;
        modalProductQty.value = productQty;

        // Set the selected category
        modalProductCategory.value = productCategoryId;
    });
</script>




<?php include("includes/footer.php") ?>
