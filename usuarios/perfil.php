<?php
session_start();
include_once('../config/server_connection.php');

// Verifica que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); 
    exit;
}

$usuario = $_SESSION['usuario'];

$conn = new ServerConnection();
$conexion = $conn->create_connection();

$query = "SELECT nombre_completo, tipo_perfil, imagen, email, fecha_registro FROM tbl_usuarios WHERE usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario_data = $result->fetch_assoc();
    $nombre_completo = $usuario_data['nombre_completo'];
    $tipo_perfil = $usuario_data['tipo_perfil'];
    $imagen = $usuario_data['imagen'];
    $email = $usuario_data['email'];
    $fecha_registro = $usuario_data['fecha_registro'];
} else {
    $nombre_completo = 'Usuario no encontrado';
    $imagen = '../imagenes/ava.png'; // Imagen por defecto si no se encuentra una imagen
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo $nombre_completo; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .container {
            max-width: 600px;
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h2 {
            font-family: 'Georgia', serif;
            font-size: 2em;
            color: #6d597a;
            margin-bottom: 20px;
        }

        .img-thumbnail {
            border-radius: 50%;
            border: 5px solid #d3c0d2;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .img-thumbnail:hover {
            transform: scale(1.05);
        }

        p {
            font-size: 1.1em;
            color: #4a4a4a;
            margin: 10px 0;
        }

        .btn-danger {
            background-color: #b56576;
            border: none;
            font-size: 1em;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 50px;
            transition: background-color 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #96535b;
        }

        .card-title {
            color: #6d597a;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <h2>Perfil de <?php echo $nombre_completo; ?></h2>
    <div>
        <!-- Mostrar la imagen de perfil -->
        <img src="<?php echo $imagen; ?>" alt="Avatar" class="img-thumbnail" style="width: 150px; height: 150px;">
    </div>
    <p><strong>Nombre Completo:</strong> <?php echo $nombre_completo; ?></p>
    <p><strong>Tipo de perfil:</strong> <?php echo ucfirst($tipo_perfil); ?></p>
    <p><strong>Email:</strong> <?php echo $email; ?></p>
    <p><strong>Fecha de Registro:</strong> <?php echo $fecha_registro; ?></p>

    <a href="logout.php" class="btn btn-danger mt-3">Cerrar sesión</a>
</div>

</body>
</html>
