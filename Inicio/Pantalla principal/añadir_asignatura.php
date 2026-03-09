<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../idiomas.php';

// Verificar sesión de si ha 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Inicio%20de%20sesion/login.php");
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
            $message = $idioma[$_SESSION['idioma']]['subject_exists'];
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
                    $message = $idioma[$_SESSION['idioma']]['error_saving_subject'];
                }
                $stmt->close();
            }
        }
        $check->close();
    }
    else {
        $message = $idioma[$_SESSION['idioma']]['please_write_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $idioma[$_SESSION['idioma']]['add_subject_title']; ?> - Get it done
    </title>
    <link rel="stylesheet" href="../css/añadir_asignatura.css">
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
                <?php echo $idioma[$_SESSION['idioma']]['add_subject_title']; ?>
            </h2>
        </div>

        <?php if ($message): ?>
        <p class="error-msg">
            <?php echo htmlspecialchars($message); ?>
        </p>
        <?php
endif; ?>

        <form action="añadir_asignatura.php" method="POST">

            <div class="form-group">
                <label class="form-label">
                    <?php echo $idioma[$_SESSION['idioma']]['subject_name_label']; ?>
                </label>
                <input type="text" name="nombre" class="form-control"
                    placeholder="<?php echo $idioma[$_SESSION['idioma']]['subject_name_placeholder']; ?>" required
                    autofocus>
            </div>

            <!-- Botón para Guardar la asignatura -->
            <button type="submit" class="btn-save">
                <?php echo $idioma[$_SESSION['idioma']]['btn_save']; ?> <i class="fas fa-check"></i>
            </button>

            <!-- Link para volver hacia atras -->
            <div class="back-link-wrapper">
                <a href="pagina_principal.php" class="back-link">
                    <?php echo $idioma[$_SESSION['idioma']]['back_home']; ?>
                </a>
            </div>

        </form>
    </div>

</body>

</html>