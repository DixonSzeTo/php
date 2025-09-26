<?php
session_start();
include('../conn.php');

// Create uploads directory if it doesn't exist
$uploadsDirectory = __DIR__ . '/pfp/';
if (!file_exists($uploadsDirectory)) {
    mkdir($uploadsDirectory, 0755, true);
}

$adminToUpdate = null;

if (!isset($_SESSION['stId']) && !isset($_SESSION['stPass'])) {
    header('Location: Admin_login.php');
    exit;
} else {
// Handle POST requests for CRUD operations
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['create_admin'])) {
            // Determine the next primary key
            $sql = "SELECT MAX(No) AS maxNo FROM Admin";
            $stmt = $conn->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextNo = ($result['maxNo'] ?? 0) + 1; // Start from 1 if no records
            // Get form data
            $staffId = $_POST['staff_id'];
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];
            $phoneNumber = $_POST['phone_number'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $dateJoined = date('Y-m-d'); // Current date
            $position = $_POST['position'];
            $profilePhoto = null;

            if ($_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $profilePhoto = uploadProfilePhoto('profile_photo');
            }

            if ($staffId && $firstName && $lastName && $phoneNumber && $email && $position) {

                $sql = "INSERT INTO Admin (No, Staff_ID, First_Name, Last_Name, Phone_Number, Email, Password, Date_Joined, Position, Pfp)
                 VALUES (:no, :staffId, :firstName, :lastName, :phoneNumber, :email, :password, :dateJoined, :position, :profilePhoto)";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':no', $nextNo);
                $stmt->bindParam(':staffId', $staffId);
                $stmt->bindParam(':firstName', $firstName);
                $stmt->bindParam(':lastName', $lastName);
                $stmt->bindParam(':phoneNumber', $phoneNumber);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':dateJoined', $dateJoined);
                $stmt->bindParam(':position', $position);
                $stmt->bindParam(':profilePhoto', $profilePhoto);

                try {
                    $stmt->execute();
                    echo "Admin created successfully with ID: " . $nextNo;
                } catch (PDOException $e) {
                    echo "Error creating admin: " . $e->getMessage();
                }
            } else {
                echo "Error: All fields are required for creating an admin.";
            }
        }

        if (isset($_POST['edit_admin'])) {
            $no = $_POST['no'];
            $sql = "SELECT * FROM Admin WHERE No = :no";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':no', $no);
            $stmt->execute();
            $adminToUpdate = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (isset($_POST['update_admin'])) {
            if (!isset($_POST['no'])) {
                echo "Error: Primary key is missing.";
                exit();
            }

            // Get the new data from the form
            $no = $_POST['no'];
            $firstName = $_POST['first_name'] ?? '';
            $lastName = $_POST['last_name'] ?? '';
            $phoneNumber = $_POST['phone_number'] ?? '';
            $email = $_POST['email'] ?? '';
            $position = $_POST['position'] ?? '';
            $profilePhoto = null;

            if ($_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $profilePhoto = uploadProfilePhoto('profile_photo');
            }

            if ($firstName && $lastName && $phoneNumber && $email && $position) {
                $sql = "UPDATE Admin SET 
                        First_Name = :firstName, 
                        Last_Name = :lastName, 
                        Phone_Number = :phoneNumber, 
                        Email = :email, 
                        Position = :position,
                        Pfp = :profilePhoto
                    WHERE No = :no";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':firstName', $firstName);
                $stmt->bindParam(':lastName', $lastName);
                $stmt->bindParam(':phoneNumber', $phoneNumber);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':position', $position);
                $stmt->bindParam(':profilePhoto', $profilePhoto);
                $stmt->bindParam(':no', $no);

                try {
                    $stmt->execute();
                    echo "Admin updated successfully.";
                } catch (PDOException $e) {
                    echo "Error updating admin: " . $e->getMessage();
                }
            } else {
                echo "Error: All fields are required for updating an admin.";
            }
        }

        if (isset($_POST['delete_admin'])) {
            if (!isset($_POST['no'])) {
                echo "Error: Primary key is missing.";
                exit();
            }

            $no = $_POST['no'];

            $sql = "DELETE FROM Admin WHERE No = :no";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':no', $no);

            try {
                $stmt->execute();
                echo "Admin deleted successfully.";
            } catch (PDOException $e) {
                echo "Error deleting admin: " . $e->getMessage();
            }
        }
    }
}

// Read and display all admin records
$sql = "SELECT * FROM Admin ORDER BY No"; // Retrieve all records, ordered by primary key
$stmt = $conn->query($sql);
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all as associative array

// Function to handle file upload
function uploadProfilePhoto($inputName) {
    $targetDir = __DIR__ . '/pfp/';
    $targetFile = $targetDir . basename($_FILES[$inputName]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES[$inputName]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES[$inputName]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedFormats = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedFormats)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFile)) {
            echo "The file " . basename($_FILES[$inputName]["name"]) . " has been uploaded.";
            return $targetFile;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Management</title>
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

            .createad{
                background-color: lightsteelblue;
                width: 45%;
                padding: 10px;
                margin-top: 10px;
                margin-left: 50px;
                float: left;
                margin-bottom: 20px;
            }
            input{
                margin-bottom: 10px;
                width: 50%;
                height: 30px;
            }
            h3{
                font-size: 20px;
            }
            .updatead{
                background-color: lightgray;
                width: 45%;
                padding: 10px;
                margin-top: 10px;
                float: left;
                margin-left: 10px;
            }

            body{
                background-color:lightyellow;
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
        <h2>Admin Listing</h2>

        <!-- Display admin records in a table -->
        <table border="1">
            <tr>
                <th>No</th>
                <th>Staff ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Date Joined</th>
                <th>Position</th>
                <th>Profile Photo</th>
                <th>Actions</th>
            </tr>
<?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?php echo $admin['No']; ?></td>
                    <td><?php echo $admin['Staff_ID']; ?></td>
                    <td><?php echo $admin['First_Name']; ?></td>
                    <td><?php echo $admin['Last_Name']; ?></td>
                    <td><?php echo $admin['Phone_Number']; ?></td>
                    <td><?php echo $admin['Email']; ?></td>
                    <td><?php echo $admin['Date_Joined']; ?></td>
                    <td><?php echo $admin['Position']; ?></td>
                    <td>
    <?php if ($admin['Pfp']): ?>
                            <img src="<?php echo '../Admin/pfp/' . basename($admin['Pfp']); ?>" width="100" height="100">
                        <?php else: ?>
                            No photo uploaded
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Form for editing -->
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="no" value="<?php echo $admin['No']; ?>">
                            <button type="submit" name="edit_admin">Edit</button>
                        </form>

                        <!-- Form for deletion -->
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="no" value="<?php echo $admin['No']; ?>">
                            <button type="submit" name="delete_admin">Delete</button>
                        </form>
                    </td>
                </tr>
<?php endforeach; ?>
        </table>
        <div class="createad">
        <!-- Form to create a new admin -->
        <h3>Create New Admin</h3>
        <form action="" method="post" enctype="multipart/form-data">
            Staff ID:
            <input type="text" name="staff_id" placeholder="Staff ID" required><br>
            First Name:
            <input type="text" name="first_name" placeholder="First Name" required><br>
            Last Name:
            <input type="text" name="last_name" placeholder="Last Name" required><br>
            Phone number:
            <input type="text" name="phone_number" placeholder="Phone Number" required><br>
            Email:
            <input type="email" name="email" placeholder="Email" required><br>
            Password:
            <input type="password" name="password" placeholder="Password" required><br>
            Job Position:
            <input type="text" name="position" placeholder="Position" required><br>
            Profile Photo:
            <input type="file" name="profile_photo" accept="image/*"><br>
            <button type="submit" name="create_admin">Create</button>
        </form>
        </div>
        <div class="updatead">
        <!-- Form to update an existing admin -->
        <h3>Update Admin Information</h3>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="no" value="<?php echo $adminToUpdate['No'] ?? ''; ?>"> <!-- Ensure correct primary key -->
            First Name:
            <input type="text" name="first_name" placeholder="First Name" value="<?php echo $adminToUpdate['First_Name'] ?? ''; ?>"><br>
            Last Name:
            <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $adminToUpdate['Last_Name'] ?? ''; ?>"><br>
            Phone Number:
            <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo $adminToUpdate['Phone_Number'] ?? ''; ?>"><br>
            Email:
            <input type="email" name="email" placeholder="Email" value="<?php echo $adminToUpdate['Email'] ?? ''; ?>"><br>
            Job Position:
            <input type="text" name="position" placeholder="Position" value="<?php echo $adminToUpdate['Position'] ?? ''; ?>"><br>
            Profile Photo:
            <input type="file" name="profile_photo" accept="image/*"><br>
            <button type="submit" name="update_admin">Update</button>
        </form>
        </div>
    </body>
</html>