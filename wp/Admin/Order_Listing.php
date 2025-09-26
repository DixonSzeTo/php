<?php
session_start();
include ('../conn.php');

$orderToUpdate = null;

// Handle POST requests for CRUD operations
if (!isset($_SESSION['stId']) && !isset($_SESSION['stPass'])) {
    header('Location: Admin_login.php');
    exit;
} else {
    // Handle POST requests for CRUD operations
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['create_order'])) {
            // Determine the next primary key
            $sql = "SELECT MAX(id) AS maxId FROM paypal_payment";
            $stmt = $conn->query($sql);
            $result1 = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextId = ($result1['maxId'] ?? 0) + 1;

            // Get form data for creating a new user
            $product_ID = $_POST['product_ID'];
            $quantity = $_POST['quantity'];

            $uid = $_POST['uid'];
            $email = $_POST['email'];
            $ship_addr = $_POST['ship_addr'];

            $stmt = $conn->prepare("SELECT name, price FROM productadmin WHERE id = :product_ID");
            $stmt->bindParam(':product_ID', $product_ID);
            $stmt->execute();
            $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result2) {
                $price = $result2['price'];
                $name = $result2['name'];
                $total = $result2['price'] * $quantity;
            }

            // Ensure required fields are filled
            if ($product_ID && $quantity && $uid && $email && $ship_addr) {
                $sql = "INSERT INTO paypal_payment (id, date_created, product_ID, product_name, product_price, quantity, total, uid, email, ship_addr)
                VALUES (:id, CURRENT_TIMESTAMP, :product_ID, :product_name, :product_price, :quantity, :total, :uid, :email, :ship_addr)";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $nextId);
                $stmt->bindParam(':product_ID', $product_ID);
                $stmt->bindParam(':product_name', $result2['name']);
                $stmt->bindParam(':product_price', $result2['price']);
                $stmt->bindParam(':quantity', $quantity);
                $stmt->bindParam(':total', $total);
                $stmt->bindParam(':uid', $uid);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':ship_addr', $ship_addr);

                try {
                    $stmt->execute();
                    echo "Order created successfully with ID: " . $nextId;
                } catch (PDOException $e) {
                    echo "Error creating order: " . $e->getMessage();
                }
            } else {
                echo "Error: All fields are required for creating an order.";
            }
        }

        if (isset($_POST['edit_order'])) {
            $id = $_POST['id'];
            $sql = "SELECT * FROM paypal_payment WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $orderToUpdate = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (isset($_POST['change_order'])) {
            $product_ID = $_POST['product_ID'];
            $quantity = $_POST['quantity'];
            $uid = $_POST['uid'];
            $email = $_POST['email'];
            $ship_addr = $_POST['ship_addr'];

            $stmt_select = $conn->prepare("SELECT name, price FROM productadmin WHERE id = :product_ID");
            $stmt_select->bindParam(':product_ID', $product_ID);
            $stmt_select->execute();
            $result = $stmt_select->fetch(PDO::FETCH_ASSOC);

            if ($product_ID && $quantity && $uid && $email && $ship_addr) {
                $total = $result['price'] * $quantity;

                $stmt_update = $conn->prepare("UPDATE paypal_payment SET 
                product_ID = :product_ID, 
                product_name = :product_name,
                product_price = :product_price,
                quantity = :quantity, 
                total = :total,
                uid = :uid, 
                email = :email ,
                ship_addr = :ship_addr
                WHERE id = :id");

                $stmt_update->bindParam(':product_ID', $product_ID);
                $stmt_update->bindParam(':product_name', $result['name']);
                $stmt_update->bindParam(':product_price', $result['price']);
                $stmt_update->bindParam(':quantity', $quantity);
                $stmt_update->bindParam(':total', $total);
                $stmt_update->bindParam(':uid', $uid);
                $stmt_update->bindParam(':email', $email);
                $stmt_update->bindParam(':ship_addr', $ship_addr);
                // Assuming the id is also coming from the form
                $stmt_update->bindParam(':id', $_POST['id']);

                try {
                    $stmt_update->execute();
                    echo "Order updated successfully.";
                } catch (PDOException $e) {
                    echo "Error updating order: " . $e->getMessage();
                }
            } else {
                echo "Error: All fields are required for updating an order.";
            }
        }

        if (isset($_POST['delete_order'])) {
            $id = $_POST['id'];
            if ($id) {
                $sql = "DELETE FROM paypal_payment WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id);

                try {
                    $stmt->execute();
                    echo "Order deleted successfully.";
                } catch (PDOException $e) {
                    echo "Error deleting order: " . $e->getMessage();
                }
            } else {
                echo "Error: Primary key is missing.";
            }
        }
    }
}
// Read and display all users
$sql = "SELECT * FROM paypal_payment ORDER BY id";
$stmt = $conn->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Order Management</title>
    </head>
    <body>
        <style>
            h2{
                text-align: center;
                font-size: 28px;
            }
            table{
                width: 100%;
                background-color: white;
            }
            th {
                height: 60px;
            }
            td{
                height:30px;
                text-align: center;
            }

            input{
                margin-bottom: 10px;
                width: 50%;
                height: 30px;
            }
            h3{
                font-size: 20px;
            }
            .updateor{
                background-color: lightgray;
                width: 45%;
                padding: 10px;
                margin-top: 10px;
                float: left;
                margin-left: 10px;
            }

            body{
                background-color: white;
            }
            a button {
                text-decoration: none;
                color: white;
                background-color:red;
                padding: 10px 20px 10px 20px;
                float: right;
            }
        </style>
        <a href="Admin_dashboard.php"><button >Back </button></a>
        <h2>Order Listing</h2>

        <!-- Display all orders in a table -->
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Date Created</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Member ID</th>
                <th>Email</th>
                <th>Shipping Address</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['date_created']; ?></td>
                    <td><?php echo $order['product_ID']; ?></td>
                    <td><?php echo $order['product_name']; ?></td>
                    <td><?php echo $order['product_price']; ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td><?php echo $order['total']; ?></td>
                    <td><?php echo $order['uid']; ?></td>
                    <td><?php echo $order['email']; ?></td>
                    <td><?php echo $order['ship_addr']; ?></td>
                    <td>
                        <!-- Form for editing -->
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="edit_order">Edit</button>
                        </form>

                        <!-- Form for deleting -->
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="delete_order">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <div class="updateor">
            <!-- Form to update an existing user -->
            <h3>Edit Order History</h3>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $orderToUpdate['id'] ?? ''; ?>"></br>
                Product ID:
                <input type="text" name="product_ID" placeholder="Product ID" value="<?php echo $orderToUpdate['product_ID'] ?? ''; ?>"></br>
                Quantity: 
                <input type="number" name="quantity" placeholder="Quantity" value="<?php echo $orderToUpdate['quantity'] ?? ''; ?>"></br>
                Member ID:
                <input type="text" name="uid" placeholder="Member ID" value="<?php echo $orderToUpdate['uid'] ?? ''; ?>"></br>
                Email:
                <input type="email" name="email" placeholder="Email" value="<?php echo $orderToUpdate['email'] ?? ''; ?>"></br>
                Address:
                <input type="text" name="ship_addr" placeholder="Address" value="<?php echo $orderToUpdate['ship_addr'] ?? ''; ?>"></br>
                <br>
                <button type="submit" name="change_order">Change</button>
            </form>
        </div>
    </body>
</html>
