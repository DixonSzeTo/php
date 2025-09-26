<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>OSTY | Edit Profile</title>
        <link href="css/default.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">

    <head>
        <meta charset="UTF-8">
        <title>Edit Profile</title>
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
    <body>
        <?php
        session_start();
//if email and password is not set, force user back to login screen
        if (!isset($_SESSION['email']) && !isset($_SESSION['password'])) {
            header("Location: Member/Member_login.php");
            exit();
        } else {
            include('conn.php');

            if (isset($_POST['removePfp'])) {
                $stmt = $conn->prepare("UPDATE user SET Pfp = NULL WHERE Email = :email");
                $stmt->bindParam(':email', $_SESSION["email"]);
                $stmt->execute();
            } else if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['name_change1'] && !empty($_POST['name_change2']))) {
                //Update info if available
                $stmt = $conn->prepare("UPDATE user SET First_Name = :fn, Last_Name = :ln, Bio = :bio WHERE Email = :email");
                $stmt->bindParam(':fn', $_POST['name_change1']);
                $stmt->bindParam(':ln', $_POST['name_change2']);
                $stmt->bindParam(':bio', $_POST['bio_change']);
                $stmt->bindParam(':email', $_SESSION["email"]);
                $stmt->execute();
                
                if (!empty($_FILES['img']['name'])) {
                    $image_name = $_FILES["img"]["name"];
                    $image_tmp = $_FILES["img"]["tmp_name"];
                    $image_folder = "resource/img/userImg/";
                    $image_path = $image_folder . $image_name;

                    if (!file_exists($image_folder)) {
                        mkdir($image_folder, 0775, true);
                    }

                    if (move_uploaded_file($image_tmp, $image_path)) {
                        $email = $_SESSION['email'];
                        $query = "UPDATE user SET Pfp = :image_path WHERE Email = :email";
                        $stmt = $conn->prepare($query);
                        $data = [
                            ':image_path' => $image_path,
                            ':email' => $email,
                        ];

                        if ($stmt->execute($data)) {
                            $imgAdd = "New image uploaded.";
                        } else {
                            $imgAdd = "Image upload failed.";
                        }
                    } else {
                        $imgAdd = "Something went wrong with the upload process.";
                    }
                }
            }
            // Fetch bio from the database if it's not a POST request
            $stmt = $conn->prepare("SELECT First_Name, Last_Name, Bio, Gender, Pfp FROM user WHERE Email = :email");
            $stmt->bindParam(':email', $_SESSION["email"]);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
            if ($result) {
                $fn = $result['First_Name'];
                $ln = $result['Last_Name'];
                $bio = $result['Bio'];
                $gender = $result['Gender'];
                if ($result['Pfp'] != null) {
                    $pfp = $result['Pfp'];
                } else {
                    $pfp = "";
                }
            }
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
        <h1 class="container mt-5">Edit Profile Details
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['name_change1']) && !empty($_POST['name_change2'])) {
                echo '<small class="text-warning">Edit successful</small>';
            }
            ?>
        </h1>
        <div style="height: 600px;" class="container border mb-5"> 
            <table class="mt-5">
                <tr class="mt-5">
                    <td>
                        <img src="<?php echo $pfp ?>"
                             onerror="this.onerror=null; this.src='resource/img/pfpDefault.png'"
                             style="width: 250px; height: 250px;" 
                             class="img-thumbnail rounded-circle float-sm-left ml-5 mr-5">        
                    </td>

                    <td>
                        <form action="" method="post" enctype="multipart/form-data">
                            <label for="name_change1">First Name*: </label><br>
                            <input type="text" name="name_change1" id="name_change1" maxlength="20" size = "50" placeholder="First name" value="<?php echo $fn; ?>"><br>
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($_POST['name_change1'])) {
                                echo '<div class="text-danger">This field must be filled in. </div>';
                            } else {
                                echo "<br>";
                            }
                            ?>
                            <label for="name_change2">Last Name*: </label><br>
                            <input type="text" name="name_change2" id="name_change2" maxlength="20" size = "50" placeholder="Last name" value="<?php echo $ln; ?>">
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($_POST['name_change2'])) {
                                echo '<div class="text-danger">This field must be filled in. </div>';
                            } else {
                                echo "<br><br>";
                            }
                            ?>
                            <label for="bio_change">Bio (optional): </label><br>
                            <textarea style="resize: none;" name="bio_change" id="bio_change" maxlength="100" rows="4" cols="50" placeholder="Enter something interesting, like introduce yourself!"><?php echo $bio; ?></textarea><br><br>
                            
                            <label for="gender_change">Gender: </label>
                            <input type="radio" name="gender_change" id="gender_change" value="Male"
                                   <?php if ($gender == "Male") {
                                       echo "checked";
                                   } ?>> Male
                            <input type="radio" name="gender_change" id="gender_change" value="Female"
                                   <?php if ($gender == "Female") {
                                       echo "checked";
                                   } ?>> Female<br><br>
                            
                            <label for="img">Change picture:</label>
                            <input type="file" name="img" id="img" placeholder="Insert Product Image" accept="image/png, image/jpeg, image/gif"><br>
                            <?php if (empty($imgAdd)) {
                                $imgAdd = " ";
                            } else {
                                echo "<p class='text-danger'>".$imgAdd."</p>";
                            }
                            ?><br>
                            
                            <input type="submit" value="Save Changes">
                            <input class="text-warning" type="submit" id="removePfp" name="removePfp" value="Remove Profile Picture">
                            <a href="profile.php" class="text-danger">Cancel</a>
                        </form>
                    </td>
                </tr>
            </table>
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

