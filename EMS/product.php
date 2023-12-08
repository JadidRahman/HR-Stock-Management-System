<?php
session_start();
// Establish a connection to the database
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "EMS"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search term if it exists
$searchTerm = $_GET['search'] ?? '';

// Determine the number of entries per page
$entriesPerPage = isset($_GET['entries']) ? (int) $_GET['entries'] : 10; // Default to 10 entries per page if not set

// Determine the current page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$startFrom = ($page - 1) * $entriesPerPage;

// Secure the inputs
$searchTermLike = "%" . $conn->real_escape_string($searchTerm) . "%";

// Query to select products based on the search term and pagination
$query = "SELECT * FROM products_table WHERE CONCAT(product_type, product_name, brand_name, qty, unit, purchaserate, mrp, expire_date) LIKE ? LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $searchTermLike, $startFrom, $entriesPerPage);
$stmt->execute();
$products = $stmt->get_result();


// Query to get the total number of entries for pagination
$totalEntriesQuery = "SELECT COUNT(*) as count FROM products_table WHERE CONCAT(product_type, product_name, brand_name, qty, unit, purchaserate, mrp, expire_date) LIKE ?";
$totalStmt = $conn->prepare($totalEntriesQuery);
$totalStmt->bind_param("s", $searchTermLike);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalEntries = $totalRow['count'];

// Calculate the total number of pages
$totalPages = ceil($totalEntries / $entriesPerPage);
$prevPage = max(1, $page - 1); // Calculate previous page number
$nextPage = min($totalPages, $page + 1); // Calculate next page number
// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Custom CSS styles -->
    <style>
        .company-logo {
            display: none
        }

        .print-only-title {
            display: none;
        }


        @page {
            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
            }
        }

        body {
            counter-reset: page;
        }

        @media print {
            body {
                font-size: 12px;
            }


            .page-break {
                display: block;
                page-break-before: always;
            }

            .page-number::after {
                content: "Page " counter(page);
            }

            .container-fluid,
            .paginate,
            .action-col,
            .buttons,
            .top-bar,
            .page-item,
            .dropdown,
            h1 {
                display: none !important;
            }

            .print-only-title {
                display: block;
                text-align: center;
                font-size: 18px;
                margin-top: 20px;
                margin-bottom: 20px;
            }

            .print-page-number {
                position: fixed;
                bottom: 0;
                right: 0;
                font-size: 12px;
                text-align: left;
                width: 100%;
                padding-right: 20px;
            }

            .print-page-number::after {
                counter-increment: page;
                content: "Page " counter(page) " of " counter(pages);
            }

            .company-logo {
                display: block;
                max-width: 100px;
                /* or any other size */
                max-height: 100px;
                /* or any other size */
            }

            .company-logo img {
                width: 200%;
                margin-left: 20px;
                margin-bottom: 20px;
            }

            .printable {
                display: block;
            }

            .no-print {
                display: none;
            }


        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Sidebar (if any) -->
        <div id="sidebar">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="company-logo">
            <img src="Safwah_Main_logo-removebg-preview.png" alt="Company Logo">

        </div>
        <center><strong class="print-only-title">Product List</strong></center>

        <!-- Main content column -->
        <div class="col-md-10 offset-md-2" id="main-content">
            <!-- Product dashboard content -->
            <div class="content">
                <h1>Product Dashboard</h1>
                <nav class="breadcrumb no-print">
                    <p>Dashboard / Products</p>
                </nav>

                <!-- Top bar for search and adding new product -->
                <div class="top-bar">
                    <!-- Search form -->
                    <div class="search-bar">
                        <form action="" method="get">
                            <input type="text" name="search" placeholder="Search products..."
                                value="<?php echo htmlspecialchars($searchTerm); ?>">
                            <input type="submit" value="Search" class="btn btn-primary">
                        </form>
                        <!-- Show entries selection -->
                        <form action="" method="get">
                            <label for="show-entries">Show</label>
                            <select name="entries" id="show-entries" onchange="this.form.submit()">
                                <option value="10" <?php echo $entriesPerPage == 10 ? 'selected' : ''; ?>>10</option>
                                <option value="25" <?php echo $entriesPerPage == 25 ? 'selected' : ''; ?>>25</option>
                                <option value="50" <?php echo $entriesPerPage == 50 ? 'selected' : ''; ?>>50</option>
                                <option value="100" <?php echo $entriesPerPage == 100 ? 'selected' : ''; ?>>100
                                </option>
                            </select>
                        </form>
                    </div>

                    <!-- Add Product Button -->
                    <button id="addProductBtn" class="btn btn-success">+ Add Product</button>
                </div>

                <!-- Table of products -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>SKU</th>
                                <th>Product Type</th>
                                <th>Product Name</th>
                                <th>Brand Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Purchase Rate</th>
                                <th>MRP</th>
                                <th>Expire Date</th>
                                <th>Manufacture Date</th>
                                <th class="no-print">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($products->num_rows > 0): ?>
                                <?php while ($product = $products->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($product['serial_number']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['sku']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['product_type']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['product_name']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['brand_name']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['qty']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['unit']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['purchaserate']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['mrp']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['expire_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['manufacture_date']); ?>
                                        </td>
                                        <td>
                                            <div class="no-print dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton<?php echo $product['serial_number']; ?>"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu"
                                                    aria-labelledby="dropdownMenuButton<?php echo $product['serial_number']; ?>">
                                                    <a class="dropdown-item edit-btn" href="#"
                                                        data-product='<?php echo json_encode($product); ?>'>Edit</a>
                                                    <a class="dropdown-item"
                                                        href="delete_product.php?serial_number=<?php echo $product['serial_number']; ?>">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9">No products found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
                <div class="print-page-number"></div>


                <!-- Pagination Links -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Page Link -->
                        <li class="page-item <?php if ($page <= 1)
                            echo 'disabled'; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $prevPage; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>"
                                aria-label="Previous">
                                <span aria-hidden="true">&laquo; Previous</span>
                            </a>
                        </li>

                        <!-- Page number links -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($page == $i)
                                echo 'active'; ?>">
                                <a class="page-link"
                                    href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Page Link -->
                        <li class="page-item <?php if ($page >= $totalPages)
                            echo 'disabled'; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $nextPage; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>"
                                aria-label="Next">
                                <span aria-hidden="true">Next &raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>



            </div>
        </div>
    </div>
    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Product</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="add_product.php" method="post">
                        <div class="form-group">
                            <label for="sku">SKU:</label>
                            <input type="text" class="form-control" id="sku" name="sku" required>
                        </div>

                        <div class="form-group">
                            <label for="product_type">Product Type:</label>
                            <input type="text" class="form-control" id="product_type" name="product_type" required>
                        </div>
                        <div class="form-group">
                            <label for="product_name">Product Name:</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="form-group">
                            <label for="brand_name">Brand Name:</label>
                            <input type="text" class="form-control" id="brand_name" name="brand_name">
                        </div>
                        <div class="form-group">
                            <label for="qty">Quantity:</label>
                            <input type="number" class="form-control" id="qty" name="qty" required>
                        </div>
                        <div class="form-group">
                            <label for="unit">Unit:</label>
                            <input type="text" class="form-control" id="unit" name="unit">
                        </div>
                        <div class="form-group">
                            <label for="purchaserate">Purchase Rate:</label>
                            <input type="number" class="form-control" id="purchaserate" name="purchaserate" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="mrp">MRP:</label>
                            <input type="number" class="form-control" id="mrp" name="mrp" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="expire_date">Expire Date:</label>
                            <input type="date" class="form-control" id="expire_date" name="expire_date">
                        </div>
                        <div class="form-group">
                            <label for="manufacture_date">Manufacture Date:</label>
                            <input type="date" class="form-control" id="manufacture_date" name="manufacture_date">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Product</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" action="edit_product.php" method="post">
                        <input type="hidden" id="edit_serial_number" name="serial_number">
                        <div class="modal-body">
                            <form action="add_product.php" method="post">
                                <div class="form-group">
                                    <label for="sku">SKU:</label>
                                    <input type="text" class="form-control" id="edit_sku" name="sku" required>

                                </div>
                                <div class="form-group">
                                    <label for="edit_product_type">Product Type:</label>
                                    <input type="text" class="form-control" id="edit_product_type" name="product_type"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_product_name">Product Name:</label>
                                    <input type="text" class="form-control" id="edit_product_name" name="product_name"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_brand_name">Brand Name:</label>
                                    <input type="text" class="form-control" id="edit_brand_name" name="brand_name">
                                </div>
                                <div class="form-group">
                                    <label for="edit_qty">Quantity:</label>
                                    <input type="number" class="form-control" id="edit_qty" name="qty" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_unit">Unit:</label>
                                    <input type="text" class="form-control" id="edit_unit" name="unit">
                                </div>
                                <div class="form-group">
                                    <label for="edit_purchaserate">Purchase Rate:</label>
                                    <input type="number" class="form-control" id="edit_purchaserate" name="purchaserate"
                                        step="0.01">
                                </div>
                                <div class="form-group">
                                    <label for="edit_mrp">MRP:</label>
                                    <input type="number" class="form-control" id="edit_mrp" name="mrp" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label for="edit_expire_date">Expire Date:</label>
                                    <input type="date" class="form-control" id="edit_expire_date" name="expire_date">
                                </div>
                                <div class="form-group">
                                    <label for="edit_expire_date">Manufacture Date:</label>
                                    <input type="date" class="form-control" id="edit_manufacture_date"
                                        name="manufacture_date">
                                </div>
                                <button type="submit" class="btn btn-primary">Update Product</button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                </div>
            </div>
        </div>






        <!-- Remove any duplicate jQuery or Bootstrap script references before these lines -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.6/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>




        <script>
            $(document).ready(function () {
                // Initialize dropdowns
                $('.dropdown-toggle').dropdown();

                // Function to show the 'Add Product' modal
                $('#addProductBtn').on('click', function () {
                    $('#addProductModal').modal('show');
                });

                // Attach click event to Edit buttons
                $('.edit-btn').on('click', function () {
                    var productData = $(this).data('product');
                    editProduct(productData);
                });




                // Global function to open the edit product modal and populate it with the selected product's data
                window.editProduct = function (productData) {
                    // Populate the form fields with the product data
                    $('#edit_serial_number').val(productData.serial_number);
                    $('#edit_sku').val(productData.sku);
                    $('#edit_product_type').val(productData.product_type);
                    $('#edit_product_name').val(productData.product_name);
                    $('#edit_brand_name').val(productData.brand_name);
                    $('#edit_qty').val(productData.qty);
                    $('#edit_unit').val(productData.unit);
                    $('#edit_purchaserate').val(productData.purchaserate);
                    $('#edit_mrp').val(productData.mrp);
                    $('#edit_expire_date').val(productData.expire_date);
                    $('#edit_manufacture_date').val(productData.manufacture_date);

                    // Show the modal
                    $('#editProductModal').modal('show');
                };
            });
        </script>


</body>

</html>