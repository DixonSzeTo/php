<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
    <head>
        <meta charset="utf-8">
        <title>OSTY | Product Cart</title>
        <link href="resource/css/default.css" rel="stylesheet">
        <link href="resource/css/paypal.css" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">

        <!-- Bootstrap CSS library -->
        <link rel="stylesheet" href=
              "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
              integrity=
              "sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
              crossorigin="anonymous">

        <!-- jQuery library -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
                integrity=
                "sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>

        <!-- JS library -->
        <script src=
                "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
                integrity=
                "sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>

        <!-- Latest compiled JavaScript library -->
        <script src=
                "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
                integrity=
                "sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    </head>
    <body>
        <?php
        session_start();

        if (!isset($_SESSION['email']) && !isset($_SESSION['password'])) {
            header("Location: Member/Member_login.php");
            exit();
        } else {
            include('conn.php');
            //Validae if user have already set up their address.
            $stmt = $conn->prepare("SELECT Address FROM user WHERE Email = :email");
            $stmt->bindParam(':email', $_SESSION['email']);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
            //Fetch address
            if ($result) {
                $address = $result['Address'];
                if ($address == "") {
                    $_SESSION['msg'] = "<small class='text-danger'>You left out your address!</small>";
                    header("Location: accountEdit.php");
                    exit();
                }
            }
            // If the user clicked the add to cart button on the product page we can check for the form data
            if (isset($_POST['id'], $_POST['quantity']) && is_numeric($_POST['id']) && is_numeric($_POST['quantity'])) {
                // Set the post variables so we easily identify them, also make sure they are integer
                $id = (int) $_POST['id'];
                $quantity = (int) $_POST['quantity'];
                // Prepare the SQL statement, we basically are checking if the product exists in our databaser
                $stmt = $conn->prepare('SELECT * FROM productadmin WHERE id = ?');
                $stmt->execute([$_POST['id']]);
                // Fetch the product from the database and return the result as an Array
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                // Check if the product exists (array is not empty)
                if ($product && $quantity > 0) {
                    // Product exists in database, now we can create/update the session variable for the cart
                    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                        if (array_key_exists($id, $_SESSION['cart'])) {
                            // Product exists in cart so just update the quanity
                            $_SESSION['cart'][$id] += $quantity;
                        } else {
                            // Product is not in cart so add it
                            $_SESSION['cart'][$id] = $quantity;
                        }
                    } else {
                        // There are no products in cart, this will add the first product to cart
                        $_SESSION['cart'] = array($id => $quantity);
                    }
                }
                // Prevent form resubmission...
                header('location: cart.php');
                exit;
            }
// Remove product from cart, check for the URL param "remove", this is the product id, make sure it's a number and check if it's in the cart
            if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
                // Remove the product from the shopping cart
                unset($_SESSION['cart'][$_GET['remove']]);
            }
// Update product quantities in cart if the user clicks the "Update" button on the shopping cart page
            if (isset($_POST['update']) && isset($_SESSION['cart'])) {
                // Loop through the post data so we can update the quantities for every product in cart
                foreach ($_POST as $k => $v) {
                    if (strpos($k, 'quantity') !== false && is_numeric($v)) {
                        $id = str_replace('quantity-', '', $k);
                        $quantity = (int) $v;
                        // Always do checks and validation
                        if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                            // Update new quantity
                            $_SESSION['cart'][$id] = $quantity;
                        }
                    }
                }
                // Prevent form resubmission...
                header('Location: cart.php');
                exit;
            }
            if (isset($_POST['placeorder']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                header('Location: index.php?page=placeorder');
                exit;
            }
// Check the session variable for products in cart
            $products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
            $products = array();
            $subtotal = 0.00;
// If there are products in cart
            if ($products_in_cart) {
                // There are products in the cart so we need to select those products from the database
                // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
                $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
                $stmt = $conn->prepare('SELECT * FROM productadmin WHERE id IN (' . $array_to_question_marks . ')');
                // We only need the array keys, not the values, the keys are the id's of the products
                $stmt->execute(array_keys($products_in_cart));
                // Fetch the products from the database and return the result as an Array
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Calculate the subtotal
                foreach ($products as $product) {
                    $subtotal += (float) $product['price'] * (int) $products_in_cart[$product['id']];
                }
            }
        }
        ?>
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
        </header>
        <h1 class="container mt-5">Shopping Cart</h1>
        <div class="container border content-wrapper mb-5">
            <form action="cart.php" method="post">
                <table class="table">
                    <thead>
                        <tr>
                            <td colspan="2">Product</td>
                            <td>Price</td>
                            <td>Quantity</td>
                            <td>Total</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">My dear, nothingness is priceless! (and we don't sell them.)</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="img">
                                        <a href="product.php?id=<?= $product['id'] ?>">
                                            <img src="<?= $product['image'] ?>" width="50" height="50" alt="<?= $product['name'] ?>">
                                        </a>
                                    </td>
                                    <td>
                                        <a href="product.php?id=<?= $product['id'] ?>"><?= $product['name'] ?></a>
                                        <br>
                                        <a href="cart.php?remove=<?= $product['id'] ?>" class="remove">Remove</a>
                                    </td>
                                    <td class="price">RM<?= $product['price'] ?></td>
                                    <td class="quantity">
                                        <input type="number" name="quantity-<?= $product['id'] ?>" value="<?= $products_in_cart[$product['id']] ?>" min="1" max="<?= $product['stock'] ?>" placeholder="Quantity" required>
                                    </td>
                                    <td class="price">RM<?= $total_price = $product['price'] * $products_in_cart[$product['id']] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="subtotal">
                    <span class="text">Subtotal</span>
                    <span class="price">RM<?= $subtotal ?></span>
                </div>
                <br>
                <div class="buttons">
                    <input type="submit" value="Update" name="update">
                </div>
            </form>
            <?php
            $total_amount = 0;
            if (isset($_SESSION['cart'])) {
// Calculate total amount for all products in the cart
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    // Retrieve product information
                    $product_stmt = $conn->prepare('SELECT price FROM productadmin WHERE id = ?');
                    $product_stmt->execute([$product_id]);
                    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

                    // Calculate total amount for this product
                    $total_amount += $product['price'] * $quantity;
                }
            }
            ?>

            <!-- Set up the PayPal module for payment -->
            <form method="post" action="https://www.sandbox.paypal.com/cgi-bin/webscr">
                <input type="hidden" name="business" value="sb-obi2b14644101@business.example.com">
                <input type="hidden" name="amount" value="<?php echo $total_amount ?>">
                <input type="hidden" name="currency_code" value="MYR">
                <input type="hidden" name="no_shipping" value="1">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="return" value="http://localhost/OSTY/placeorder.php">
                <input type="hidden" name="cancel_return" value="http://localhost/OSTY/ordercancel.php">
                <div class="paypal">
                    <h3>Place Order: </h3>
                    <button type="submit" name="paypal"><img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" border="0" alt="PayPal Logo"></button>
                </div>
            </form>
        </div>
        <footer>
            <p>
                This website references the following brands: <br>
            <ol>
                <li>r.e.m beauty</li>
                <li>Sephora</li>
                <li>Ulta</li>
            </ol>

            <em>Beauty belongs to everyone<br>
                -OSTY Cosmetics</em>
        </p>
    </footer>
</body>
</html>
