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

        $stmt->bind_param("issss", $a_id, $n, $desc, $f, $prio);

        if ($stmt->execute()) {
            header("Location: pagina_principal.php");
            exit;
        }
        else {
            $message = $idioma[$_SESSION['idioma']]['error_saving'];
        }
        $stmt->close();
    }
    else {
        $message = $idioma[$_SESSION['idioma']]['error_fill_fields'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $idioma[$_SESSION['idioma']]['add_task_title']; ?> - Get it done
    </title>
    <link rel="stylesheet" href="../css/añadir_tareas.css">
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
                <?php echo $idioma[$_SESSION['idioma']]['add_task_title']; ?>
            </h2>
        </div>

        <?php if ($message): ?>
        <p class="error-message">
            <?php echo htmlspecialchars($message); ?>
        </p>
        <?php
endif; ?>

        <form action="añadir_tareas.php" method="POST">

            <div class="form-group">
                <label class="form-label">
                    <?php echo $idioma[$_SESSION['idioma']]['task_name_label']; ?>
                </label>
                <input type="text" name="nombre" class="form-control"
                    placeholder="<?php echo $idioma[$_SESSION['idioma']]['task_name_placeholder']; ?>" required>
            </div>

            <div class="form-row">
                <!-- Asignatura (Dropdown) -->
                <div class="form-col-half">
                    <label class="form-label">
                        <?php echo $idioma[$_SESSION['idioma']]['subject_label']; ?>
                    </label>
                    <select name="asignatura_id" class="form-control" required>
                        <option value="" disabled selected>
                            <?php echo $idioma[$_SESSION['idioma']]['select_one']; ?>
                        </option>
                        <?php foreach ($asignaturas as $asig): ?>
                        <option value="<?php echo $asig['id']; ?>">
                            <?php echo htmlspecialchars($asig['nombre']); ?>
                        </option>
                        <?php
endforeach; ?>
                    </select>
                </div>

                <div class="form-col-half">
                    <label class="form-label">
                        <?php echo $idioma[$_SESSION['idioma']]['due_date_label']; ?>
                    </label>
                    <input type="datetime-local" name="fecha_limite" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <?php echo $idioma[$_SESSION['idioma']]['comments_label']; ?>
                </label>
                <textarea name="descripcion" class="form-control"
                    placeholder="<?php echo $idioma[$_SESSION['idioma']]['comments_placeholder']; ?>"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <?php echo $idioma[$_SESSION['idioma']]['relevance_level']; ?>
                </label>
                <div class="urgency-options">

                    <label>
                        <input type="radio" name="prioridad" value="alta">
                        <div class="urgency-btn btn-high">
                            <?php echo $idioma[$_SESSION['idioma']]['high']; ?>
                        </div>
                    </label>

                    <label>
                        <input type="radio" name="prioridad" value="media">
                        <div class="urgency-btn btn-med">
                            <?php echo $idioma[$_SESSION['idioma']]['medium']; ?>
                        </div>
                    </label>

                    <label>
                        <input type="radio" name="prioridad" value="baja" checked>
                        <div class="urgency-btn btn-low">
                            <?php echo $idioma[$_SESSION['idioma']]['low']; ?>
                        </div>
                    </label>

                </div>
            </div>

            <button type="submit" class="btn-save">
                <?php echo $idioma[$_SESSION['idioma']]['btn_save_task']; ?> <i class="fas fa-sparkles"></i>
            </button>

            <div class="cancel-container">
                <a href="pagina_principal.php" class="cancel-link">
                    <?php echo $idioma[$_SESSION['idioma']]['cancel']; ?>
                </a>
            </div>

        </form>
    </div>

</body>

</html>