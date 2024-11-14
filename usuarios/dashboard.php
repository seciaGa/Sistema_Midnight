<?php
session_start();
include_once('../config/server_connection.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Información de sesión
$usuario = $_SESSION['usuario'];
$tipo_perfil = $_SESSION['tipo_perfil'];
$imagenPerfil = '../imagenes/ava.png';  

// Conexión para obtener la imagen del perfil
$conn = new ServerConnection();
$conexion = $conn->create_connection();
$query = "SELECT imagen FROM tbl_usuarios WHERE usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    if (!empty($row['imagen'])) {
        // Verifica que la imagen existe y es accesible
        $imagenPerfil = htmlspecialchars($row['imagen']);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Boutique</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Ubicación del botón en la esquina inferior izquierda */
        #toggle-theme {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            background-color: #f1f1f1;
            border: none;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
        }
        /* modo oscuro */
        .bg-dark-mode {
            background-color: #121212 !important;
            color: white !important;
        }
        .bg-dark-mode .navbar, .bg-dark-mode .sidebar, .bg-dark-mode .card {
            background-color: #333 !important;
        }
        .bg-dark-mode .card-body {
            color: white !important;
        }
        .bg-dark-mode .navbar-dark .navbar-brand {
            color: #fff !important;
        }

        a .card-body i {
    color: black !important;
        }

    </style>
</head>
<body id="body" class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #c798a3;">
        <a class="navbar-brand" href="#">ADMINISTRADOR</a>
        <div class="ml-auto d-flex align-items-center">
            <!-- Imagen de perfil-->
            <a href="perfil.php">
                <img src="<?php echo $imagenPerfil; ?>" alt="Avatar" class="rounded-circle" width="40" height="40" style="margin-right: 10px;">
                <span class="navbar-text text-white">Bienvenido/a <?php echo $usuario; ?></span>
            </a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Menú lateral -->
            <div class="col-md-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-door-fill"></i> Escritorio</a></li>
                    <li class="nav-item"><a class="nav-link" href="productos.php"><i class="bi bi-box-seam"></i> Productos</a></li>
                    <?php if ($tipo_perfil === 'admin') : ?>
                        <li class="nav-item"><a class="nav-link" href="marcas.php"><i class="bi bi-tags-fill"></i> Marcas</a></li>
                        <li class="nav-item"><a class="nav-link" href="categorias.php"><i class="bi bi-folder-fill"></i> Categorías</a></li>
                        <li class="nav-item"><a class="nav-link" href="usuarios.php"><i class="bi bi-person-badge-fill"></i> Usuarios</a></li>
                        <li class="nav-item"><a class="nav-link" href="movimientos.php"><i class="bi bi-arrow-left-right"></i> Movimientos</a></li>
                        <li class="nav-item"><a class="nav-link" href="movimientos_cajas.php"><i class="bi bi-cash-stack"></i> Movimientos cajas</a></li>
                    <?php else : ?>
                        <li class="nav-item"><a class="nav-link" href="categorias.php"><i class="bi bi-folder-fill"></i> Categorías</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Área principal de contenido -->
            <div class="col-md-10 main-content">
                <h1 class="mt-4">Escritorio</h1>
                <div class="row mt-4">
                <div class="col-md-3">
                
    <a href="ventas.php" class="text-decoration-none text-dark">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-shopping-cart fa-2x"></i>
                <h5 class="card-title">VENTAS</h5>
            </div>
        </div>
    </a>
</div>

<?php if ($tipo_perfil === 'admin') : ?>
    <div class="col-md-3">
        <a href="compras.php" class="text-decoration-none text-dark">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-cart-plus fa-2x"></i>
                    <h5 class="card-title">COMPRAS</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="listar_usuarios.php" class="text-decoration-none">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-person-badge-fill fa-2x"></i>
                    <h5 class="card-title">USUARIOS</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="movimientos_cajas.php" class="text-decoration-none">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-cash-stack fa-2x"></i>
                    <h5 class="card-title">MOVIMIENTOS DE CAJAS</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="ubicaciones.php" class="text-decoration-none">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-geo-alt fa-2x"></i>
                    <h5 class="card-title">UBICACIONES FÍSICAS</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="presentaciones.php" class="text-decoration-none">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-box-seam fa-2x"></i>
                    <h5 class="card-title">PRESENTACIONES</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="unidades_medida.php" class="text-decoration-none">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-rulers fa-2x"></i>
                    <h5 class="card-title">UNIDADES DE MEDIDA</h5>
                </div>
            </div>
        </a>
</div>

                    <?php else : ?>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="bi bi-gem fa-2x"></i>
                                    <h5 class="card-title">CATEGORÍAS</h5>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <button id="toggle-theme"><i class="bi bi-sun"></i></button>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    
    <script>

        // Función para alternar el tema
        const toggleButton = document.getElementById('toggle-theme');
        const body = document.getElementById('body');

        toggleButton.addEventListener('click', function() {
            body.classList.toggle('bg-dark-mode');
            if (body.classList.contains('bg-dark-mode')) {
                toggleButton.innerHTML = '<i class="bi bi-moon"></i>';
            } else {
                toggleButton.innerHTML = '<i class="bi bi-sun"></i>';
            }
        });
    </script>
</body>
</html>
