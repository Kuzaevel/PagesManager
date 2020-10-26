<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "pages_manager";


    try {
        $conn = new PDO("mysql:host=$servername;dbname=".$database.";charset=utf8", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET CHARACTER SET utf8;");

    }
    catch(PDOException $e)
    {
        die( $e->getMessage() ) ;
    }
