<?php
session_start();
include 'conexion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "Completa todos los campos";
    }
    else {
        $stmt = $conn->prepare("SELECT id, nombre_usuario, password FROM usuarios WHERE nombre_usuario = ? AND email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_username, $db_password_hash);
            $stmt->fetch();

            if (password_verify($password, $db_password_hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $db_username;

                header("Location: ../index.php");
                exit();
            }
            else {
                $error = "Contraseña incorrecta";
            }
        }
        else {
            $error = "Usuario o email no encontrados";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cousine&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/favicon.png">
</head>

<body>
    <div class="login-container">
        <h1>Iniciar sesion</h1>

        <hr class="divider">

        <?php if (!empty($error)): ?>
        <div style="color: red; margin-bottom: 1rem; font-family: var(--primary-font); text-align: center;">
            <?php echo $error; ?>
        </div>
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

            <button type="submit" class="btn-login">INICIAR SESION</button>
        </form>
    </div>
</body>

</html>