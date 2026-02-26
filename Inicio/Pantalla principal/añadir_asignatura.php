<?php
session_start();

// Verificar sesión de si ha 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Inicio de sesion/login.php");
    exit;
}

require_once '../Inicio de sesion/conexion.php';

$user_id = $_SESSION['user_id'];
$message = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'] ?? '';

    if (!empty($nombre)) {
        // Verificar si ya existe
        $check = $conn->prepare("SELECT id FROM asignaturas WHERE nombre = ? AND usuario_id = ?");
        $check->bind_param("si", $nombre, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Esta asignatura ya existe.";
        }
        else {
            $stmt = $conn->prepare("INSERT INTO asignaturas (usuario_id, nombre) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param("is", $user_id, $nombre);
                if ($stmt->execute()) {
                    header("Location: pagina_principal.php");
                    exit;
                }
                else {
                    $message = "Error al guardar la asignatura.";
                }
                $stmt->close();
            }
        }
        $check->close();
    }
    else {
        $message = "Por favor, escribe un nombre.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Asignatura - TaskMaster</title>
    <link rel="stylesheet" href="../css/añadir_asignatura.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
</head>

<body>

    <div class="add-task-container" style="width: 400px; padding: 2rem;">
        <div class="add-task-header">
            <h2>Nueva Asignatura</h2>
        </div>

        <?php if ($message): ?>
        <p style="color: #FF6B6B; text-align: center; font-weight: bold;">
            <?php echo htmlspecialchars($message); ?>
        </p>
        <?php
endif; ?>

        <form action="añadir_asignatura.php" method="POST">

            <div class="form-group">
                <label class="form-label">Nombre de la asignatura</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej: Matemáticas" required autofocus>
            </div>

            <!-- Botón para Guardar la asignatura -->
            <button type="submit" class="btn-save">
                Guardar <i class="fas fa-check"></i>
            </button>

            <!-- Link para volver hacia atras -->
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="pagina_principal.php" style="color: #666; text-decoration: none; font-size: 0.9rem;"> Volver al
                    inicio</a>
            </div>

        </form>
    </div>

</body>

</html>