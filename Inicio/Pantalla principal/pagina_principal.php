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
    <title>TaskMaster - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cousine&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/pagina principal.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-icon menu-icon">
            <i class="fas fa-bars" style="color: #000;"></i>
        </div>

        <div class="sidebar-icon">
            <i class="fas fa-home"></i>
        </div>
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
                    <a href="#">
                        <?php echo htmlspecialchars($asig['nombre']); ?>
                        <i class="fas fa-trash-alt" style="float:right"></i>
                    </a>
                    <?php
    endforeach; ?>
                    <?php
else: ?>
                    <a href="#">Sin asignaturas</a>
                    <?php
endif; ?>

                    <div
                        style="padding: 10px; display: flex; justify-content: center; border-top: 1px solid rgba(255,255,255,0.1);">
                        <a href="añadir_asignatura.php" class="add-subject-btn" title="Añadir Asignatura">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="header-actions">
                <a href="añadir_tareas.php" class="btn-icon" style="text-decoration: none;">
                    <i class="fas fa-plus-circle"
                        style="color: #9370DB; font-size: 3rem; background: white; border-radius: 50%;"></i>
                </a>
                <a href="../Inicio de sesion/logout.php" class="btn-icon">
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
                    <small style="color: #666; margin-top:5px; display:block;">
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
                        <i class="fas fa-pen"></i>
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </div>

            <?php
    endforeach; ?>
            <?php
else: ?>
            <div style="text-align: center; color: #555; margin-top: 2rem;">
                <h3>No tienes tareas pendientes</h3>
                <p>Usa el botón + para añadir una nueva</p>
            </div>
            <?php
endif; ?>

        </div>

    </main>
</body>

</html>