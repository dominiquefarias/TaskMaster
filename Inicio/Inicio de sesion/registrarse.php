<?php
include 'conexion.php';
// Creo un 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Completa todos los campos";
    }
    else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);
        $stmt->execute();
        session_start();
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $username;

        header("Location: ../Pantalla principal/pagina_principal.php");
        exit;

    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="../css/registrarse.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cousine&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/favicon.png">
</head>

<body>
    <div class="login-container">
        <h1>Crear cuenta</h1>
        <p class="subtitle">¿Ya estás registrado? <a href="login.php">Iniciar sesión</a></p>

        <hr class="divider">

        <?php if (!empty($error)): ?>
        <p style="color: red; text-align: center;">
            <?php echo $error; ?>
        </p>
        <?php
endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="username">Nombre de usuario</label>
                <input type="text" id="username" name="username" placeholder="Nombre de usuario" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
            </div>

            <button type="submit" class="btn-register">REGISTRARSE</button>
        </form>
    </div>
</body>

</html>