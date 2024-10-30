<?php
$host = 'localhost'; // or your database host
$db = 'demo-final'; // your database name
$user = 'root'; // your database username
$pass = ''; // your database password

try {
    $con = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>