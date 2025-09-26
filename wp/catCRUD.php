<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>

    <head>
        <meta charset="utf-8">
        <title>Admin - Category</title>
        <link href="resource/css/default.css" rel="stylesheet">
        <link href="resource/css/catcrud.css" rel="stylesheet">

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

        <style>
            a button {
                text-decoration: none;
                color: white;
                background-color:red;
                padding: 10px 20px 10px 20px;
                float: right;
                margin-right: 180px;
            }
        </style>
    </head>

    <body>
        <?php
        session_start();
        if (!isset($_SESSION['stId']) && !isset($_SESSION['stPass'])) {
            header('Location: Admin/Admin_login.php');
            exit;
        }
        ?>
        <a href="Admin/Admin_dashboard.php"><button >Back </button></a>

        <h1 class="container mt-5">Category Listing</h1>
        <div style="height: 450px;" class="container border mb-5"> 
            <div class="mt-3 ml-3 mr-3 mb-3">
                <p>
                    Administration page for changing category of the navigation. Modification of the page requires the name of category and name of the file (HTML/PHP). Creation of new category requires manually inserting new files in accordance to the category.
                </p>
                <form  method="POST" action="category.php";>
                    Action: 
                    <input type="radio" name="action" value="create">Create 
                    <input type="radio" name="action" value="upname">Update Name
                    <input type="radio" name="action" value="upurl">Update URL
                    <input type="radio" name="action" value="del">Delete<br><br>
                    Category name: <input type="text" name="name"><br><br>
                    Category webpage: <input type="text" name="url"><br><br>
                    <input class="button" type="submit" value="Change">
                    <input class="button" type="reset" value="Reset">
                </form>
            </div>
        </div>
    </body>
</html>