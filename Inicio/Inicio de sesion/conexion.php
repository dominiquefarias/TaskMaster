<?php
// Conecto php con la base de datos
$servername = "localhost";
$username = "Dominique";
$password = "Dominique123$";
$dbname = "task_master";

// Crear conexión 
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer charset a utf8 para evitar problemas con caracteres especiales
$conn->set_charset("utf8");
?>