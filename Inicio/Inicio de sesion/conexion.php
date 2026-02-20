<?php
$servername = "localhost";
$username = "Dominique";
$password = "Dominique123$";
$dbname = "task_master";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>