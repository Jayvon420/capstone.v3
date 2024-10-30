<?php include ("auth.php") ?>
<?php include("includes/header.php") ?>
<?php include("includes/navBar.php") ?>
<?php include("includes/sideNav.php") ?>

        
<!-- main content -->
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>

        <div class="row justify-content-between">
            <div class="col-xl-6 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">Add Product</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="addProduct.php">See more..</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">Reports</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="report.php">See more..</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar graph -->
            <div class="container mt-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Product Quantities</h5>
                        <canvas id="myBarChart" width="1000" height="400"></canvas>
                    </div>
                </div>
            </div>


        <!-- Add content here or table bar graphs etc... -->

    </div>    
</main>
<!-- main content end -->

<?php include("includes/footer.php") ?>


