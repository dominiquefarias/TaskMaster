<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../idiomas.php';
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
        header("Location: ../Pantalla principal/pagina_principal.php");
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
            header("Location: ../Pantalla principal/pagina_principal.php");
            exit;
        }
        else {
            $message = $idioma[$_SESSION['idioma']]['error_updating'];
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
        $message = ($check_res->num_rows !== 1) ? $idioma[$_SESSION['idioma']]['no_permission'] : $idioma[$_SESSION['idioma']]['error_fill_fields'];
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
    header("Location: ../Pantalla principal/pagina_principal.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $idioma[$_SESSION['idioma']]['edit_task_title']; ?> - Get it done
    </title>
    <link rel="stylesheet" href="../css/añadir_tareas.css">
    <link rel="stylesheet" href="../css/editar_tarea.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png?v=<?php echo time(); ?>">
</head>

<body>
    <div class="lang-selector-wrapper">
        <select id="lang_selector">
            <option value="es" <?php if ($_SESSION['idioma']=='es' )
    echo 'selected' ; ?>>🇪🇸 </option>
            <option value="en" <?php if ($_SESSION['idioma']=='en' )
    echo 'selected' ; ?>>🇬🇧 </option>
        </select>
    </div>
    <script>
        document.getElementById('lang_selector').onchange = function () {
            window.location.href = '?idioma=' + this.value;
        };
    </script>
    <div class="add-task-container">
        <div class="add-task-header">
            <h2>
                <?php echo $idioma[$_SESSION['idioma']]['edit_task_title']; ?>
            </h2>
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
                <label class="form-label">
                    <?php echo $idioma[$_SESSION['idioma']]['task_name_label']; ?>
                </label>
                <input type="text" name="nombre" class="form-control"
                    placeholder="<?php echo $idioma[$_SESSION['idioma']]['task_name_placeholder']; ?>"
                    value="<?php echo htmlspecialchars($tarea['nombre']); ?>" required>
            </div>

            <div class="form-row">
                <!-- Asignatura (Dropdown) -->
                <div class="form-col-half">
                    <label class="form-label">
                        <?php echo $idioma[$_SESSION['idioma']]['subject_label']; ?>
                    </label>
                    <select name="asignatura_id" class="form-control" required>
                        <option value="" disabled>
                            <?php echo $idioma[$_SESSION['idioma']]['select_one']; ?>
                        </option>
                        <?php foreach ($asignaturas as $asig): ?>
                        <option value="<?php echo $asig['id']; ?>" <?php echo ($tarea['asignatura_id'] == $asig['id'])
        ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($asig['nombre']); ?>
                        </option>
                        <?php
endforeach; ?>
                    </select>
                </div>

                <!-- Fecha y Hora -->
                <div class="form-col-half">
                    <label class="form-label">
                        <?php echo $idioma[$_SESSION['idioma']]['due_date_label']; ?>
                    </label>
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
                <label class="form-label">
                    <?php echo $idioma[$_SESSION['idioma']]['comments_label']; ?>
                </label>
                <textarea name="descripcion" class="form-control"
                    placeholder="<?php echo $idioma[$_SESSION['idioma']]['comments_placeholder']; ?>"><?php echo htmlspecialchars($tarea['descripcion']); ?></textarea>
            </div>

            <!-- Prioridad -->
            <div class="form-group">
                <label class="form-label">
                    <?php echo $idioma[$_SESSION['idioma']]['relevance_level']; ?>
                </label>
                <div class="urgency-options">

                    <label>
                        <input type="radio" name="prioridad" value="alta" <?php echo ($tarea['prioridad'] === 'alta')
    ? 'checked' : ''; ?>>
                        <div class="urgency-btn btn-high">
                            <?php echo $idioma[$_SESSION['idioma']]['high']; ?>
                        </div>
                    </label>

                    <label>
                        <input type="radio" name="prioridad" value="media" <?php echo ($tarea['prioridad'] === 'media')
    ? 'checked' : ''; ?>>
                        <div class="urgency-btn btn-med">
                            <?php echo $idioma[$_SESSION['idioma']]['medium']; ?>
                        </div>
                    </label>

                    <label>
                        <input type="radio" name="prioridad" value="baja" <?php echo ($tarea['prioridad'] === 'baja')
    ? 'checked' : ''; ?>>
                        <div class="urgency-btn btn-low">
                            <?php echo $idioma[$_SESSION['idioma']]['low']; ?>
                        </div>
                    </label>

                </div>
            </div>

            <!-- Botón para guardar -->
            <button type="submit" class="btn-save">
                <?php echo $idioma[$_SESSION['idioma']]['btn_update_task']; ?>
            </button>

            <!-- retroceso -->
            <div class="cancel-action-container">
                <a href="../Pantalla principal/pagina_principal.php" class="cancel-action-link">
                    <?php echo $idioma[$_SESSION['idioma']]['cancel']; ?>
                </a>
            </div>

        </form>
    </div>

</body>

</html>