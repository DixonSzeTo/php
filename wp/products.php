<?php

session_start();
include('conn.php');

if (isset($_POST['deleteProduct'])) {
    $name = $_POST['deleteProduct'];
    try {
        $query = "DELETE FROM productadmin WHERE name = :name";
        $statement = $conn->prepare($query);
        $data = [':name' => $name];
        $query_execute = $statement->execute($data);

        if ($query_execute) {
            $_SESSION['message'] = "Product Deleted Successfully";
        } else {
            $_SESSION['message'] = "Failed to Delete Product";
        }
    } catch (Exception $ex) {
        $_SESSION['message'] = "Error while deleting: " . $ex->getMessage();
    }
    header('Location: prodCRUD.php');
    exit;
}

if (isset($_POST['addpro'])) {
    $id = $_POST['id'];
    $cat = $_POST['category'];
    $pname = $_POST['pname'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $detail = $_POST['detail'];
    
    $stmt = $conn->prepare("SELECT id FROM category WHERE name = :cat");
    $stmt->bindParam (':cat', $cat);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    $catID = $category['id'];

    // Check if product ID already exists
    $checkQuery = "SELECT COUNT(*) FROM productadmin WHERE id = :id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([':id' => $id]);
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        // If product exists, set an error message and redirect
        $_SESSION['idf'] = "Product with ID $id already exists.";
        header('Location: prodCRUD.php');
        exit;
    }

    // Proceed if no existing ID is found
    $image_name = $_FILES["img"]["name"];
    $image_tmp = $_FILES["img"]["tmp_name"];
    $image_folder = "resource/img/products/";
    $image_path = $image_folder . $image_name;

    if (!file_exists($image_folder)) {
        mkdir($image_folder, 0775, true);
    }

    if (move_uploaded_file($image_tmp, $image_path)) {
        $query = "INSERT INTO productadmin (id, cat_ID, category, name, image, price, stock,details) VALUES (:id, :catID, :category, :pname, :image_path, :price,:stock, :detail)";
        $stmt = $conn->prepare($query);
        $data = [
            ':id' => $id,
            ':catID' => $catID,
            ':category' => $cat,
            ':pname' => $pname,
            ':image_path' => $image_path,
            ':price' => $price,
            ':detail' => $detail,
            ':stock' => $stock,
        ];

        if ($stmt->execute($data)) {
            $_SESSION['message'] = "Product Inserted Successfully";
            header('Location: prodCRUD.php');
            exit;
        } else {
            $_SESSION['message'] = "Product Not Inserted";
            header('Location: prodCRUD.php');
            exit;
        }
    } else {
        $_SESSION['message'] = "Error uploading image. Check the destination folder permission.";
        header('Location: prodCRUD.php');
        exit;
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $cat = $_POST['category'];
    $name = $_POST['pname'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $detail = $_POST['detail'];
    
    $stmt = $conn->prepare("SELECT id FROM category WHERE name = :cat");
    $stmt->bindParam (':cat', $cat);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    $catID = $category['id'];

    $image_folder = "resource/img/products/";
    $image_uploaded = isset($_FILES["img"]["name"]) && $_FILES["img"]["error"] == 0;
    $image_path = "";

    if ($image_uploaded) {
        $image_name = $_FILES["img"]["name"];
        $image_tmp = $_FILES["img"]["tmp_name"];
        $image_path = $image_folder . $image_name;

        if (!file_exists($image_folder)) {
            mkdir($image_folder, 0775, true);
        }

        if (!move_uploaded_file($image_tmp, $image_path)) {
            $_SESSION['message'] = "Failed to upload image";
            header('Location: prodCRUD.php');
            exit;
        }
    }

    try {
        // Prepare SQL based on whether an image was uploaded
        if ($image_uploaded) {
            $query = "UPDATE productadmin SET name=:pname, image=:image_path,  category=:category, price=:price, stock=:stock, details=:detail WHERE id=:id LIMIT 1";
        } else {
            $query = "UPDATE productadmin SET name=:pname, category=:category, price=:price, stock=:stock, details=:detail WHERE id=:id LIMIT 1";
        }

        $statement = $conn->prepare($query);
        $data = [
            ':id' => $id,
            ':category' => $cat,
            ':pname' => $name,
            ':price' => $price,
            ':detail' => $detail,
            ':stock' => $stock,
        ];

        if ($image_uploaded) {
            $data[':image_path'] = $image_path;
        }

        if ($statement->execute($data)) {
            $_SESSION['message'] = "Product Updated Successfully";
            header('Location: prodCRUD.php');
            exit;
        } else {
            $_SESSION['message'] = "Product Not Updated";
            header('Location: prodCRUD.php');
            exit;
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>
