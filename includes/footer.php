        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; All rights reserve Dela Rosa & Orito 2024</div>
                    <div>
                        <a href="##">Privacy Policy</a>
                        &middot;
                        <a href="##">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>

            <!-- SIDE NAV content END -->
        </div> 
        <!-- SIDE NAV END -->
    </div>



    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>



    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Fetch product data
            fetch('getProductData.php')
                .then(response => response.json())
                .then(data => {
                    // Prepare product names and quantities for the chart
                    const productNames = data.map(item => item.name);
                    const productQuantities = data.map(item => item.quantity);

                    // Get the context of the canvas where the chart will be rendered
                    const ctx = document.getElementById('myBarChart').getContext('2d');

                    // Create the chart
                    const myBarChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: productNames,
                            datasets: [{
                                label: 'Product Quantity',
                                data: productQuantities,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    </script>



    <!-- Clear the selected category when typing in the "Or Add New Category" field -->
    <script>
        document.getElementById('newCategory').addEventListener('input', function() {
            document.getElementById('productCategory').selectedIndex = 0;
        });

        // Clear the "Or Add New Category" field when a category is selected
        document.getElementById('productCategory').addEventListener('change', function() {
            if (this.selectedIndex > 0) { // Check if an existing category is selected
                document.getElementById('newCategory').value = ''; // Clear the input field
            }
        });

        // Validate form to ensure a category is selected or a new one is entered
        function validateForm() {
            var categoryDropdown = document.getElementById('productCategory');
            var newCategoryInput = document.getElementById('newCategory').value.trim();

            // Ensure either a new category is entered or an existing one is selected
            if (newCategoryInput === "" && categoryDropdown.selectedIndex === 0) {
                alert("Please select an existing category or enter a new one.");
                return false; // Prevent form submission
            }
            return true;
        }
    </script>


    <!-- validition of category kung add new categoy ignore select category if Input sa add new category bypass the select category -->
    <script>
    function validateForm() {
        const productCategory = document.getElementById('productCategory');
        const newCategory = document.getElementById('newCategory');

        // If new category is provided, clear any validation error for the product category
        if (newCategory.value.trim() !== "") {
            productCategory.setCustomValidity('');
        }

        // If both newCategory and productCategory are empty, prevent form submission
        if (newCategory.value.trim() === "" && productCategory.value === "") {
            productCategory.setCustomValidity('Please select an existing category or enter a new one.');
            productCategory.reportValidity();
            return false;
        }

        return true; // Allow form submission if validation passes
    }
    </script>
    <!-- validittion of categoy end -->

    <!-- script for zero value not accepted -->
    <script>
    function validateForm() {
        const quantityInput = document.getElementById('productQty');
        const quantity = quantityInput.value;

        // Check if the quantity starts with a zero or is less than or equal to zero
        if (quantity.startsWith('0') || quantity <= 0) {
            // Set a custom validation message
            quantityInput.setCustomValidity('Quantity must be a positive number and cannot start with zero.');
            
            // Trigger the invalid event to show the message
            quantityInput.reportValidity();
            
            return false; // Prevent form submission
        } else {
            // Clear the custom validation message if the input is valid
            quantityInput.setCustomValidity('');
        }

        return true; // Allow form submission if validation passes
    }

    // Add an event listener to clear the validation message when the input changes
    document.getElementById('productQty').addEventListener('input', function() {
        this.setCustomValidity('');
    });
    </script>
    <!-- script for zero value not accepted end -->


    <!-- table script. -->
    <script>
        $(document).ready( function () {
                $('#myTable').DataTable();
        });
    </script>

    
    <!-- Include SweetAlert for deleting data pop-up-->
    <script src = "js/sweet-alert.js"></script>
    <script>
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default button action

                const form = this.closest('.deleteForm'); // Get the closest form element

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to delete this data?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const input = document.createElement('input'); // Create a new input element
                        input.type = 'hidden';
                        input.name = 'deleteProduct'; // Set the name attribute
                        form.appendChild(input); // Append the input to the form

                        form.submit(); // Submit the form if confirmed
                    }
                });
            });
        });
    </script>
    <!-- delete sweet alert -->



    <script src="js/scripts.js"></script>
    <script src = "js/bootstrap.bundle.min.js"></script>   


    </body>
</html>