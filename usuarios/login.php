<?php
session_start();
include_once('../config/server_connection.php');

$error = ""; // Variable para almacenar el mensaje de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $conn = new ServerConnection();
    $conexion = $conn->create_connection();

    // Consulta para verificar las credenciales
    $query = "SELECT * FROM tbl_usuarios WHERE usuario = ? AND password = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ss", $usuario, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['tipo_perfil'] = $user['tipo_perfil'];
        
        if ($_SESSION['tipo_perfil'] == 'admin') {
            header("Location: dashboard.php");
            exit;
        } else if ($_SESSION['tipo_perfil'] == 'usuario') {
            header("Location: dashboard_usuario.php");
            exit;
        }
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Boutique</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f2e1d2, #c798a3);
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            max-width: 500px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .login-container h2 {
            text-align: center;
            color: #c798a3;
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .form-control {
            padding: 18px;
            border: 2px solid #c798a3;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #b88c96;
            box-shadow: 0 0 5px rgba(185, 140, 150, 0.7);
        }

        .btn-login {
            background-color: #c798a3;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .btn-login:hover {
            background-color: #b88c96;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>

    <!-- Mostrar mensaje de error si existe -->
    <?php if (!empty($error)): ?>
        <div class="error-message">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <div class="form-group">
            <input type="text" name="usuario" class="form-control" placeholder="Usuario" required autocomplete="off">
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
        </div>
        <button type="submit" class="btn-login">Entrar</button>
    </form>
    <div class="text-center mt-3">
        <a href="#">¿Olvidaste tu contraseña?</a>
    </div>
</div>

</body>
</html>
