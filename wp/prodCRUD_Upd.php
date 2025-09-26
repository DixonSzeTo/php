<?php
session_start();
include('conn.php');
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
        <link href="resource/css/udproduct.css" rel="stylesheet">

        <title>Admin | Update Product</title>
    </head>
    <body>          
        <header>

        </header>
        <div class="container">
            <h1>Edit & Update Product</h1>
            <!-- The Modal -->
            <div id="modalDialog" class="modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content animate-top">
                        <div class="modal-header">
                            <h5 class="modal-title ">Product Details</h5>
                            <a href="prodCRUD.php">
                                <button>Back</button>
                            </a>
                        </div>
                        <?php
                        if (isset($_GET['id'])) {
                            $product_id = $_GET['id'];
                            $query = "SELECT * FROM productadmin WHERE id=:id LIMIT 1";
                            $statement = $conn->prepare($query);
                            $data = [':id' => $product_id];
                            $statement->execute($data);
                            $result = $statement->fetch(PDO::FETCH_OBJ);
                        }
                        ?>
                        <form method="POST" action="products.php" enctype="multipart/form-data">
                            <div class="modal-body">
                                <!-- Form submission status -->
                                <div class="response"></div>
                                <div class="form-group row">
                                    <div class="col-md-5">
                                        <div class="row">

                                            <div class="col-sm-4">
                                                <label for="id">Product ID:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" name="id" id="id" placeholder="Enter Product ID" required="" value="<?= $result->id; ?>" readonly="true" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6"> <!-- "Product Category" takes the other half -->
                                        <label for="category">Product Category:</label>
                                        <select name="category" id="category">
                                            <?php
                                            $stmt = $conn->prepare("SELECT * FROM category");
                                            $stmt->execute();

                                            // Assuming $result is the product being edited and contains a 'category' field with the name of the category
                                            $currentCategory = $result->category; // This assumes $result->category holds the current category of the product

                                            while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                // Check if this category is the current category of the product
                                                $selected = ($cat['name'] == $currentCategory) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($cat['name']) . '" ' . $selected . '>' . htmlspecialchars($cat['name']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Product Name:</label>
                                    <input type="text" name="pname" id="pname" placeholder="Enter Product Name" required="" value="<?= $result->name; ?>">
                                </div>                                
                                <div class="form-group">
                                    <label>Product Image:</label>
                                    <input type="file" name="img" id="img" placeholder="Insert Product Image"  accept="image/png, image/jpeg, image/gif"  >  
                                    <?php if ($result->image): ?>
                                        <img src="<?= htmlspecialchars($result->image); ?>" alt="Current Image" style="max-height: 250px;">
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label>Product Price:</label>
                                    <input type="number" name="price" id="price"  placeholder="Enter Product Price" required="" min="0" step="any" value="<?= $result->price; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Stock Available:</label>
                                    <input type="number" name="stock" id="stock"  placeholder="Available Stock" required="" min="0" step="1" value="<?= $result->stock; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Product Details:</label>
                                    <textarea name="detail" id="detail" class="form-control" placeholder="Enter Product Details" rows="4"><?= $result->details; ?></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <form action="products.php" method="POST">
                                    <!-- Submit button -->
                                    <button type="submit" name="update" id="update" >Update</button>
                                </form>  
                            </div>
                    </div>
                </div>  
            </div>
        </form>
    </div>                        


</body>
</html>
