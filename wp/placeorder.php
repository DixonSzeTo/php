<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
    <head>
        <meta charset="utf-8">
        <title>Order Placed!</title>
        <link href="resource/css/default.css" rel="stylesheet">
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
        include('conn.php');

        if (isset($_SESSION['email']) && isset($_SESSION['password']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            // Get user information
            $email = $_SESSION['email'];
            $password = $_SESSION['password'];

            $stmt = $conn->prepare('SELECT * FROM user WHERE Email = :email');
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $user_id = $result['id'];
                $address = $result['Address'];
            }

            // Deduct product stock from the database
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                // Retrieve current stock amount for the product
                $stock_stmt = $conn->prepare('SELECT stock FROM productadmin WHERE id = ?');
                $stock_stmt->execute([$product_id]);
                $current_stock = $stock_stmt->fetchColumn();

                if ($current_stock !== false && $current_stock >= $quantity) {
                    // Calculate new stock amount after deduction
                    $new_stock = $current_stock - $quantity;

                    // Update product stock in the database
                    $update_stmt = $conn->prepare('UPDATE productadmin SET stock = ? WHERE id = ?');
                    $update_stmt->execute([$new_stock, $product_id]);
                } else {
                    // Handle insufficient stock scenario (e.g., redirect to cart with a message)
                    $_SESSION['error_message'] = 'Insufficient stock for product ' . $product_id;
                    header('Location: cart.php');
                    exit();
                }
            }

            // Insert transaction records
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $product_stmt = $conn->prepare('SELECT name, price FROM productadmin WHERE id = ?');
                $product_stmt->execute([$product_id]);
                $product = $product_stmt->fetch(PDO::FETCH_ASSOC);
                $product_name = $product['name'];
                $product_price = $product['price'];
                $total_price = $product_price * $quantity;

                $insert_stmt = $conn->prepare('INSERT INTO paypal_payment (date_created, product_id, product_name, product_price, quantity, total, uid, email, ship_addr) VALUES (CURRENT_TIMESTAMP, ?, ?, ?, ?, ?, ?, ?, ?)');
                $insert_stmt->execute([$product_id, $product_name, $product_price, $quantity, $total_price, $user_id, $email, $address]);
            }

            // Unset the cart session variable
            unset($_SESSION['cart']);

            // If required, include recording PayPal payment info here
        } else {
            // Redirect user to login page if not logged in or cart is empty
            header("Location: Member/Member.php");
            exit();
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
            <a href="index.php"><img class="logo" style="margin-right: 0; padding-right: 1276px;" src="resource/img/osty-cosmetic.png" alt="Logo"></a>
        </header>
        <div class="placeorder content-wrapper text-center">
            <img src="resource\img\paid.png" style="max-height: 250px; max-width: 250px; display: block; margin-left: auto; margin-right: auto; width: 50%;" alt="paid.png" class="mt-5 mb-5">
            <h1>Your Order Has Been Placed</h1>
            <p>Thank you for ordering with us! We'll be delivering your orders soon.</p>
            <a href="index.php" class="text-info">Go Back</a>
        </div>
    </body>
</html>