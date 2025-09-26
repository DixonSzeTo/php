<?php
session_start();
include ('../conn.php');

try {
    // Get the form data
    $staffId = $_POST['staffID']; 
    $passwordInput = $_POST['password']; 

    // Query to find the admin with the given Staff ID
    $sql = "SELECT * FROM Admin WHERE Staff_ID = :staffId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':staffId', $staffId);
    $stmt->execute();

    // Fetch the admin record
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Verify the input password with the stored hash
        if (password_verify($passwordInput, $admin['Password'])) {
            $_SESSION['stId'] = $staffId;
            $_SESSION['stPass'] = $passwordInput;
            // Login successful - redirect to the admin dashboard
            header("Location: admin_dashboard.php");
            exit(); 
        } else {
            echo "Invalid password. Please try again.<br>"; 
        }
    } else {
        echo "Invalid Staff ID. Please try again.<br>";
    }

} catch (PDOException $e) {
   
    echo "Error: " . $e->getMessage();
    exit(); 
}

// Close the connection
$conn = null; 
?>

