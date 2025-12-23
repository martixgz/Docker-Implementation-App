<?php
$servername = "mysql-db"; // your MySQL container name
$username = "root";
$password = "rootpass"; // your MySQL root password
$dbname = "testdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
