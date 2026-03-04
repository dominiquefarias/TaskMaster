<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: Inicio%20de%20sesion/login.php");
    exit;
}
else {
    // Si ya inició sesión, redirigir al Dashboard
    header("Location: Pantalla%20principal/pagina_principal.php");
    exit;
}
?>