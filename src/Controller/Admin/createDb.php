<?php


function createDb($dbname, $servername, $username, $password,)
{
    try {
        $sql = "CREATE DATABASE $dbname";


        $dbco = new PDO("mysql:host=$servername", $username, $password);

        $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $dbco->exec($sql);

        return true;
    } catch (PDOException $e) {
        return false;
    }
}

?>