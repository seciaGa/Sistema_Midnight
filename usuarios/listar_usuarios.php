<?php
session_start();
include_once('../config/server_connection.php');

// Verifica que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); 
    exit;
}

$conn = new ServerConnection();
$conexion = $conn->create_connection();

// Consulta para obtener los usuarios
$query = "SELECT id_usuario, nombre_completo, usuario, tipo_perfil, activo, fecha_registro, imagen, email FROM tbl_usuarios";
$result = $conexion->query($query);

// Eliminar usuario
if (isset($_GET['eliminar_id'])) {
    $id_usuario = $_GET['eliminar_id'];
    $deleteQuery = "DELETE FROM tbl_usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($deleteQuery);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    header("Location: listado_usuarios.php"); 
    exit;
}

// Editar usuario
if (isset($_POST['editar'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre_completo = $_POST['nombre_completo'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $tipo_perfil = $_POST['tipo_perfil'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    $updateQuery = "UPDATE tbl_usuarios SET nombre_completo = ?, usuario = ?, email = ?, tipo_perfil = ?, activo = ? WHERE id_usuario = ?";
    $stmt = $conexion->prepare($updateQuery);
    $stmt->bind_param("ssssii", $nombre_completo, $usuario, $email, $tipo_perfil, $activo, $id_usuario);
    $stmt->execute();
    header("Location: listado_usuarios.php");
    exit;
}

// Detalles del usuario
$detalles_usuario = null;
if (isset($_GET['detalles_id'])) {
    $id_usuario = $_GET['detalles_id'];
    $queryDetalles = "SELECT * FROM tbl_usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($queryDetalles);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $detalles_usuario = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-body form input, .modal-body form select {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Listado de Usuarios</h2>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>Nombre Completo</th>
                <th>Usuario</th>
                <th>Tipo Perfil</th>
                <th>Activo</th>
                <th>Fecha Registro</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['id_usuario']; ?></td>
                    <td><img src="<?php echo $row['imagen']; ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%;"></td>
                    <td><?php echo $row['nombre_completo']; ?></td>
                    <td><?php echo $row['usuario']; ?></td>
                    <td><?php echo ucfirst($row['tipo_perfil']); ?></td>
                    <td><?php echo $row['activo'] ? 'Sí' : 'No'; ?></td>
                    <td><?php echo $row['fecha_registro']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $row['id_usuario']; ?>">Detalles</button>
                        <!-- Modal de Detalles -->
                        <div class="modal fade" id="detailsModal<?php echo $row['id_usuario']; ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?php echo $row['id_usuario']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailsModalLabel<?php echo $row['id_usuario']; ?>">Detalles del Usuario</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario']; ?>">
                                            <div class="mb-3">
                                                <label for="nombre_completo" class="form-label">Nombre Completo</label>
                                                <input type="text" class="form-control" id="nombre_completo" value="<?php echo $row['nombre_completo']; ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label for="usuario" class="form-label">Usuario</label>
                                                <input type="text" class="form-control" id="usuario" value="<?php echo $row['usuario']; ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" value="<?php echo $row['email']; ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label for="tipo_perfil" class="form-label">Tipo de Perfil</label>
                                                <input type="text" class="form-control" value="<?php echo ucfirst($row['tipo_perfil']); ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label for="activo" class="form-label">Activo</label>
                                                <input type="checkbox" id="activo" <?php echo $row['activo'] ? 'checked' : ''; ?> disabled>
                                            </div>
                                            <a href="?detalles_id=<?php echo $row['id_usuario']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                            <a href="?eliminar_id=<?php echo $row['id_usuario']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="?agregar=1" class="btn btn-primary mt-3">Agregar Usuario</a>
</div>

<!-- Modal para Agregar Usuario -->
<?php if (isset($_GET['agregar']) && $_GET['agregar'] == 1) : ?>
    <div class="modal fade show" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true" style="display: block;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Agregar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="nombre_completo" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_perfil" class="form-label">Tipo de Perfil</label>
                            <select class="form-control" name="tipo_perfil" required>
                                <option value="admin">Administrador</option>
                                <option value="usuario">Usuario Normal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="activo" class="form-label">Activo</label>
                            <input type="checkbox" name="activo" checked>
                        </div>
                        <button type="submit" class="btn btn-primary" name="agregar_usuario">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Script Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
