<?php
session_start();
include ('conn.php');

// Handle POST requests for CRUD operations
if (!isset($_SESSION['email']) && !isset($_SESSION['password'])) {
    header('Location: Member/Member_login.php');
    exit;
} else {
    //Prepare user ID
    $stmt = $conn->prepare("SELECT id FROM user WHERE Email = :email"); // Retrieve records, ordered by primary key
    $stmt->bindParam(':email', $_SESSION['email']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array
    if ($user) {
        $id = $user['id'];
    }

    // Read and display all admin records
    $stmt = $conn->prepare("SELECT * FROM paypal_payment WHERE uid = :id ORDER BY id"); // Retrieve all records, ordered by primary key
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all as associative array
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>OSTY | Order History</title>
        <link href="resource/css/default.css" rel="stylesheet">

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
    <style>
        a button {
            text-decoration: none;
            color: white;   
            background-color:red;
            padding: 10px 20px 10px 20px;
            float: left;
        }
        h2{
            text-align: center;
        }

    </style>
    <body>
        <header>
            <nav>
                <ul style="margin: 0;padding: 0;">
                    <li><a href="Member/Member_login.php">Member</a></li>
                    <li><a href="Admin/Admin_login.php">Admins</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="product.php">Product</a></li>
                    <li><a href="index.php">Main</a></li>

                </ul>
            </nav>
            <a href="index.php"><img class="logo" style="margin-right: 0; padding-right: 1276px;" src="resource/img/osty-cosmetic.png" alt="Logo"></a>
        </header>        

        <!-- Display admin records in a table -->
        <a href="profile.php"><button>Back </button></a><br>

        <table class="table container">
            <h2>
                Order History
            </h2>
            <tr>
                <th>Order Date</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Shipping Address</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['date_created']; ?></td>
                    <td><?php echo $order['product_name']; ?></td>
                    <td><?php echo $order['product_price']; ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td><?php echo $order['total']; ?></td>
                    <td><?php echo $order['ship_addr']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

    </body>
</html>
