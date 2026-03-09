<?php
session_start();
if (!($_SESSION['user_id'] ?? null)) {
    header("Location: ../Inicio de sesion/login.php");
    exit;
}
require_once '../Inicio de sesion/conexion.php';
$user_id = $_SESSION['user_id'];
$message = "";

// Obtener asignaturas para el select
$stmt = $conn->prepare("SELECT id, nombre FROM asignaturas WHERE usuario_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$asignaturas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$tarea = null;

// Si es GET con ID, cargamos la tarea
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tarea_id = intval($_GET['id']);

    // Verificamos propiedad haciendo JOIN con asignaturas
    $sql_get = "SELECT t.* FROM tareas t 
                JOIN asignaturas a ON t.asignatura_id = a.id 
                WHERE t.id = ? AND a.usuario_id = ?";
    $stmt = $conn->prepare($sql_get);
    $stmt->bind_param("ii", $tarea_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $tarea = $result->fetch_assoc();
    }
    else {
        header("Location: pagina_principal.php");
        exit;
    }
    $stmt->close();
}
elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Si es POST, procesamos la actualización
    $t_id = $_POST['id'] ?? null;
    $n = $_POST['nombre'] ?? '';
    $a_id = $_POST['asignatura_id'] ?? null;
    $f = $_POST['fecha_limite'] ?? '';
    $desc = $_POST['descripcion'] ?? '';
    $prio = $_POST['prioridad'] ?? 'baja';

    // Verificamos primero que la tarea le pertenece
    $sql_check = "SELECT t.id FROM tareas t JOIN asignaturas a ON t.asignatura_id = a.id WHERE t.id = ? AND a.usuario_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $t_id, $user_id);
    $stmt_check->execute();
    $check_res = $stmt_check->get_result();
    $stmt_check->close();

    if ($check_res->num_rows === 1 && $n && $a_id && $f) {
        $sql = "UPDATE tareas SET asignatura_id = ?, nombre = ?, descripcion = ?, fecha_limite = ?, prioridad = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("issssi", $a_id, $n, $desc, $f, $prio, $t_id);

        if ($stmt->execute()) {
            header("Location: pagina_principal.php");
            exit;
        }
        else {
            $message = "Error al actualizar";
            // Recargamos datos de la tarea para volver a mostrar el formulario en caso de error
            $tarea = [
                'id' => $t_id,
                'nombre' => $n,
                'asignatura_id' => $a_id,
                'fecha_limite' => $f,
                'descripcion' => $desc,
                'prioridad' => $prio
            ];
        }
        $stmt->close();
    }
    else {
        $message = ($check_res->num_rows !== 1) ? "No tienes permiso para editar esta tarea." : "Completa los campos obligatorios";
        // Recargamos datos para el formulario si faltan campos
        $tarea = [
            'id' => $t_id ?? '',
            'nombre' => $n,
            'asignatura_id' => $a_id,
            'fecha_limite' => $f,
            'descripcion' => $desc,
            'prioridad' => $prio
        ];
    }
}
else {
    // Si no hay ID en GET ni POST
    header("Location: pagina_principal.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea - Get it done</title>
    <link rel="stylesheet" href="../css/añadir_tareas.css">
    <link rel="stylesheet" href="../css/editar_tarea.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png?v=<?php echo time(); ?>">
</head>

<body>

    <div class="add-task-container">
        <div class="add-task-header">
            <h2>Editar Tarea</h2>
        </div>

        <?php if ($message): ?>
        <p class="message-error">
            <?php echo htmlspecialchars($message); ?>
        </p>
        <?php
endif; ?>

        <form action="editar_tarea.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($tarea['id']); ?>">

            <!-- Nombre de la tarea -->
            <div class="form-group">
                <label class="form-label">¿Qué tienes pendiente?</label>
                <input type="text" name="nombre" class="form-control"
                    placeholder="Ej: Estudiar para el examen de cálculo"
                    value="<?php echo htmlspecialchars($tarea['nombre']); ?>" required>
            </div>

            <div class="form-row">
                <!-- Asignatura (Dropdown) -->
                <div class="form-col-half">
                    <label class="form-label">Asignatura</label>
                    <select name="asignatura_id" class="form-control" required>
                        <option value="" disabled>Selecciona una...</option>
                        <?php foreach ($asignaturas as $asig): ?>
                        <option value="<?php echo $asig['id']; ?>" <?php echo ($tarea['asignatura_id']==$asig['id'])
                            ? 'selected' : '' ; ?>>
                            <?php echo htmlspecialchars($asig['nombre']); ?>
                        </option>
                        <?php
endforeach; ?>
                    </select>
                </div>

                <!-- Fecha y Hora -->
                <div class="form-col-half">
                    <label class="form-label">¿Para cuándo?</label>
                    <?php
// Formatear la fecha para input type="datetime-local"  (YYYY-MM-DDThh:mm)
$fecha_formato = date('Y-m-d\TH:i', strtotime($tarea['fecha_limite']));
?>
                    <input type="datetime-local" name="fecha_limite" class="form-control"
                        value="<?php echo $fecha_formato; ?>" required>
                </div>
            </div>

            <!-- Descripción / Notas -->
            <div class="form-group">
                <label class="form-label">Comentarios</label>
                <textarea name="descripcion" class="form-control"
                    placeholder="Comentarios..."><?php echo htmlspecialchars($tarea['descripcion']); ?></textarea>
            </div>

            <!-- Prioridad -->
            <div class="form-group">
                <label class="form-label">Nivel de relevancia</label>
                <div class="urgency-options">

                    <label>
                        <input type="radio" name="prioridad" value="alta" <?php echo ($tarea['prioridad']==='alta' )
                            ? 'checked' : '' ; ?>>
                        <div class="urgency-btn btn-high">Alta</div>
                    </label>

                    <label>
                        <input type="radio" name="prioridad" value="media" <?php echo ($tarea['prioridad']==='media' )
                            ? 'checked' : '' ; ?>>
                        <div class="urgency-btn btn-med">Media</div>
                    </label>

                    <label>
                        <input type="radio" name="prioridad" value="baja" <?php echo ($tarea['prioridad']==='baja' )
                            ? 'checked' : '' ; ?>>
                        <div class="urgency-btn btn-low">Baja</div>
                    </label>

                </div>
            </div>

            <!-- Botón para guardar -->
            <button type="submit" class="btn-save">
                Actualizar Tarea
            </button>

            <!-- retroceso -->
            <div class="cancel-action-container">
                <a href="pagina_principal.php" class="cancel-action-link">Cancelar</a>
            </div>

        </form>
    </div>

</body>

</html>