<?php
include("auth.php");
include("includes/header.php");
include("includes/navBar.php");
include("includes/sideNav.php");

$userId = $_SESSION['user_id']; // Assuming the user ID is stored in the session upon login

// Handle adding a category
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    
    try {
        $query = "INSERT INTO categories (cat_name, user_id) VALUES (:category_name, :user_id)";
        $stmt = $con->prepare($query);
        $stmt->execute([
            ':category_name' => $category_name,
            ':user_id' => $userId
        ]);
        $_SESSION['message'] = "Category '$category_name' added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding category: " . $e->getMessage();
    }
}

// Handle deleting a category
if (isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'];
    
    try {
        $query = "DELETE FROM categories WHERE id = :category_id AND user_id = :user_id";
        $stmt = $con->prepare($query);
        $stmt->execute([
            ':category_id' => $category_id,
            ':user_id' => $userId
        ]);
        $_SESSION['message'] = "Category deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting category: " . $e->getMessage();
    }
}

// Handle updating a category
if (isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    
    try {
        $query = "UPDATE categories SET cat_name = :category_name WHERE id = :category_id AND user_id = :user_id";
        $stmt = $con->prepare($query);
        $stmt->execute([
            ':category_name' => $category_name,
            ':category_id' => $category_id,
            ':user_id' => $userId
        ]);
        $_SESSION['message'] = "Category updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating category: " . $e->getMessage();
    }
}

// Fetch all categories for the logged-in user
$categories = $con->prepare("SELECT * FROM categories WHERE user_id = :user_id");
$categories->execute([':user_id' => $userId]);
$categories = $categories->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="my-5">Manage Categories</h1>
        <a href="javascript:void(0);" class="my-5 button" onclick="window.history.back();">
            <i class="h1 text-dark fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>ERROR!</strong> <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <script>
            setTimeout(function() {
                var alertElement = document.querySelector('.alert-warning');
                if (alertElement) {
                    var alert = new bootstrap.Alert(alertElement);
                    alert.close();
                }
            }, 2000);
        </script>

        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>SUCCESS!</strong> <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <script>
            setTimeout(function() {
                var alertElement = document.querySelector('.alert-success');
                if (alertElement) {
                    var alert = new bootstrap.Alert(alertElement);
                    alert.close();
                }
            }, 2000);
        </script>

        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Add Category Form -->
    <form action="manageCategories.php" method="POST">
        <div class="col-md-4">
            <div class="form-group">
                <label for="category_name"><strong>Add New Category</strong></label>
                <input type="text" class="form-control" id="category_name" name="category_name" required>
            </div>
            <button type="submit" name="add_category" class="btn btn-success my-3">Add Category</button>
        </div>
    </form>

    <!-- Display Categories -->
    <h2>Category List</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="myTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['cat_name']) ?></td>
                        <td class="text-center">
                            <form action="manageCategories.php" method="POST" class="d-inline deleteForm">
                                <input type="hidden" name="category_id" value="<?= htmlspecialchars($row['id']); ?>">
                                <button type="button" class="btn btn-danger btn-sm delete-category" data-id="<?= htmlspecialchars($row['id']); ?>">Delete</button>
                            </form>
                            <button type="button" class="btn btn-primary btn-sm update-category" data-id="<?= htmlspecialchars($row['id']); ?>" data-name="<?= htmlspecialchars($row['cat_name']); ?>" data-bs-toggle="modal" data-bs-target="#updateModal">Update</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateForm" action="manageCategories.php" method="POST">
                    <input type="hidden" name="category_id" id="modalCategoryId">
                    <div class="mb-3">
                        <label for="modalCategoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="modalCategoryName" name="category_name" required>
                    </div>
                    <button type="submit" name="update_category" class="btn btn-success">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>

<!-- SweetAlert Script for deleting Categories-->
<script>
    document.querySelectorAll('.delete-category').forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'manageCategories.php';

                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'category_id';
                    hiddenField.value = categoryId;

                    const deleteField = document.createElement('input');
                    deleteField.type = 'hidden';
                    deleteField.name = 'delete_category';
                    deleteField.value = true;

                    form.appendChild(hiddenField);
                    form.appendChild(deleteField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll('.update-category').forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-id');
            const categoryName = this.getAttribute('data-name');

            // Set the values in the modal
            document.getElementById('modalCategoryId').value = categoryId;
            document.getElementById('modalCategoryName').value = categoryName;
        });
    });
</script>
