<?php
session_start();

include('../conn.php');

// Lockout duration in seconds (e.g., 5 minutes)
$lockoutDuration = 300; // 5 minutes

try {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($email && $password) {
            // Check if the email exists in the database
            $stmt = $conn->prepare("SELECT Password, LoginAttempts, LastAttemptTime FROM User WHERE Email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $currentTime = time();

                // Check if the user is currently locked out
                if ($user['LoginAttempts'] >= 3 && ($currentTime - strtotime($user['LastAttemptTime'])) < $lockoutDuration) {
                    echo "Error: Your account is temporarily locked. Please try again later.";
                    exit;
                }

                // Verify the provided password against the stored hashed password
                if (password_verify($password, $user['Password'])) {
                    // Reset login attempts and last attempt time on successful login
                    $stmt = $conn->prepare("UPDATE User SET LoginAttempts = 0, LastAttemptTime = NULL WHERE Email = :email");
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    
                    $_SESSION['email'] = $email;
                    $_SESSION['password'] = $password;

                    // Login successful, redirect to profile.php
                    header("Location: ../profile.php");
                    exit; // Stop further script execution after redirection
                } else {
                    // Increment login attempts and update last attempt time on failed login
                    $stmt = $conn->prepare("UPDATE User SET LoginAttempts = LoginAttempts + 1, LastAttemptTime = NOW() WHERE Email = :email");
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();

                    echo "Error: Incorrect password. Please try again.";
                }
            } else {
                echo "Error: Incorrect email. Please try again.";
            }
        } else {
            echo "Error: Email and password are required.";
        }
    } else {
        echo "Error: Invalid request method.";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>