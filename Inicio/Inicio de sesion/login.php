<?php
session_start();
require_once '../idiomas.php';
include 'conexion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = $idioma[$_SESSION['idioma']]['error_fill_fields'];
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
                $error = $idioma[$_SESSION['idioma']]['error_wrong_password'];
            }
        }
        else {
            $error = $idioma[$_SESSION['idioma']]['error_user_not_found'];
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
    <title>
        <?php echo $idioma[$_SESSION['idioma']]['login_title']; ?>
    </title>
    <link rel="stylesheet" href="../css/login.css">
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
            <?php echo $idioma[$_SESSION['idioma']]['login_title']; ?>
        </h1>

        <hr class="divider">

        <?php if (!empty($error)): ?>
        <div style="color: red; margin-bottom: 1rem; font-family: var(--primary-font); text-align: center;">
            <?php echo $error; ?>
        </div>
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

            <button type="submit" class="btn-login">
                <?php echo $idioma[$_SESSION['idioma']]['btn_login']; ?>
            </button>
        </form>
    </div>
</body>

</html>