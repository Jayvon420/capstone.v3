<?php include ("auth.php")?>
<?php include("includes/header.php") ?>
<?php include("includes/navBar.php") ?>
<?php include("includes/sideNav.php") ?>
<?php
$history_query = "SELECT * FROM transaction_history ORDER BY date_time DESC";
$history_result = mysqli_query($con, $history_query);
?>


<div class="container-fluid px-4">

<h2 class="mt-5">Not Working Right now Sorry uwu !</h2>


</div>

<?php include("includes/footer.php")?>