<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "mywp";

try {
    // Step 1: Connect to the server and create the database if needed
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to server successfully<br>";

    // Create the database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "Database '$dbname' created successfully<br>";

    // Step 2: Connect to the database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database '$dbname' successfully<br>";

    // Step 3: Create the User table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS User (
    No INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    First_Name VARCHAR(30) NOT NULL,
    Last_Name VARCHAR(30) NOT NULL,
    Phone_Number VARCHAR(30) NOT NULL UNIQUE,
    Email VARCHAR(100) NOT NULL UNIQUE,  
    Gender VARCHAR(10) NOT NULL,
    Date_Joined DATE, 
    Password VARCHAR(255) NOT NULL,
    Profile_Photo VARCHAR(255) 
)";
    
    

    $conn->exec($sql);
    echo "Table 'User' created successfully<br>";

    // Set auto-increment to start from 1
    $conn->exec("ALTER TABLE User AUTO_INCREMENT = 1");
    
    $conn->exec("ALTER TABLE User ADD LoginAttempts INT DEFAULT 0");
    $conn->exec("ALTER TABLE User ADD LastAttemptTime TIMESTAMP NULL DEFAULT NULL");


     // Handle POST request for registration
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $f_name = $_POST['fname'] ?? null;
        $l_name = $_POST['lname'] ?? null;
        $email = $_POST['email'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $password = $_POST['password'] ?? null;
        $gender = $_POST['gender'] ?? null; // New gender field
        $dateJoined = date('Y-m-d'); // Current date

        if ($f_name && $l_name && $email && $phone && $password && $gender) {
            // Check if email or phone number already exists
            $stmt = $conn->prepare("SELECT * FROM User WHERE Email = :email OR Phone_Number = :phone");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "Error: User with this email or phone number already exists.";
            } else {
                $conn->beginTransaction();
                $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Encrypt the password

                $stmt = $conn->prepare("INSERT INTO User (First_Name, Last_Name, Email, Phone_Number, Gender,Date_Joined, Password) VALUES (:f_name, :l_name, :email, :phone, :gender,:dateJoined, :password)");
                $stmt->bindParam(':f_name', $f_name);
                $stmt->bindParam(':l_name', $l_name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':gender', $gender); 
                $stmt->bindParam(':dateJoined', $dateJoined);
                $stmt->bindParam(':password', $hashed_password);

                $stmt->execute();
                $conn->commit();

                echo "Registration successful!";
            }
        } else {
            echo "Error: All fields are required.";
        }
    } else {
        echo "Error: Invalid request method.";
    }

} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage();
}
?>
