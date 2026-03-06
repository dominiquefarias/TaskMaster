<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Inicio de sesion/login.php");
    exit;
}
require_once '../Inicio de sesion/conexion.php';
$user_id = $_SESSION['user_id'];

// Check user data for avatar/name
$user_name = "Alex Johnson"; // Fallback
$user_stmt = $conn->prepare("SELECT u.nombre_usuario FROM usuarios u WHERE u.id = ?");
if ($user_stmt && $user_stmt->execute([$user_id])) {
    $user_result = $user_stmt->get_result();
    if ($u_row = $user_result->fetch_assoc()) {
        $user_name = trim($u_row['nombre_usuario']);
    }
    $user_stmt->close();
}
// Fetch subjects (asignaturas)
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
// Fetch tasks (tareas)
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
// --- Calendar Logic ---
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

if ($month < 1 || $month > 12) {
    $month = (int)date('n');
}
if ($year < 2000 || $year > 3000) {
    $year = (int)date('Y');
}

$firstDayOfMonth = "$year-$month-01";
$timestampFirstDay = strtotime($firstDayOfMonth);
$daysInMonth = date('t', $timestampFirstDay);
// 0 (Sun) to 6 (Sat)
$dayOfWeek = date('w', $timestampFirstDay);

$monthName = date('F', $timestampFirstDay);

// Navigation links
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth == 0) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth == 13) {
    $nextMonth = 1;
    $nextYear++;
}

// Group tasks by date string (YYYY-MM-DD) for rendering inside calendar grid
$tasksByDate = [];
foreach ($tareas as $tarea) {
    $dateOnly = date('Y-m-d', strtotime($tarea['fecha_limite']));
    if (!isset($tasksByDate[$dateOnly])) {
        $tasksByDate[$dateOnly] = [];
    }
    $tasksByDate[$dateOnly][] = $tarea;
}

function getInitials($name)
{
    $parts = explode(' ', trim($name));
    $initials = '';
    $i = 0;
    foreach ($parts as $part) {
        if (strlen($part) > 0 && $i < 2) {
            $initials .= strtoupper($part[0]);
            $i++;
        }
    }
    return $initials ?: 'U';
}

$user_initials = getInitials($user_name);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Calendar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cousine&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/calendario.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../img/favicon.png?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <div class="calendar-header">
            <div class="calendar-title">
                <h1>Calendario academico</h1>
                <p>Mantente un paso adelante de tus
                    <?php echo count($tareas); ?> entregas
                </p>
            </div>
            <div class="calendar-view-toggles">
                <button class="view-toggle active">Month</button>
                <button class="view-toggle">Week</button>
                <button class="view-toggle">Day</button>
            </div>
        </div>

        <!-- CALENDAR GRID -->
        <div class="calendar-container">
            <div class="calendar-days-header">
                <div class="day-name">SUN</div>
                <div class="day-name">MON</div>
                <div class="day-name">TUE</div>
                <div class="day-name">WED</div>
                <div class="day-name">THU</div>
                <div class="day-name">FRI</div>
                <div class="day-name">SAT</div>
            </div>

            <div class="calendar-grid">
                <?php
for ($dia = 1; $dia < 31; $dia++) {
    echo "<div class='calendar-cell'>";
    echo "<div class='cell-date'>" . $dia . "</div>";
    // Si quieres mantener la lógica de las tareas para el día actual:
    $currentDateStr = sprintf('%04d-%02d-%02d', $year, $month, $dia);
    if (isset($tasksByDate[$currentDateStr])) {
        foreach ($tasksByDate[$currentDateStr] as $tarea_del_dia) {
            $nombre_tarea = htmlspecialchars($tarea_del_dia['nombre']);

            $typeClass = 'type-class';
            if (stripos($nombre_tarea, 'examen') !== false || stripos($nombre_tarea, 'exam') !== false || stripos($nombre_tarea, 'midterm') !== false) {
                $typeClass = 'type-exam';
            }
            elseif (stripos($nombre_tarea, 'entrega') !== false || stripos($nombre_tarea, 'proyecto') !== false || stripos($nombre_tarea, 'submission') !== false || stripos($nombre_tarea, 'proposal') !== false) {
                $typeClass = 'type-deadline';
            }

            echo "<a href='editar_tarea.php?id={$tarea_del_dia['id']}' class='task-block $typeClass' title='{$nombre_tarea}\nAsignatura: {$tarea_del_dia['asignatura_nombre']}'>";
            echo "$nombre_tarea";
            echo "</a>";
        }
    }

    echo "<div class='calendar-cell-actions'>
            <button type='button' class='cell-action-btn' title='Add task on this day' onclick=\"window.location.href='añadir_tareas.php'\"><i class='fas fa-plus'></i></button>
          </div>";

    echo "</div>";
}
?>
            </div>

            <!-- Calendar Navigation footer -->
            <div class="calendar-nav-footer">
                <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="calendar-nav-link"><i
                        class="fas fa-chevron-left"></i> Previous Month</a>
                <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="calendar-nav-link">Next
                    Month <i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
    </main>


</body>

</html>