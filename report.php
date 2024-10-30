
<?php>include ("auth.php")?>
<?php include("includes/header.php") ?>
<?php include("includes/navBar.php") ?>
<?php include("includes/sideNav.php") ?>
<?php 
// Variables to store filter values (default to monthly)
$filter_type = isset($_POST['filter_type']) ? $_POST['filter_type'] : 'month';

// Prepare query based on the selected filter (per day or per month)
if ($filter_type == 'day') {
    // Grouping by day
    $query = "
        SELECT product_name, SUM(quantity) AS total_quantity, DATE(date_time) AS report_date 
        FROM transaction_history 
        WHERE action = 'Sell'
        GROUP BY product_name, DATE(date_time)
        ORDER BY DATE(date_time) DESC";
} else {
    // Grouping by month
    $query = "
        SELECT product_name, SUM(quantity) AS total_quantity, DATE_FORMAT(date_time, '%Y-%m') AS report_date 
        FROM transaction_history 
        WHERE action = 'Sell'
        GROUP BY product_name, DATE_FORMAT(date_time, '%Y-%m')
        ORDER BY DATE_FORMAT(date_time, '%Y-%m') DESC";
}

try {
    // Execute the query using PDO
    $stmt = $con->prepare($query);
    $stmt->execute();
    
    // Prepare arrays for Chart.js
    $labels = []; // Labels for days/months
    $datasets = []; // Data for the bar chart

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $row['report_date'];
        $datasets[] = [
            'label' => $row['product_name'],
            'data' => $row['total_quantity']
        ];
    }
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>

<!-- Report Section -->
<div class="container-fluid px-4">
    <h1 class="my-4">Sales Report</h1>
    
    <!-- Filter Form -->
    <form method="POST" class="mb-4">
        <label for="filter_type">Generate Report By: </label>
        <select name="filter_type" id="filter_type">
            <option value="month" <?= $filter_type == 'month' ? 'selected' : '' ?>>Per Month</option>
            <option value="day" <?= $filter_type == 'day' ? 'selected' : '' ?>>Per Day</option>
        </select>
        <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>

    <!-- Bar Chart Container -->
    <div class="container">
        <canvas id="salesReportChart"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Get the data passed from PHP
    const labels = <?= json_encode($labels) ?>;
    const datasets = <?= json_encode($datasets) ?>.map((item) => {
        return {
            label: item.label,
            data: [item.data],
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        };
    });

    const ctx = document.getElementById('salesReportChart').getContext('2d');
    const salesReportChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include("includes/footer.php"); ?>
