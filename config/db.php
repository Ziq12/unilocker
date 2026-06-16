<?php
$host = '127.0.0.1';
$dbname = 'unilocker_v1';
$username = 'root';
$password = ''; // Default XAMPP MySQL password is empty

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>