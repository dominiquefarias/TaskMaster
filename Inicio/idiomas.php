<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = 'es';
}

if (isset($_GET['idioma'])) {
    $_SESSION['idioma'] = $_GET['idioma'];
}

$idioma = [];

// login.php & base errors
$idioma['es']['login_title'] = "Iniciar sesion";
$idioma['es']['error_fill_fields'] = "Completa todos los campos";
$idioma['es']['error_wrong_password'] = "Contraseña incorrecta";
$idioma['es']['error_user_not_found'] = "Usuario o email no encontrados";
$idioma['es']['username_label'] = "Nombre de usuario";
$idioma['es']['email_label'] = "Email";
$idioma['es']['password_label'] = "Contraseña";
$idioma['es']['btn_login'] = "INICIAR SESION";

$idioma['en']['login_title'] = "Login";
$idioma['en']['error_fill_fields'] = "Fill in all fields";
$idioma['en']['error_wrong_password'] = "Incorrect password";
$idioma['en']['error_user_not_found'] = "User or email not found";
$idioma['en']['username_label'] = "Username";
$idioma['en']['email_label'] = "Email";
$idioma['en']['password_label'] = "Password";
$idioma['en']['btn_login'] = "LOGIN";

// registrarse.php
$idioma['es']['register_title'] = "Crear cuenta";
$idioma['es']['already_registered'] = "¿Ya estás registrado?";
$idioma['es']['already_registered_link'] = "Iniciar sesión";
$idioma['es']['btn_register'] = "REGISTRARSE";

$idioma['en']['register_title'] = "Create account";
$idioma['en']['already_registered'] = "Already registered?";
$idioma['en']['already_registered_link'] = "Login";
$idioma['en']['btn_register'] = "REGISTER";

// pagina_principal.php
$idioma['es']['subjects'] = "Asignaturas";
$idioma['es']['no_subjects'] = "Sin asignaturas";
$idioma['es']['delete_subject_title'] = "Eliminar asignatura";
$idioma['es']['delete_subject_confirm'] = "¿Seguro que quieres eliminar esta asignatura?";
$idioma['es']['delete_task_title'] = "Eliminar tarea";
$idioma['es']['edit_task_title_icon'] = "Editar tarea";
$idioma['es']['delete_task_confirm'] = "¿Seguro que deseas eliminar esta tarea?";
$idioma['es']['no_tasks'] = "No tienes tareas pendientes";
$idioma['es']['use_plus_button'] = "Usa el botón + para añadir una nueva asignatura o tarea";
$idioma['es']['add_subject_title_icon'] = "Añadir Asignatura";

$idioma['en']['subjects'] = "Subjects";
$idioma['en']['no_subjects'] = "No subjects";
$idioma['en']['delete_subject_title'] = "Delete subject";
$idioma['en']['delete_subject_confirm'] = "Are you sure you want to delete this subject?";
$idioma['en']['delete_task_title'] = "Delete task";
$idioma['en']['edit_task_title_icon'] = "Edit task";
$idioma['en']['delete_task_confirm'] = "Are you sure you want to delete this task?";
$idioma['en']['no_tasks'] = "You have no pending tasks";
$idioma['en']['use_plus_button'] = "Use the + button to add a new subject or task";
$idioma['en']['add_subject_title_icon'] = "Add Subject";

// añadir_asignatura.php
$idioma['es']['add_subject_title'] = "Nueva Asignatura";
$idioma['es']['subject_exists'] = "Esta asignatura ya existe.";
$idioma['es']['error_saving_subject'] = "Error al guardar la asignatura.";
$idioma['es']['please_write_name'] = "Por favor, escribe un nombre.";
$idioma['es']['subject_name_label'] = "Nombre de la asignatura";
$idioma['es']['subject_name_placeholder'] = "Ej: Matemáticas";
$idioma['es']['btn_save'] = "Guardar";
$idioma['es']['back_home'] = "Volver al inicio";

$idioma['en']['add_subject_title'] = "New Subject";
$idioma['en']['subject_exists'] = "This subject already exists.";
$idioma['en']['error_saving_subject'] = "Error saving subject.";
$idioma['en']['please_write_name'] = "Please write a name.";
$idioma['en']['subject_name_label'] = "Subject name";
$idioma['en']['subject_name_placeholder'] = "Ex: Mathematics";
$idioma['en']['btn_save'] = "Save";
$idioma['en']['back_home'] = "Back to home";

// añadir_tareas.php & editar_tarea.php
$idioma['es']['add_task_title'] = "Añadir Nueva Tarea";
$idioma['es']['edit_task_title'] = "Editar Tarea";
$idioma['es']['error_saving'] = "Error al guardar";
$idioma['es']['error_updating'] = "Error al actualizar";
$idioma['es']['no_permission'] = "No tienes permiso para editar esta tarea.";
$idioma['es']['task_name_label'] = "¿Qué tienes pendiente?";
$idioma['es']['task_name_placeholder'] = "Ej: Estudiar para el examen de cálculo";
$idioma['es']['subject_label'] = "Asignatura";
$idioma['es']['select_one'] = "Selecciona una...";
$idioma['es']['due_date_label'] = "¿Para cuándo?";
$idioma['es']['comments_label'] = "Comentarios";
$idioma['es']['comments_placeholder'] = "Comentarios...";
$idioma['es']['relevance_level'] = "Nivel de relevancia";
$idioma['es']['high'] = "Alta";
$idioma['es']['medium'] = "Media";
$idioma['es']['low'] = "Baja";
$idioma['es']['btn_save_task'] = "Guardar Tarea";
$idioma['es']['btn_update_task'] = "Actualizar Tarea";
$idioma['es']['cancel'] = "Cancelar";

$idioma['en']['add_task_title'] = "Add New Task";
$idioma['en']['edit_task_title'] = "Edit Task";
$idioma['en']['error_saving'] = "Error saving";
$idioma['en']['error_updating'] = "Error updating";
$idioma['en']['no_permission'] = "You do not have permission to edit this task.";
$idioma['en']['task_name_label'] = "What is pending?";
$idioma['en']['task_name_placeholder'] = "Ex: Study for the calculus exam";
$idioma['en']['subject_label'] = "Subject";
$idioma['en']['select_one'] = "Select one...";
$idioma['en']['due_date_label'] = "By when?";
$idioma['en']['comments_label'] = "Comments";
$idioma['en']['comments_placeholder'] = "Comments...";
$idioma['en']['relevance_level'] = "Relevance level";
$idioma['en']['high'] = "High";
$idioma['en']['medium'] = "Medium";
$idioma['en']['low'] = "Low";
$idioma['en']['btn_save_task'] = "Save Task";
$idioma['en']['btn_update_task'] = "Update Task";
$idioma['en']['cancel'] = "Cancel";

// calendario.php
$idioma['es']['calendar_title'] = "Calendario academico";
$idioma['es']['stay_ahead'] = "Mantente un paso adelante de tus";
$idioma['es']['deliveries'] = "entregas";
$idioma['es']['view_month'] = "Mes";
$idioma['es']['view_week'] = "Semana";
$idioma['es']['view_day'] = "Día";
$idioma['es']['sun'] = "DOM";
$idioma['es']['mon'] = "LUN";
$idioma['es']['tue'] = "MAR";
$idioma['es']['wed'] = "MIÉ";
$idioma['es']['thu'] = "JUE";
$idioma['es']['fri'] = "VIE";
$idioma['es']['sat'] = "SÁB";
$idioma['es']['prev_month'] = "Mes Anterior";
$idioma['es']['next_month'] = "Mes Siguiente";
$idioma['es']['add_task_calendar_title'] = "Añadir tarea en este día";

$idioma['en']['calendar_title'] = "Academic Calendar";
$idioma['en']['stay_ahead'] = "Stay one step ahead of your";
$idioma['en']['deliveries'] = "deliveries";
$idioma['en']['view_month'] = "Month";
$idioma['en']['view_week'] = "Week";
$idioma['en']['view_day'] = "Day";
$idioma['en']['sun'] = "SUN";
$idioma['en']['mon'] = "MON";
$idioma['en']['tue'] = "TUE";
$idioma['en']['wed'] = "WED";
$idioma['en']['thu'] = "THU";
$idioma['en']['fri'] = "FRI";
$idioma['en']['sat'] = "SAT";
$idioma['en']['prev_month'] = "Previous Month";
$idioma['en']['next_month'] = "Next Month";
$idioma['en']['add_task_calendar_title'] = "Add task on this day";
?>
