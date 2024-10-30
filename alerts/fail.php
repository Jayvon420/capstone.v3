<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>ERROR!</strong> <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        // Automatically dismiss the alert after 5 seconds (5000 milliseconds)
        setTimeout(function() {
            var alertElement = document.querySelector('.alert');
            if (alertElement) {
                // Bootstrap's method to close the alert
                var alert = new bootstrap.Alert(alertElement);
                alert.close();
            }
        }, 2000);
    </script>

    <?php unset($_SESSION['error']); ?>
<?php endif; ?>


