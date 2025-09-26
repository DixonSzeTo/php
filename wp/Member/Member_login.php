<!DOCTYPE html>
<html lang="en">
    <!--
    Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
    Click nbfs://nbhost/SystemFileSystem/Templates/Other/html.html to edit this template
    -->

    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Member Webpage</title>

            <link rel="stylesheet" href="Member.css">
        </head>


        <body>
            <?php
            session_start();
            if (isset($_SESSION['email']) && (isset($_SESSION['password']))) {
                header('Location: ../profile.php');
            }
            ?>
            <header>
                <h1 style="font-size: 50px">Welcome to Our Shop</h1>
            </header>
            <div class="container">
                <div class="signup-container">
                    <h2>NEW CUSTOMERS</h2>
                    <p>There are many advantages to creating an account, including expedited checkout, multiple address storage, order tracking, and more.</p>
                    <form action="Register.html" method="POST">
                        <button type="submit" >SIGN UP</button>
                    </form>
                </div>
                <div class="login-container">
                    <?php if (isset($_SESSION['message'])) : ?>
                        <h5 style=text-align: center; class="alert alert-success"><?= $_SESSION['message']; ?></h5>
                        <?php
                        unset($_SESSION['message']);
                    endif;
                    ?>
                    <h2>REGISTERED CUSTOMERS</h2>
                    <p>if you have an account,sign in with your personal details.</p>
                    <form id="login-form" action="Member.php" method="POST" onsubmit="return validateForm()">
                        <label for="login_email">Email : </label> <br><br>
                        <input type="email" name="email" id="login_email" placeholder="Enter your email" required><br><br>
                        <label for="login_password">Password : </label><br><br>
                        <input type="password" name="password" id="login_password" placeholder="Enter your password" required><br><br>
                        <button type="submit">Login</button>
                    </form>

                    <a href="ForgotPassword.php" class="forgot-password">Forgot your password?</a>
                </div>
            </div>



            <script src="Register.js"></script>

            <style>

                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }

                header {
                    text-align: center;
                    margin-top: 50px;
                }

                .container {
                    display: flex;
                    justify-content: center;
                    margin-top: 50px;
                }

                .signup-container, .login-container {
                    width: 8000px;
                    padding: 100px;
                    border: 1px solid #ccc;
                    margin: 0 20px;
                }

                h2 {
                    text-align: center;
                }

                form {
                    margin-top: 20px;
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                button {
                    width: 100%;
                    padding: 10px;
                    margin-bottom: 10px;
                }

                button {
                    background-color: #007bff;
                    color: #fff;
                    border: none;
                    cursor: pointer;
                }

                button:hover {
                    background-color: #0056b3;
                }
            </style>
        </body>
    </html>
