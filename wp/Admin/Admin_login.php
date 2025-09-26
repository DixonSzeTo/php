<!DOCTYPE html>
<html lang="en">


<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Webpage</title>
    
    <link rel="stylesheet" href="#">
</head>

      

<body>
    <?php
    session_start();
    if (isset($_SESSION['stId']) && isset($_SESSION['stPass'])) {
        header('Location: Admin_dashboard.php');
        exit;
    }
    ?>
    <div class="login-container">
        <h2 style="font-size: 30px;text-align: center"><u>Admin Login</u></h2>
        <form action="Admin.php" method="post">
            <input type="text" name="staffID" placeholder="Staff ID" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit"  >Login</button>
        </form>
           <a href="ForgotPassword2.php" class="forgot-password">Forgot your password?</a>
    </div>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .login-container {
            padding: 170px; 
            border: 1px solid #ccc;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: white; 
            width: 750px; 
            display: flex;
            flex-direction: column;
            gap: 30px; 
            
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            margin:10px;
            padding: 12px; 
            border: 1px solid #ccc;
            border-radius: 4px;
            
        }
 
        .options {
            display: flex;
            justify-content: space-between; 
            align-items: center;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px; 
            margin:10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 104%; 
        }
        button:hover {
             background-color: #0056b3;
        }
        .forgot-password {
            color: #555;
            text-decoration: none;
            font-size: 12px;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
        
         
    </style>
</body>
</html>