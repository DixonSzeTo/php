<?php

$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "OSTY";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // sql to create table
    $sql = "CREATE TABLE IF NOT EXISTS admin (
        No INT AUTO_INCREMENT PRIMARY KEY,
        Staff_ID VARCHAR(10) UNIQUE,
        First_Name VARCHAR(50) NOT NULL,
        Last_Name VARCHAR(50) NOT NULL,
        Phone_Number VARCHAR(15) NOT NULL,
        Email VARCHAR(100) UNIQUE NOT NULL,
        Password VARCHAR(255) NOT NULL,
        Date_Joined DATE NOT NULL,
        Position VARCHAR(50) NOT NULL,
        Pfp VARCHAR(255) NOT NULL
    )";
    $conn->exec($sql);
    echo "Table admin created successfully.<br>";

    //next sql
    $sql = "CREATE TABLE IF NOT EXISTS category (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(8) NOT NULL,
    url VARCHAR(20)NOT NULL
    )";
    $conn->exec($sql);
    echo "Table category created successfully.<br>";

    //next sql again
    $sql = "CREATE TABLE IF NOT EXISTS productadmin (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    category VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    price DECIMAL(5,2) NOT NULL,
    stock INT(5) NOT NULL,
    details TEXT NOT NULL,
    cat_ID INT(6)
    )";
    $conn->exec($sql);
    echo "Table productadmin created successfully.<br>";

    //next sql again
    $sql = "CREATE TABLE IF NOT EXISTS paypal_payment (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date_created DATE NOT NULL,
    product_ID INT(10) NOT NULL,
    product_price DOUBLE NOT NULL,
    quantity INT(11) NOT NULL,
    total DOUBLE NOT NULL,
    uid INT(10) NOT NULL,
    email VARCHAR(255) NOT NULL,
    ship_addr VARCHAR(255) NOT NULL
    )";
    $conn->exec($sql);
    echo "Table paypal_payment created successfully.<br>";

    //next sql again
    $sql = "CREATE TABLE IF NOT EXISTS user (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    First_Name VARCHAR(255) NOT NULL,
    Last_Name VARCHAR(255) NOT NULL,
    Gender VARCHAR(6) NOT NULL,
    Date_Joined date NOT NULL,
    Bio VARCHAR(100) NOT NULL,
    Phone_Number VARCHAR(255) NOT NULL,
    Address VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    Pfp VARCHAR(255) NOT NULL,
    Password VARCHAR(255) NOT NULL

    )";
    $conn->exec($sql);
    echo "Table user created successfully.<br>";
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>

