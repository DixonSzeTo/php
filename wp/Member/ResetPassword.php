<?php
include ('../conn.php');

try {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';  // The email should be passed as a hidden input
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword === $confirmPassword) {
            // Hash the new password for security
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update the user's password
            $stmt = $conn->prepare("UPDATE User SET Password = :password WHERE Email = :email");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);

            $stmt->execute();

            echo "Your password has been reset successfully.";
        } else {
            echo "Passwords do not match. Please try again.";
        }
    }
} catch (PDOException $e) {
    echo "Error updating password: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member - Reset Password</title>
</head>
<body>
    <h1>Reset Your Password</h1>
    <form action="" method="POST"> 
        <!-- Email is passed as a hidden input from the URL query parameter -->
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'], ENT_QUOTES, 'UTF-8'); ?>">

        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>