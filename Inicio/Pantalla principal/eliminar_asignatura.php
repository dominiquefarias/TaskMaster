<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Inicio de sesion/login.php");
    exit;
}

require_once '../Inicio de sesion/conexion.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $asignatura_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM asignaturas WHERE id = ? AND usuario_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $asignatura_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Redirigir de vuelta a la página principal siempre
header("Location: pagina_principal.php");
exit;
?>
