<?php
session_start();
include('../conn.php');

$userToUpdate = null;

if (!isset($_SESSION['stId']) && !isset($_SESSION['stPass'])) {
    header('Location: Admin_login.php');
    exit;
} else {
    try {

        // Handle POST requests for CRUD operations
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Handle file upload
            $targetDir = "/resource/img/userImg/";
            $profilePhoto = '';
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['size'] > 0) {
                $targetFilePath = $targetDir . basename($_FILES["profile_photo"]["name"]);
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                $profilePhoto = $targetFilePath;

                if (!move_uploaded_file($_FILES["profile_photo"]["tmp_name"], "../" . $targetFilePath)) {
                    echo "Sorry, there was an error uploading your file.";
                }
            }

            if (isset($_POST['create_user'])) {
                // Determine the next primary key
                $sql = "SELECT MAX(id) AS maxId FROM User";
                $stmt = $conn->query($sql);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $nextId = ($result['maxId'] ?? 0) + 1;

                // Get form data for creating a new user
                $firstName = $_POST['first_name'];
                $lastName = $_POST['last_name'];
                $phoneNumber = $_POST['phone'];
                $email = $_POST['email'];
                $gender = $_POST['gender'];
                $dateJoined = date('Y-m-d');
                $address = $_POST['address'];
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

                // Ensure required fields are filled
                if ($firstName && $lastName && $phoneNumber && $email && $password) {
                    $sql = "INSERT INTO User (id, First_Name, Last_Name, Phone_Number, Email, Gender, Date_Joined, Password, Address, Pfp)
                        VALUES (:id, :firstName, :lastName, :phoneNumber, :email, :gender, :dateJoined, :password, :address, :profilePhoto)";

                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam('id', $nextId);
                    $stmt->bindParam(':firstName', $firstName);
                    $stmt->bindParam(':lastName', $lastName);
                    $stmt->bindParam(':phoneNumber', $phoneNumber);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':gender', $gender);
                    $stmt->bindParam(':dateJoined', $dateJoined);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':address', $address);
                    $stmt->bindParam(':profilePhoto', $profilePhoto);

                    try {
                        $stmt->execute();
                        echo "User created successfully with ID: " . $nextId;
                    } catch (PDOException $e) {
                        echo "Error creating user: " . $e->getMessage();
                    }
                } else {
                    echo "Error: All fields are required for creating a user.";
                }
            }

            if (isset($_POST['edit_user'])) {
                $id = $_POST['id'];
                $sql = "SELECT * FROM User WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $userToUpdate = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if (isset($_POST['update_user'])) {
                $id = $_POST['id'];
                $firstName = $_POST['first_name'] ?? '';
                $lastName = $_POST['last_name'] ?? '';
                $phoneNumber = $_POST['phone'] ?? '';
                $email = $_POST['email'] ?? '';

                if ($firstName && $lastName && $phoneNumber && $email && $id) {
                    $sql = "UPDATE User SET 
                        First_Name = :firstName, 
                        Last_Name = :lastName, 
                        Phone_Number = :phoneNumber, 
                        Email = :email,
                        Address = :address,
                        Pfp = :profilePhoto
                        WHERE id = :id";

                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':firstName', $firstName);
                    $stmt->bindParam(':lastName', $lastName);
                    $stmt->bindParam(':phoneNumber', $phoneNumber);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':address', $address);
                    $stmt->bindParam(':profilePhoto', $profilePhoto);

                    try {
                        $stmt->execute();
                        echo "User updated successfully.";
                    } catch (PDOException $e) {
                        echo "Error updating user: " . $e->getMessage();
                    }
                } else {
                    echo "Error: All fields are required for updating a user.";
                }
            }

            if (isset($_POST['delete_user'])) {
                $id = $_POST['id'];
                if ($id) {
                    $sql = "DELETE FROM User WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $id);

                    try {
                        $stmt->execute();
                        echo "User deleted successfully.";
                    } catch (PDOException $e) {
                        echo "Error deleting user: " . $e->getMessage();
                    }
                } else {
                    echo "Error: Primary key is missing.";
                }
            }
        }

        // Read and display all users
        $sql = "SELECT * FROM User ORDER BY id";
        $stmt = $conn->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Member Management</title>
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

            .createmem{
                background-color: lightsteelblue;
                width: 45%;
                padding: 10px;
                margin-top: 10px;
                margin-left: 50px;
                float: left;
                margin-bottom: 20px;
            }
            .info input{
                margin-bottom: 10px;
                width: 50%;
                height: 30px;
            }


            h3{
                font-size: 20px;
            }
            .updatemem{
                background-color: lightgray;
                width: 45%;
                padding: 10px;
                margin-top: 10px;
                float: left;
                margin-left: 10px;
            }

            body{
                background-color:lightcyan;
            }
            a button {
                text-decoration: none;
                color: white;
                background-color:red;
                padding: 10px 20px 10px 20px;
                float: right;
            }
        </style>
    </head>
    <body>
        <a href="Admin_dashboard.php"><button >Back </button></a>
        <h2>Member Listing</h2>

        <!-- Display all users in a table -->
        <table border="1">
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Gender</th> 
                <th>Date Joined</th>
                <th>Address</th>
                <th>Profile Photo</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['First_Name']; ?></td>
                    <td><?php echo $user['Last_Name']; ?></td>
                    <td><?php echo $user['Phone_Number']; ?></td>
                    <td><?php echo $user['Email']; ?></td>
                    <td><?php echo $user['Gender']; ?></td>
                    <td><?php echo $user['Date_Joined']; ?></td>
                    <td><?php echo $user['Address']; ?></td>
                    <td><?php echo $user['Pfp'] ? '<img src="../' . $user['Pfp'] . '" width="100px" height="100px">' : 'N/A'; ?></td>
                    <td>
                        <!-- Form for editing -->
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="edit_user">Edit</button>
                        </form>

                        <!-- Form for deleting -->
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="createmem">
            <!-- Form to create a new user -->
            <h3>Create New User</h3>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="info">
                    First Name:
                    <input type="text" name="first_name" placeholder="First Name" required><br>
                    Last Name:
                    <input type="text" name="last_name" placeholder="Last Name" required><br>
                    Phone Number:
                    <input type="tel" name="phone" placeholder="Enter your phone number" pattern="[0-9]{10,15}" title="Phone number must contain 10-15 digits" required><br>
                    Email:
                    <input type="email" name="email" placeholder="Email" required><br>
                    Address:
                    <input type="text" name="address" placeholder="Address"><br>
                    Password:
                    <input type="password" name="password" placeholder="Password" required><br>
                    Profile Photo:
                    <input type="file" name="profile_photo"><br>

                </div>
                <label for="gender">Gender:</label>
                <input type="radio" id="male" name="gender" value="Male" required> 
                <label for="male">Male</label>
                <input type="radio" id="female" name="gender" value="Female" required> 
                <label for="female">Female</label>
                <br><br>
                <button type="submit" name="create_user">Create</button>
            </form>
        </div>
        <div class="updatemem">
            <!-- Form to update an existing user -->
            <h3>Update User Information</h3>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="info">
                    <input type="hidden" name="id" value="<?php echo $userToUpdate['id'] ?? ''; ?>"><br>
                    First Name:
                    <input type="text" name="first_name" placeholder="First Name" value="<?php echo $userToUpdate['First_Name'] ?? ''; ?>"><br>
                    Last Name:
                    <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $userToUpdate['Last_Name'] ?? ''; ?>"><br>
                    Phone Number:
                    <input type="text" name="phone" placeholder="Phone Number" value="<?php echo $userToUpdate['Phone_Number'] ?? ''; ?>"><br>
                    Email:
                    <input type="email" name="email" placeholder="Email" value="<?php echo $userToUpdate['Email'] ?? ''; ?>"><br>
                    Address:
                    <input type="text" name="address" placeholder="Address" value="<?php echo $userToUpdate['Address'] ?? ''; ?>"><br>
                    Profile Photo:
                    <input type="file" name="profile_photo"><br>
                    <button type="submit" name="update_user">Update</button>
                </div>
            </form>
        </div>
    </body>
</html>