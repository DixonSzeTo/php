<?php

include ('conn.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $name = $url = "";
    if (empty($_POST["action"])) {
        echo "Pick an action!<br>";
    } else {
        $action = $_POST["action"];
    }

    if (empty($_POST["name"])) {
        echo "Fill in a category name!<br>";
    } else {
        $name = $_POST["name"];
    }

    if (empty($_POST["url"])) {
        echo "Fill in the category URL!<br>";
    } else {
        $url = $_POST["url"];
    }
} else {
    header("Location: Admin_dashboard.php");
    exit();
}

switch ($action) {
    case "create":
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->beginTransaction();
            $conn->exec("INSERT INTO category (name, url) VALUES ('$name', '$url')");
            $conn->commit();
            echo "Category Added";
        } catch (PDOException $e) {
            $conn->rollback();
            echo "<br>Error: " . $e->getMessage();
        }
        $conn = null;
        break;
    case "upname":
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE category SET name='$name' WHERE url='$url'";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            echo "category Updated Successfully!";
        } catch (PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
        }
        $conn = null;
        break;
    case "upurl":
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE category SET url = '$url' WHERE name ='$name'";

            $stmt = $conn->prepare($sql);
            $stmt->execute();

            echo "Category Updated Successfully!";
        } catch (PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
        }
        $conn = null;
        break;
    case "del":
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "DELETE FROM category WHERE name = '$name' ";
            $conn->exec($sql);
            echo "Record deleted successfully";
        } catch (PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
        }

        $conn = null;
}
