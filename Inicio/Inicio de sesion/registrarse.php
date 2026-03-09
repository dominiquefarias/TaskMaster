<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../idiomas.php';
include 'conexion.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($email) || empty($password)) {
        $error = $idioma[$_SESSION['idioma']]['error_fill_fields'];
    }
    else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);
        $stmt->execute();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $username;

        header("Location: ../Pantalla%20principal/pagina_principal.php");
        exit;

    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $idioma[$_SESSION['idioma']]['register_title']; ?>
    </title>
    <link rel="stylesheet" href="../css/registrarse.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cousine&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/favicon.png?v=<?php echo time(); ?>">
</head>

<body>
    <div style="position: absolute; top: 1rem; right: 1rem;">
        <select id="lang_selector"
            style="padding: 5px; border-radius: 5px; border: 1px solid #ccc; font-family: var(--primary-font, sans-serif); cursor: pointer;">
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
    <div class="login-container">
        <h1>
            <?php echo $idioma[$_SESSION['idioma']]['register_title']; ?>
        </h1>
        <p class="subtitle">
            <?php echo $idioma[$_SESSION['idioma']]['already_registered']; ?> <a href="login.php">
                <?php echo $idioma[$_SESSION['idioma']]['already_registered_link']; ?>
            </a>
        </p>

        <hr class="divider">

        <?php if (!empty($error)): ?>
        <p style="color: red; text-align: center;">
            <?php echo $error; ?>
        </p>
        <?php
endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="username">
                    <?php echo $idioma[$_SESSION['idioma']]['username_label']; ?>
                </label>
                <input type="text" id="username" name="username"
                    placeholder="<?php echo $idioma[$_SESSION['idioma']]['username_label']; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">
                    <?php echo $idioma[$_SESSION['idioma']]['email_label']; ?>
                </label>
                <input type="email" id="email" name="email"
                    placeholder="<?php echo $idioma[$_SESSION['idioma']]['email_label']; ?>" required>
            </div>

            <div class="form-group">
                <label for="password">
                    <?php echo $idioma[$_SESSION['idioma']]['password_label']; ?>
                </label>
                <input type="password" id="password" name="password"
                    placeholder="<?php echo $idioma[$_SESSION['idioma']]['password_label']; ?>" required>
            </div>

            <button type="submit" class="btn-register">
                <?php echo $idioma[$_SESSION['idioma']]['btn_register']; ?>
            </button>
        </form>
    </div>
</body>

</html>