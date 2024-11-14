<?php
session_start();
include_once('../config/server_connection.php'); // Asegúrate de que esta ruta sea correcta

$mensaje = ''; // Variable para mostrar mensajes

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_completo = $_POST['nombre_completo'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $tipo_perfil = $_POST['tipo_perfil'];
    $imagen = ''; // Ruta donde se guardará la imagen del perfil

    // Verifica si el nombre de usuario ya existe
    $conn = new ServerConnection();
    $conexion = $conn->create_connection();
    $query_check_user = "SELECT * FROM tbl_usuarios WHERE usuario = ?";
    $stmt_check_user = $conexion->prepare($query_check_user);
    $stmt_check_user->bind_param("s", $usuario);
    $stmt_check_user->execute();
    $result_check_user = $stmt_check_user->get_result();

    if ($result_check_user->num_rows > 0) {
        $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> El nombre de usuario ya está en uso.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        // Cifra la contraseña antes de insertarla
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Verifica si se ha subido una imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreImagen = basename($_FILES["imagen"]["name"]);
            $directorioDestino = "../imagenes/"; // Carpeta donde se guardarán las imágenes
            $rutaCompleta = $directorioDestino . uniqid() . "_" . $nombreImagen;

            // Mover el archivo a la carpeta de destino
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaCompleta)) {
                $imagen = $rutaCompleta; // Guarda la ruta de la imagen
            } else {
                $mensaje = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Advertencia!</strong> No se pudo guardar la imagen.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
            }
        }

        // Consulta para insertar un nuevo usuario
        $query = "INSERT INTO tbl_usuarios (nombre_completo, usuario, password, tipo_perfil, imagen) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("sssss", $nombre_completo, $usuario, $password_hash, $tipo_perfil, $imagen);

        if ($stmt->execute()) {
            // Almacenar los datos del usuario en la sesión para redirección posterior
            $_SESSION['usuario'] = $usuario;
            $_SESSION['tipo_perfil'] = $tipo_perfil;

            // Mensaje de éxito
            $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>¡Éxito!</strong> Usuario registrado con éxito.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';

            // Redirigir al área correspondiente según el tipo de perfil
            if ($tipo_perfil == 'admin') {
                header("Location: dashboard.php");
                exit;
            } else {
                header("Location: dashboard_usuario.php");
                exit;
            }
        } else {
            $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> Hubo un error al registrar el usuario.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Boutique</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 50px auto;
        }
        .form-container h2 {
            text-align: center;
            color: #c798a3;
            margin-bottom: 30px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-custom {
            background-color: #e57373;
            color: white;
            border-radius: 5px;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #c95f5f;
        }
        .form-container input, .form-container select {
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
        }
        .alert-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
            max-width: 90%;
        }
    </style>
</head>
<body>

    <div class="alert-container">
        <?php echo $mensaje; ?> <!-- Mostrar mensaje -->
    </div>

    <div class="form-container">
        <h2>Registro de Usuario</h2>
        <!-- Formulario de registro -->
        <form method="post" action="registro.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre_completo">Nombre Completo</label>
                <input type="text" name="nombre_completo" id="nombre_completo" placeholder="Nombre Completo" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="usuario">Nombre de Usuario</label>
                <input type="text" name="usuario" id="usuario" placeholder="Usuario" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" placeholder="Contraseña" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="tipo_perfil">Tipo de Perfil</label>
                <select name="tipo_perfil" id="tipo_perfil" required>
                    <option value="admin">Administrador</option>
                    <option value="usuario">Usuario</option>
                </select>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen de Perfil</label>
                <input type="file" name="imagen" id="imagen">
            </div>
            <button type="submit" class="btn btn-custom">Registrar</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
