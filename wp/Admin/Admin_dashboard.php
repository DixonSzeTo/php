<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="../resource/css/default.css"> 
        <style>

            .container {
                margin: 20px;
                padding: 20px;
                border: 1px solid #ccc;
                background: #f9f9f9;
            }

            nav ul {
                list-style: none;
                padding: 0;
            }

            nav ul li {
                display: inline-block;
                margin-right: 10px;
            }

            nav a {
                text-decoration: none;
                color: blue;
            }

            nav a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <?php
        session_start();
        if (!isset($_SESSION['stId']) && !isset($_SESSION['stPass'])) {
            header('Location: Admin_login.php');
            exit;
        } else {
            include('../conn.php');
            $staffID = $_SESSION['stId'];
            $stmt = $conn->prepare("SELECT First_Name, Last_Name, Pfp FROM admin WHERE Staff_ID = :staffID");
            $stmt->bindParam(':staffID', $staffID);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        ?>
        <div class="container">
            <h1>Welcome to the Admin Dashboard</h1>
            <table>
                <tr>
                    <td style="padding-left: 50px; padding-right: 50px; padding-bottom: 50px;">
                        <img src="<?php echo '../Admin/pfp/' . basename($admin['Pfp']); ?>" width="200" height="200">
                    </td>
                    <td>
                        <h2>Staff Information: </h2>
                        <p>Staff Name: <?php echo $admin['First_Name']." ".$admin['Last_Name']?></p>
                        <p>Staff ID: <?php echo $staffID ?></p>
                    </td>
                </tr>
            </table>

            <nav>
                <ul>
                    <li><a href="../catCRUD.php">Catalog Listing</a></li>
                    <li><a href="../prodCRUD.php">Product Listing</a></li>
                    <li><a href="Order_Listing.php">Order Listing</a></li>
                    <li><a href="Member_Listing.php">Member Listing</a></li>
                    <li><a href="Admin_Listing.php">Admin Listing</a></li> 
                </ul>
            </nav>
            <br>
            <a href="adLogout.php" style="color: red;">Log out</a>


        </div>
    </body>
</html>