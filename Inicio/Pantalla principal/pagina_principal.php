<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Inicio de sesion/login.php");
    exit;
}

require_once '../Inicio de sesion/conexion.php';

$user_id = $_SESSION['user_id'];

$asignaturas = [];
$stmt = $conn->prepare("SELECT * FROM asignaturas WHERE usuario_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $asignaturas[] = $row;
    }
    $stmt->close();
}

$tareas = [];
$stmt = $conn->prepare("
    SELECT t.*, a.nombre as asignatura_nombre 
    FROM tareas t 
    JOIN asignaturas a ON t.asignatura_id = a.id 
    WHERE a.usuario_id = ? 
    ORDER BY t.fecha_limite ASC
");

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tareas[] = $row;
    }
    $stmt->close();
}

function tiempoRestante($fecha_limite)
{
    $ahora = new DateTime();
    $limite = new DateTime($fecha_limite);
    $intervalo = $ahora->diff($limite);

    if ($ahora > $limite) {
        return ["00", "00", "00", "00"]; // Vencida
    }

    $dias = str_pad($intervalo->days, 2, "0", STR_PAD_LEFT);
    $horas = str_pad($intervalo->h, 2, "0", STR_PAD_LEFT);
    $minutos = str_pad($intervalo->i, 2, "0", STR_PAD_LEFT);
    $segundos = str_pad($intervalo->s, 2, "0", STR_PAD_LEFT);

    return [$dias, $horas, $minutos, $segundos];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get it done - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?familyi=Cousine&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/pagina principal.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../img/favicon.png?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=calendar_month" />
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-icon menu-icon">
            <i class="fas fa-bars menu-icon-bars"></i>
        </div>

        <a href="pagina_principal.php" class="sidebar-icon" title="Inicio"
            style="text-decoration: none; display: flex; justify-content: center;">
            <i class="fas fa-home"></i>
        </a>
        <a href="calendario.php" class="sidebar-icon" title="Calendario"
            style="text-decoration: none; display: flex; justify-content: center;">
            <i class="fa-solid fa-calendar"></i>
        </a>
        <div class="sidebar-icon">
            <i class="fas fa-user-circle"></i>
        </div>
    </aside>

    <main class="main-content">

        <header class="top-header">
            <div class="dropdown-container">
                <button class="dropdown-btn">
                    Asignaturas <i class="fas fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <?php if (count($asignaturas) > 0): ?>
                    <?php foreach ($asignaturas as $asig): ?>
                    <div class="subject-item-container">
                        <a href="#" class="subject-item-link">
                            <?php echo htmlspecialchars($asig['nombre']); ?>
                        </a>
                        <a href="../Pantalla%20trasera/eliminar_asignatura.php?id=<?php echo $asig['id']; ?>"
                            onclick="return confirm('¿Seguro que quieres eliminar esta asignatura?');"
                            class="subject-item-delete" title="Eliminar asignatura">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                    <?php
    endforeach; ?>
                    <?php
else: ?>
                    <a href="#">Sin asignaturas</a>
                    <?php
endif; ?>

                    <div class="add-subject-wrapper">
                        <a href="añadir_asignatura.php" class="add-subject-btn" title="Añadir Asignatura">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="header-actions">
                <a href="añadir_tareas.php" class="btn-icon btn-add-task-link">
                    <i class="fas fa-plus-circle btn-add-subject"></i>
                </a>
                <a href="../Inicio%20de%20sesion/logout.php" class="btn-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <div class="cards-container">

            <?php if (count($tareas) > 0): ?>
            <?php foreach ($tareas as $tarea): ?>
            <?php
        $tiempo = tiempoRestante($tarea['fecha_limite']);
?>
            <div class="card">
                <div class="card-info">
                    <h3>
                        <?php echo htmlspecialchars($tarea['nombre']); ?>
                    </h3>
                    <p>
                        <?php echo htmlspecialchars($tarea['descripcion']); ?>
                    </p>
                    <small class="task-subject-name">
                        <?php echo htmlspecialchars($tarea['asignatura_nombre']); ?>
                    </small>
                </div>
                <div class="clock-actions">
                    <div class="clock-badge">
                        <span class="clock-number">
                            <?php echo $tiempo[0]; ?>
                        </span>
                        <span class="clock-number">
                            <?php echo $tiempo[1]; ?>
                        </span>
                        <span class="clock-number">
                            <?php echo $tiempo[2]; ?>
                        </span>
                        <span class="clock-number">
                            <?php echo $tiempo[3]; ?>
                        </span>
                    </div>
                    <div class="action-icons">
                        <a href="../Pantalla%20trasera/editar_tarea.php?id=<?php echo $tarea['id']; ?>"
                            class="action-btn" title="Editar tarea" style="color: inherit; text-decoration: none;">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="../Pantalla%20trasera/eliminar_tarea.php?id=<?php echo $tarea['id']; ?>"
                            class="action-btn" title="Eliminar tarea" style="color: inherit; text-decoration: none;"
                            onclick="return confirm('¿Seguro que deseas eliminar esta tarea?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>

            <?php
    endforeach; ?>
            <?php
else: ?>
            <div class="no-tasks-msg">
                <h3>No tienes tareas pendientes</h3>
                <p>Usa el botón + para añadir una nueva asignatura</p>
            </div>
            <?php
endif; ?>

        </div>
    </main>

    <script>
        function actualizarContadores() {
            const tarjetas = document.querySelectorAll('.card');

            tarjetas.forEach(tarjeta => {
                const numeros = tarjeta.querySelectorAll('.clock-number');
                if (numeros.length === 4) {
                    let d = parseInt(numeros[0].textContent.trim()) || 0;
                    let h = parseInt(numeros[1].textContent.trim()) || 0;
                    let m = parseInt(numeros[2].textContent.trim()) || 0;
                    let s = parseInt(numeros[3].textContent.trim()) || 0;

                    let total = (d * 86400) + (h * 3600) + (m * 60) + s;

                    if (total > 0) {
                        total--;
                        numeros[0].textContent = Math.floor(total / 86400).toString().padStart(2, '0');
                        numeros[1].textContent = Math.floor((total % 86400) / 3600).toString().padStart(2, '0');
                        numeros[2].textContent = Math.floor((total % 3600) / 60).toString().padStart(2, '0');
                        numeros[3].textContent = (total % 60).toString().padStart(2, '0');
                    } else {
                        const badge = tarjeta.querySelector('.clock-badge');
                        if (badge) badge.style.borderColor = "#ff4d4d";
                    }
                }
            });
        }
        setInterval(actualizarContadores, 1000);
    </script>
</body>

</html>