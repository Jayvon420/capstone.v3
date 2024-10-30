<?php if(isset($_SESSION['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>SUCCESS!</strong> <?= $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        // Automatically dismiss the alert after 5 seconds (5000 milliseconds)
        setTimeout(function() {
            var alertElement = document.querySelector('.alert-success');
            if (alertElement) {
                // Bootstrap's method to close the alert
                var alert = new bootstrap.Alert(alertElement);
                alert.close();
            }
        }, 2000);
    </script>

    <?php unset($_SESSION['message']); ?>
<?php endif; ?>
