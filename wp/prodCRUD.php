<?php
session_start();
if (!isset($_SESSION['stId']) && !isset($_SESSION['stPass'])) {
    header('Location: Admin/Admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--header footer format-->
        <link href="resource/css/default.css" rel="stylesheet">
        <link href="resource/css/style.css" rel="stylesheet">
        <!--Table format-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity= "sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!--fa fa icon-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <title>Admin | Product</title>
        <style>
            a button {
                text-decoration: none;
                color: white;
                background-color:red;
                padding: 10px 20px 10px 20px;
                float: right;
                margin-right: 20px;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>          
        <header>

        </header>
        <a href="Admin/Admin_dashboard.php"><button >Back </button></a>

        <div class="container">

            <h1>Product</h1>
            <!-- The Modal -->
            <div id="modalDialog" class="modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content animate-top">
                        <div class="modal-header">
                            <h5 class="modal-title ">Product Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <form method="POST" action="products.php" enctype="multipart/form-data">
                            <div class="modal-body">
                                <!-- Form submission status -->
                                <div class="response"></div>
                                <div class="form-group row">
                                    <div class="col-md-6"> <!-- "Product Category" takes the other half -->
                                        <label for="category">Product Category:</label>
                                        <select name="category" id="category">
                                            <?php
                                            include_once('conn.php');
                                            $stmt = $conn->prepare("SELECT * FROM category");
                                            $stmt->execute();
                                            while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . htmlspecialchars($cat['name']) . '">' . htmlspecialchars($cat['name']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="fgroup">
                                    <label>Product Name:</label>
                                    <input type="text" name="pname" id="pname"  placeholder="Enter Product Name" required="">
                                </div>                                
                                <div class="fgroup">
                                    <label>Product Image:</label>
                                    <input type="file" name="img" id="img" placeholder="Insert Product Image" required="" accept="image/png, image/jpeg, image/gif">                                </div>

                                <div class="fgroup">
                                    <label>Product Price:</label>
                                    <input type="number" name="price" id="price"  placeholder="Enter Product Price" required="" min="0" step="any">
                                </div>
                                <div class="fgroup">
                                    <label>Stock Available:</label>
                                    <input type="number" name="stock" id="stock"  placeholder="Available Stock" required="" min="0" step="1" max="9999">
                                </div>
                                <div class="fgroup">
                                    <label>Product Details:</label>
                                    <textarea name="detail" id="detail" class="form-control" placeholder="Enter Product Details" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!-- Submit button -->
                                <button type="submit" name="addpro" id="addpro" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row-lg-8">
                <div class="col">
                    <div class="card-mt-8">
                        <?php if (isset($_SESSION['message'])) : ?>
                            <h5 style=text-align: center; class="alert alert-success"><?= $_SESSION['message']; ?></h5>
                            <?php
                            unset($_SESSION['message']);
                        endif;
                        ?>
                        <?php if (isset($_SESSION['idf'])) : ?>
                            <h5 style=text-align: center; class="alert alert-warning"><?= $_SESSION['idf']; ?></h5>
                            <?php
                            unset($_SESSION['idf']);
                        endif;
                        ?>
                        <!-- Trigger/Open The Modal -->

                        <div class="card-header">
                            <h2 class="display-6 ">
                                Product Details<button id="mbtn" class="btn btn-primary turned-button">Add Product ++</button>                             
                                <div class="search-box" style="float:right;">
                                    <form action="" method="GET"> <!-- Using GET for filtering -->
                                        <select name="filter" id="filter">
                                            <option value="">--Select Category--</option>
                                            <?php
                                            include_once('conn.php');
                                            $stmt = $conn->prepare("SELECT * FROM category");
                                            $stmt->execute();
                                            while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . htmlspecialchars($cat['name']) . '">' . htmlspecialchars($cat['name']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" id="catfil"  style="background-color:grey; color: white; border-radius: 5px;">Search</button>
                                    </form>
                                    <form action="" method="GET" > 
                                        <select name="delete" id="delete">
                                            <option value="">--Select Category--</option>
                                            <?php
                                            include_once('conn.php');
                                            $stmt = $conn->prepare("SELECT * FROM category");
                                            $stmt->execute();
                                            while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="' . htmlspecialchars($cat['name']) . '">' . htmlspecialchars($cat['name']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" id="delete" onclick="return confirmBatchDelete()" style="background-color:darkorange; color: white; border-radius: 5px;">Batch Delete</button>
                                    </form>
                                </div>
                            </h2>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-dark ">
                                    <thead>
                                        <tr>
                                            <th scope="col">Product ID</th>
                                            <th scope="col">Product Category</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Product Image</th>
                                            <th scope="col">Product Price(RM)</th>
                                            <th scope="col">Stock Available</th>
                                            <th scope="col">Product Details</th>          
                                            <th scope="col">Edit</th>        
                                            <th scope="col">Delete</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_GET['filter']) && !empty($_GET['filter'])) {
                                            $selectedCategory = $_GET['filter'];
                                            $stmt = $conn->prepare("SELECT * FROM productadmin WHERE category = ?");
                                            $stmt->execute([$selectedCategory]);
                                        } elseif (isset($_GET['delete']) && !empty($_GET['delete'])) {
                                            $delete = $_GET['delete'];
                                            $stmt = $conn->prepare("DELETE FROM productadmin WHERE category = ?");
                                            $stmt->execute([$delete]);
                                            // Execute a new query to fetch all products after deletion
                                            $stmt = $conn->prepare("SELECT * FROM productadmin");
                                            $stmt->execute();
                                        } else {
                                            $stmt = $conn->prepare("SELECT * FROM productadmin");
                                            $stmt->execute();
                                        }
                                        $users = $stmt->fetchAll();
                                        foreach ($users as $user) {
                                            ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo $user['category']; ?></td>

                                                <td><?php echo $user['name']; ?></td>
                                                <td>
                                                    <?php
                                                    // Check if the 'image' key in the $user array is set and not empty
                                                    $width = 250;  // width in pixels
                                                    $height = 150; // height in pixels
                                                    if (isset($user['image']) && !empty($user['image'])) {
                                                        $imageName = $user['image'];
                                                        echo "<img src='$imageName' alt='User Image'width='$width' height='$height' >";
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo $user['price']; ?></td>
                                                <td><?php echo $user['stock']; ?></td>

                                                <td style="color: white"><pre><?php echo $user['details']; ?></pre></td>
                                                                                        <td>
                                                                                                <a href="prodCRUD_Upd.php?id=<?= $user['id'] ?>" class="btn btn-primary">
                                                                                                    <i class="fa fa-pencil"></i> Edit</a>
                                                                                        </td>
                                                                                        <td>
                                                                                            <form action="products.php" method="POST">
                                                                                                <button type="submit" name="deleteProduct" value="<?= $user['name'] ?>" class="btn btn-danger" onclick="return confirmDelete()">
                                                                                                    <i class="fa fa-trash"></i> Delete
                                                                                                </button>
                                                                                            </form>
                                                                                        </td>


                                                                      </tr>
                                            <?php
                                        }
                                        ?>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>


<script>


    function confirmDelete() {
        return confirm('Are you sure you want to delete this product?');
    }

    function confirmBatchDelete() {
        return confirm('Are you sure you want to delete this category products?');
    }

    /*
     * Modal popup
     */
    // Get the modal
    var modal = $('#modalDialog');

    // Get the button that opens the modal
    var btn = $("#mbtn");

    // Get the  element that closes the modal
    var span = $(".close");

    $(document).ready(function () {
        // When the user clicks the button, open the modal 
        btn.on('click', function () {
            modal.show();
        });

        // When the user clicks on  (x), close the modal
        span.on('click', function () {
            modal.hide();
        });
    });

    // When the user clicks anywhere outside of the modal, close it
    $('body').bind('click', function (e) {
        if ($(e.target).hasClass("modal")) {
            modal.hide();
        }
    });
</script>