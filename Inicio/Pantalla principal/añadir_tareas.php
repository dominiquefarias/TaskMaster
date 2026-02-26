<?php
session_start();
if (!($_SESSION['user_id'] ?? null)) {
    header("Location: ../Inicio de sesion/login.php");
    exit;
}
require_once '../Inicio de sesion/conexion.php';
$user_id = $_SESSION['user_id'];
$message = "";

$stmt = $conn->prepare("SELECT id, nombre FROM asignaturas WHERE usuario_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$asignaturas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $n = $_POST['nombre'] ?? '';
    $a_id = $_POST['asignatura_id'] ?? null;
    $f = $_POST['fecha_limite'] ?? '';
    $desc = $_POST['descripcion'] ?? '';
    $prio = $_POST['prioridad'] ?? 'baja';

    if ($n && $a_id && $f) {
        $sql = "INSERT INTO tareas (asignatura_id, nombre, descripcion, fecha_limite, prioridad) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Usamos bind_param para máxima compatibilidad
        $stmt->bind_param("issss", $a_id, $n, $desc, $f, $prio);

        if ($stmt->execute()) {
            header("Location: pagina_principal.php");
            exit;
        }
        else {
            $message = "Error al guardar";
        }
        $stmt->close();
    }
    else {
        $message = "Completa los campos obligatorios";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nueva Tarea - TaskMaster</title>
    <link rel="stylesheet" href="../css/añadir_tareas.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
</head>

<body>

    <div class="add-task-container">
        <div class="add-task-header">
            <h2>Añadir Nueva Tarea</h2>
        </div>

        <?php if ($message): ?>
        <p style="color: red; text-align: center;">
            <?php echo htmlspecialchars($message); ?>
        </p>
        <?php
endif; ?>

        <form action="añadir_tareas.php" method="POST">

            <!-- Nombre de la tarea -->
            <div class="form-group">
                <label class="form-label">¿Qué tienes pendiente?</label>
                <input type="text" name="nombre" class="form-control"
                    placeholder="Ej: Estudiar para el examen de cálculo" required>
            </div>

            <div class="form-row">
                <!-- Asignatura (Dropdown) -->
                <div class="form-col-half">
                    <label class="form-label">Asignatura</label>
                    <select name="asignatura_id" class="form-control" required>
                        <option value="" disabled selected>Selecciona una...</option>
                        <?php foreach ($asignaturas as $asig): ?>
                        <option value="<?php echo $asig['id']; ?>">
                            <?php echo htmlspecialchars($asig['nombre']); ?>
                        </option>
                        <?php
endforeach; ?>
                    </select>
                </div>

                <!-- Fecha y Hora -->
                <div class="form-col-half">
                    <label class="form-label">¿Para cuándo?</label>
                    <input type="datetime-local" name="fecha_limite" class="form-control" required>
                </div>
            </div>

            <!-- Descripción / Notas -->
            <div class="form-group">
                <label class="form-label">Comentarios</label>
                <textarea name="descripcion" class="form-control" placeholder="Comentarios..."></textarea>
            </div>

            <!-- Prioridad -->
            <div class="form-group">
                <label class="form-label">Nivel de relevancia</label>
                <div class="urgency-options">

                    <label>
                        <input type="radio" name="prioridad" value="alta">
                        <div class="urgency-btn btn-high">Alta</div>
                    </label>

                    <label>
                        <input type="radio" name="prioridad" value="media">
                        <div class="urgency-btn btn-med">Media</div>
                    </label>

                    <label>
                        <input type="radio" name="prioridad" value="baja" checked>
                        <div class="urgency-btn btn-low">Baja</div>
                    </label>

                </div>
            </div>

            <!-- Botón Guardar -->
            <button type="submit" class="btn-save">
                Guardar Tarea <i class="fas fa-sparkles"></i>
            </button>

            <!-- Back link just in case -->
            <div style="text-align: center; margin-top: 1rem;">
                <a href="pagina_principal.php"
                    style="color: #666; text-decoration: none; font-size: 0.9rem;">Cancelar</a>
            </div>

        </form>
    </div>

</body>

</html>