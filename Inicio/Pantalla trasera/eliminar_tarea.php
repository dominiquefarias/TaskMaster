<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Inicio%20de%20sesion/login.php");
    exit;
}

require_once '../Inicio de sesion/conexion.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tarea_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Eliminamos la tarea usando JOIN con asignaturas para validar que pertenece al usuario de la sesión actual
    $sql = "DELETE t FROM tareas t 
            JOIN asignaturas a ON t.asignatura_id = a.id 
            WHERE t.id = ? AND a.usuario_id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ii", $tarea_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: ../Pantalla%20principal/pagina_principal.php");
exit;
?>