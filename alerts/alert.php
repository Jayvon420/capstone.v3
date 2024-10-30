<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>ERROR!</strong> <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        // Automatically dismiss the error alert after 2 seconds
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

<?php if(isset($_SESSION['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>SUCCESS!</strong> <?= $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        // Automatically dismiss the success alert after 2 seconds
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
