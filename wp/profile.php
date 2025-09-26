<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
    <head>
        <meta charset="utf-8">
        <title>OSTY | Member</title>
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
        if (!isset($_SESSION['email']) && !isset($_SESSION['password'])) {
            header("Location: Member/Member_login.php");
            exit();
        } else {
            include('conn.php');

            $stmt = $conn->prepare("SELECT First_Name, Last_Name, Bio, Pfp FROM user WHERE Email = :email");
            $stmt->bindParam(':email', $_SESSION["email"]);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
            if ($result) {
                $fn = $result['First_Name'];
                $ln = $result['Last_Name'];
                $bio = $result['Bio'];
                if ($result['Pfp'] != null) {
                    $pfp = $result['Pfp'];
                } else {
                    $pfp = "";
                }
            }
            $conn = null;
        }
        ?>
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
        </header>
        <h1 class="container mt-5">Your Profile Page</h1>
        <div style="height: 350px;" class="container border mb-5"> 
            <div class="mt-5 mb-5">
                <img src="<?php echo $pfp ?>"
                     onerror="this.onerror=null; this.src='resource/img/pfpDefault.png'"
                     style="width: 250px; height: 250px;" 
                     class="img-thumbnail rounded-circle float-sm-left ml-5 mr-5">
                <h2><?php echo $fn . " " . $ln; ?></h2>
                <p><i><?php
                        if (empty($bio)) {
                            echo ("This member has not left anything interesting here!");
                        } else {
                            echo $bio;
                        }
                        ?></i></p>
                <br>
                <a href="profileEdit.php"><h5>Edit Profile</h5></a>
                <a href="accountEdit.php"><h5>Account Settings</h5></a>        
                <a href="orderHistory.php"><h5>Order History</h5></a>
                <a href="Member/logout.php" class="text-danger"><h5>Logout</h5></a>
            </div>
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