<?php
include ('../conn.php'); 

try {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];

        // Check if the email exists
        $stmt = $conn->prepare("SELECT * FROM Admin WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Redirect to ResetPassword.php with email
            header("Location: ResetPassword2.php?email=" . urlencode($email));
            exit;
        } else {
            echo "No account found with this email address.";
        }
    }
} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Forgot Password</title>
</head>
<body>
    <h1>Forgot Your Password?</h1>
    <form action="" method="POST"> <!-- This form sends email to the same page for processing -->
        <label for="email">Enter your email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>