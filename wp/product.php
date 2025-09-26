<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link href="resource/css/product.css" rel="stylesheet">
        <link href="resource/css/default.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <title>OSTY | Product</title>

    </head>
    <body>
        <header>
            <nav>
                <ul style="margin: 0;padding: 0;">
                    <li><a href="Member/Member_login.php">Member</a></li>
                    <li><a href="Admin/Admin_login.php">Admin</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="product.php">Product</a></li>
                    <li><a href="index.php">Main</a></li>

                </ul>
            </nav>
            
            <a href="index.php"><img class="logo" style="margin-right: 0;" src="resource/img/osty-cosmetic.png" alt="Logo"></a>
            <nav>
                <ul class="bottomnav">
                    <?php
                    $servername = "localhost";
                    $username = "username";
                    $password = "password";
                    $dbname = "OSTY";

                    try {
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $sql = "SELECT name, url FROM category";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<li><a href="?name=' . $row["name"] . '">' . $row["name"] . '</a></li>';
                            }
                        }
                    } catch (PDOException $e) {
                        echo $sql . "<br>" . $e->getMessage();
                    }
                    $conn->close();
                    ?>
                </ul>
            </nav>
        </header>
        
        <div class="container pb-5 px-5 my-5 border-none " > 
            <?php
            session_start();
            if (!isset($_SESSION['email']) && !isset($_SESSION['password'])) {
                header("Location: Member/Member_login.php");
                exit();
            } else {
                include_once('conn.php');

                if (isset($_GET['name'])) {
                    $selectedCategory = $_GET['name'];
                    $stmt = $conn->prepare("SELECT * FROM productadmin WHERE category = ?");
                    $stmt->execute([$selectedCategory]);  // Pass parameters directly to execute method
                    $categoryDisplayed = false;

                    while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        if (!$categoryDisplayed) {
                            // Display category only once
                            ?>
                            <h1><?php echo htmlspecialchars($user['category']); ?></h1>
                            <?php
                            $categoryDisplayed = true;
                        }

                        // Display product information
                        ?>
                        <div class="row g-0 my-3 border product-row">
                            <div class="col">
                                <?php
                                $width = 302;  // width in pixels
                                $height = 350; // height in pixels
                                if (!empty($user['image'])) {
                                    $imageName = htmlspecialchars($user['image']);
                                    echo "<img src='$imageName' alt='User Image' width='$width' height='$height'>";
                                }
                                ?>
                            </div>
                            <div class="col-9 px-3">
                                <h2 style="margin-top: 10px;"><?php echo htmlspecialchars($user['name']); ?></h2>
                                <p style="color: #5E5A5A;font-size: 22px; font-weight: 600;"><?php echo "RM " . htmlspecialchars($user['price']); ?></p>
                                <p style="font-weight: 600;font-size: 18px;"><?php echo "Stock Avaliable: " . htmlspecialchars($user['stock']); ?></p>
                                <p>Product Details: </p>
                                <pre style="background-color: white ;"><?php echo htmlspecialchars($user['details']); ?></pre>
                                <form action="cart.php" method="post">
                                    Quantity Required: 
                                    <input type="number" style="width: 100px;" name="quantity" value="1" min="1" step="1" max="<?= htmlspecialchars($user['stock']) ?>" placeholder="stock" required>
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                    <button type="submit">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>
    </body>
</html>
        <script>
            window.onload = function() {
                var urlParams = new URLSearchParams(window.location.search);
                if (!urlParams.has('name')) {
                    var firstCategoryLink = document.querySelector('.bottomnav li a');
                    if (firstCategoryLink) {
                        firstCategoryLink.click();
                    }
                }
            };
        </script>