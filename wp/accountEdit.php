<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>OSTY | Edit Account</title>
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
        } else {
            include('conn.php');
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email_change']) && !empty($_POST['address'])) {
                $newEmail = $_POST['email_change'];
                $newPass = !empty($_POST['pass_change']) ? md5($_POST['pass_change']) : null;
                $newPhone = $_POST['phone_change'];
                $newAddr = $_POST['address'];

                // Get the user ID
                $stmt = $conn->prepare("SELECT id FROM user WHERE Email = :email");
                $stmt->bindParam(':email', $_SESSION["email"]);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $id = $result['id'];

                // Construct the SQL query based on whether password fields are empty or not
                if ($newPass !== null) {
                    $stmt = $conn->prepare("UPDATE user SET Email = :new_email, Password = :new_pass, Phone_Number = :new_phone, Address = :address WHERE id = :id");
                    $stmt->bindParam(':new_pass', $newPass);
                } else {
                    $stmt = $conn->prepare("UPDATE user SET Email = :new_email, Phone_Number = :new_phone, Address = :address WHERE id = :id");
                }

                // Bind parameters and execute the query
                $stmt->bindParam(':new_email', $newEmail);
                $stmt->bindParam(':new_phone', $newPhone);
                $stmt->bindParam(':address', $newAddr);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    $_SESSION["email"] = $newEmail;
                }
            }

// Fetch and display updated user information
            $stmt = $conn->prepare("SELECT * FROM user WHERE Email = :email");
            $stmt->bindParam(':email', $_SESSION["email"]);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $phone = $result['Phone_Number'];
                $id = $result['id'];
                $address = $result['Address'];
            }

            $conn = null;
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
        <h1 class="container mt-5">Account Settings and Security
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email_change']) && !empty($_POST['address'])) {
                echo '<small class="text-warning">Edit successful</small>';
            } else if (empty($address) && !empty($_SESSION['msg'])) {
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
            }
            ?>
        </h1>
        <div style="height: 650px;" class="container border mb-5"> 
            <div class="mt-3 ml-3 mr-3 mb-3">
                <h2>Security</h2>
                <form action="" method="post">
                    <label for="email_change">Email*: </label><br>
                    <input type="text" name="email_change" id="email_change" size = "50" placeholder="Email must be valid. " value="<?php echo $_SESSION['email']; ?>">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($_POST['email_change'])) {
                        echo "<p class='text-danger'>Please enter your email.</p>";
                    } else {
                        echo "<br><br>";
                    }
                    ?>

                    <label for="pass_change">Password: </label><br>
                    <input type="password" name="pass_change" id="pass_change" maxlength="16" size="50" 
                           placeholder="Change your password">
                    <p class='text-warning'>*Please make sure you create a strong password, but can only be remembered by you. Do not reveal your password to anyone.</p>

                    <label for="pass_changeConf">Re-enter password*: </label><br>
                    <input type="password" name="pass_changeConf" id="pass_changeConf" maxlength="16" size="50" 
                           placeholder="Re-enter password for new password"><br>
                           <?php
                           if (!empty($_POST["pass_change"]) || !empty($_POST["pass_changeConf"])) {
                               // At least one password field is not empty
                               if ($_POST["pass_change"] != $_POST["pass_changeConf"]) {
                                   // Passwords don't match, display error message
                                   echo "<p class='text-danger'>Password mismatched / empty. Please try again.</p>";
                               }
                           }
                           ?>
                    <br>
                    <label for="phone_change">Phone Number: </label><br>
                    <input type="text" name="phone_change" id="phone_change" size = "50" placeholder="Put your phone number here for easier contact." value="<?php echo $phone; ?>"><br>
                    <br>
                    <label for="address">Shipping Address*: </label><br>
                    <textarea style="resize: none;" name="address" id="address" maxlength="100" rows="4" cols="50" placeholder="Set your address; It is mandatory for using our shipping service."><?php echo $address; ?></textarea>
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['address'])) {
                        echo "<p class='text-danger'>This field cannot be left blank!</p>";
                    } else {
                        echo "</br></br>";
                    }
                    ?>
                    <input type = "submit" value = "Save Changes">
                    <a href="profile.php" class="text-danger">Cancel</a>
                </form>
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
