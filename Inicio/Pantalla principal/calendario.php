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
// Default to current month/year if not provided in URL
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Ensure valid month/year
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

    <!-- LEFT SIDEBAR -->
    <aside class="sidebar-left">
        <div class="brand">
            <div class="brand-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="brand-name">Academia</div>
        </div>

        <nav class="nav-menu">
            <a href="calendario.php" class="nav-item active">
                <i class="far fa-calendar-alt"></i> Calendar
            </a>
            <a href="pagina_principal.php" class="nav-item">
                <i class="fas fa-tasks"></i> Assignments
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-book"></i> Courses
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-chart-line"></i> Performance
            </a>
        </nav>

        <a href="añadir_tareas.php" class="quick-add-btn">
            <i class="fas fa-plus-circle"></i> Quick Add
        </a>

        <div class="user-profile">
            <div class="user-avatar">
                <?php echo $user_initials; ?>
            </div>
            <div class="user-info">
                <h4 class="user-name">
                    <?php echo htmlspecialchars($user_name); ?>
                </h4>
                <p class="user-role">Student</p>
            </div>
            <a href="../Inicio de sesion/logout.php" class="user-settings" title="Log Out"><i
                    class="fas fa-cog"></i></a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="top-header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search events, courses, or deadlines...">
            </div>
            <div class="header-right">
                <div class="notification-bell">
                    <i class="far fa-bell"></i>
                    <div class="notification-dot"></div>
                </div>
                <div class="current-date-display">
                    <i class="far fa-calendar"></i>
                    <?php echo "$monthName $year"; ?>
                </div>
            </div>
        </header>

        <div class="calendar-header">
            <div class="calendar-title">
                <h1>Academic Calendar</h1>
                <p>Stay ahead of your
                    <?php echo count($tareas); ?> upcoming deadlines.
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
// Offset for beginning of the month
$totalCells = 35; // 5 weeks (can be adjusted to 42 if needed)
if ($dayOfWeek + $daysInMonth > 35) {
    $totalCells = 42; // Need 6 weeks
}

$currentDay = 1;

for ($i = 0; $i < $totalCells; $i++) {
    if ($i < $dayOfWeek || $currentDay > $daysInMonth) {
        // Empty cell (previous/next month)
        echo '<div class="calendar-cell other-month"><div class="cell-date"></div></div>';
    }
    else {
        // Current month cell
        $currentDateStr = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
        $isToday = ($currentDateStr === date('Y-m-d')) ? 'today' : '';

        echo "<div class='calendar-cell $isToday'>";
        echo "<div class='cell-date'>$currentDay</div>";

        // Render tasks for this day
        if (isset($tasksByDate[$currentDateStr])) {
            foreach ($tasksByDate[$currentDateStr] as $tarea_del_dia) {
                // Simple logic to color code based on keyword or random, since we don't have explicit type
                $nombre_tarea = htmlspecialchars($tarea_del_dia['nombre']);

                $typeClass = 'type-class'; // Default purple
                if (stripos($nombre_tarea, 'examen') !== false || stripos($nombre_tarea, 'exam') !== false || stripos($nombre_tarea, 'midterm') !== false) {
                    $typeClass = 'type-exam'; // Red
                }
                elseif (stripos($nombre_tarea, 'entrega') !== false || stripos($nombre_tarea, 'proyecto') !== false || stripos($nombre_tarea, 'submission') !== false || stripos($nombre_tarea, 'proposal') !== false) {
                    $typeClass = 'type-deadline'; // Orange
                }

                echo "<a href='editar_tarea.php?id={$tarea_del_dia['id']}' class='task-block $typeClass' title='{$nombre_tarea}\nAsignatura: {$tarea_del_dia['asignatura_nombre']}'>";
                echo "$nombre_tarea";
                echo "</a>";
            }
        }

        // Add action layer on hover (Optional, enables direct delete/edit)
        echo "<div class='calendar-cell-actions'>
                                <button class='cell-action-btn' title='Add task on this day'><i class='fas fa-plus'></i></button>
                              </div>";

        echo "</div>";
        $currentDay++;
    }
}
?>
            </div>

            <!-- Calendar Navigation footer -->
            <div
                style="padding: 12px; display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); background: var(--bg-panel);">
                <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>"
                    style="color: var(--text-secondary); text-decoration: none; font-size: 13px;"><i
                        class="fas fa-chevron-left"></i> Previous Month</a>
                <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>"
                    style="color: var(--text-secondary); text-decoration: none; font-size: 13px;">Next Month <i
                        class="fas fa-chevron-right"></i></a>
            </div>
        </div>
    </main>

    <!-- RIGHT SIDEBAR -->
    <aside class="sidebar-right">
        <h2 class="section-title"><i class="far fa-calendar-check"></i> Today's Schedule</h2>
        <div class="schedule-list">
            <?php
$todayStr = date('Y-m-d');
$hasTasksToday = false;

if (isset($tasksByDate[$todayStr])) {
    $hasTasksToday = true;
    foreach ($tasksByDate[$todayStr] as $tarea_hoy) {
        $nombre_tarea = htmlspecialchars($tarea_hoy['nombre']);
        $asig_nombre = htmlspecialchars($tarea_hoy['asignatura_nombre']);

        // Same type logic
        $typeClass = 'class';
        $typeLabel = 'CLASS';
        if (stripos($nombre_tarea, 'examen') !== false || stripos($nombre_tarea, 'exam') !== false || stripos($nombre_tarea, 'midterm') !== false) {
            $typeClass = 'exam';
            $typeLabel = 'EXAM';
        }
        elseif (stripos($nombre_tarea, 'entrega') !== false || stripos($nombre_tarea, 'proyecto') !== false || stripos($nombre_tarea, 'submission') !== false) {
            $typeClass = 'deadline';
            $typeLabel = 'DEADLINE';
        }

        // Format time if available, otherwise just use end of day
        $timeFormatted = "23:59";
        if (strlen($tarea_hoy['fecha_limite']) > 10) {
            $timeFormatted = date('H:i', strtotime($tarea_hoy['fecha_limite']));
        }

        echo "<a href='editar_tarea.php?id={$tarea_hoy['id']}' class='schedule-card $typeClass'>
                            <div class='card-header'>
                                <span class='card-type'>$typeLabel</span>
                                <span class='card-time'>$timeFormatted</span>
                            </div>
                            <h4 class='card-title'>$nombre_tarea</h4>
                            <div class='card-meta'>
                                <i class='fas fa-map-marker-alt'></i> $asig_nombre
                            </div>
                          </a>";
    }
}

if (!$hasTasksToday) {
    echo "<p style='color: var(--text-secondary); font-size: 13px;'>No tasks scheduled for today. Enjoy your day!</p>";
}
?>
        </div>

        <h2 class="section-title"><i class="fas fa-stopwatch"></i> Focus Timer</h2>
        <div class="focus-timer">
            <p class="timer-subtitle">Current Session: Study</p>
            <h3 class="timer-time" id="pomodoro-display">25:00</h3>
            <button class="btn-pomodoro" id="pomodoro-btn">Start Pomodoro</button>
        </div>

        <h2 class="section-title"><i class="fas fa-tasks"></i> Upcoming Deadlines</h2>
        <div class="deadline-list">
            <?php
$upcomingCount = 0;
$now = time();
foreach ($tareas as $tarea) {
    if ($upcomingCount >= 4)
        break; // Show only next 4

    $tareaTime = strtotime($tarea['fecha_limite']);
    if ($tareaTime >= $now) {
        $daysRemaining = floor(($tareaTime - $now) / (60 * 60 * 24));

        $dotColor = 'purple';
        if ($daysRemaining < 2)
            $dotColor = 'red';
        else if ($daysRemaining > 5)
            $dotColor = 'green';

        $name = htmlspecialchars($tarea['nombre']);
        if (strlen($name) > 20) {
            $name = substr($name, 0, 18) . '...';
        }

        echo "<div class='deadline-item'>
                            <div class='deadline-info'>
                                <div class='dot $dotColor'></div>
                                <span class='deadline-title'>$name</span>
                            </div>
                            <span class='deadline-time'>{$daysRemaining}d</span>
                          </div>";
        $upcomingCount++;
    }
}
if ($upcomingCount === 0) {
    echo "<p style='color: var(--text-secondary); font-size: 13px;'>No upcoming tasks.</p>";
}
?>
        </div>
    </aside>

    <script>
        // Simple Pomodoro Timer Logic
        const pomodoroBtn = document.getElementById('pomodoro-btn');
        const pomodoroDisplay = document.getElementById('pomodoro-display');
        let timerInterval;
        let timeLeft = 25 * 60; // 25 minutes
        let isRunning = false;

        function updateDisplay() {
            let m = Math.floor(timeLeft / 60);
            let s = timeLeft % 60;
            pomodoroDisplay.textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }

        pomodoroBtn.addEventListener('click', () => {
            if (isRunning) {
                clearInterval(timerInterval);
                pomodoroBtn.textContent = 'Resume Pomodoro';
                isRunning = false;
            } else {
                isRunning = true;
                pomodoroBtn.textContent = 'Pause Pomodoro';pagina_principa
                timerInterval = setInterval(() => {
                    timeLeft--;
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        pomodoroBtn.textContent = 'Start Pomodoro';
                        isRunning = false;
                        timeLeft = 25 * 60;
                        alert("Pomodoro session complete! Take a break.");
                    }
                    updateDisplay();
                },        });
    </script>
</body>

</html>