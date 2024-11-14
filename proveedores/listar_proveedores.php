<?php
include_once '../config/server_connection.php';
include_once '../utils/paginador.php';
$conexion = new ServerConnection();

if (isset($_GET['buscar'])) {
    $buscar = $_GET['buscar'];
} else {
    $buscar = "";
}

// Inserción de un nuevo proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && !isset($_POST['id_proveedor'])) {
    $nombre = $_POST['nombre'];
    $tipo_proveedor = $_POST['tipo_proveedor'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $dui = $_POST['dui'];
    $nit = $_POST['nit'];
    $nrc = $_POST['nrc'];
    $giro = $_POST['giro'];
    $tiene_percepcion = isset($_POST['tiene_percepcion']) ? 1 : 0;

    if (!empty($nombre) && !empty($telefono) && !empty($direccion)) {
        // Verificar duplicados
        $query_check = "SELECT * FROM tbl_proveedores WHERE dui = '{$dui}'";
        $conexion->query = $query_check;
        $duplicate_check = $conexion->execute_query();

        if ($duplicate_check->num_rows > 0) {
            // Si el DUI ya existe, redirigir a un script que muestre la alerta
            echo "<script>window.location.href = 'mostrar_alerta.php';</script>";
        } else {
            // Insertar proveedor si no hay duplicados
            $query_insert = "INSERT INTO tbl_proveedores (nombre, tipo_proveedor, telefono, direccion, dui, nit, nrc, giro, tiene_percepcion) 
                             VALUES ('{$nombre}', '{$tipo_proveedor}', '{$telefono}', '{$direccion}', '{$dui}', '{$nit}', '{$nrc}', '{$giro}', {$tiene_percepcion})";
            $conexion->query = $query_insert;
            $result = $conexion->execute_query();

            if ($result) {
                header("Location: listar_proveedores.php?buscar=" . urlencode($buscar) . "&accion=guardado");
                exit();
            } else {
                echo "Error al agregar el proveedor.";
            }
        }
    } else {
        echo "Por favor ingrese todos los datos.";
    }
}
// Actualizar proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_proveedor'])) {
    $id_proveedor = $_POST['id_proveedor'];
    $nombre = $_POST['nombre'];
    $tipo_proveedor = $_POST['tipo_proveedor'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $dui = $_POST['dui'];
    $nit = $_POST['nit'];
    $nrc = $_POST['nrc'];
    $giro = $_POST['giro'];
    $tiene_percepcion = isset($_POST['tiene_percepcion']) ? 1 : 0;

    if (!empty($nombre) && !empty($telefono) && !empty($direccion)) {
        $query_update = "UPDATE tbl_proveedores SET nombre = '{$nombre}', tipo_proveedor = '{$tipo_proveedor}', telefono = '{$telefono}', direccion = '{$direccion}', 
                         dui = '{$dui}', nit = '{$nit}', nrc = '{$nrc}', giro = '{$giro}', tiene_percepcion = {$tiene_percepcion} WHERE id_proveedor = {$id_proveedor}";
        $conexion->query = $query_update;
        $result = $conexion->execute_query();

        if ($result) {
            header("Location: listar_proveedores.php?buscar=" . urlencode($buscar) . "&accion=actualizado");
            exit();
        } else {
            echo "Error al actualizar el proveedor.";
        }
    } else {
        echo "Por favor ingrese todos los datos.";
    }
}

// Eliminar proveedor
if (isset($_GET['eliminar'])) {
    $id_proveedor = $_GET['eliminar'];
    $query_delete = "DELETE FROM tbl_proveedores WHERE id_proveedor = {$id_proveedor}";
    $conexion->query = $query_delete;
    $conexion->execute_query();
    header("Location: listar_proveedores.php?buscar=" . urlencode($buscar) . "&accion=eliminado");
    exit();
}

// Consulta para mostrar los proveedores con búsqueda
$query = "SELECT * FROM tbl_proveedores WHERE nombre LIKE '%{$buscar}%'";

$paginador = new Paginador();
$paginador->query = $query;
$paginador->registros_por_pag = 5;
$paginador->pag_actual = @$_GET['pa'];
$paginador->destino = "listar_proveedores.php";
$paginador->variables_url = "buscar=" . @$_GET["buscar"];
$paginador->crear_paginador();

// Ver detalles de un proveedor
$ProveedorSeleccionado = null;
if (isset($_GET['id_proveedor'])) {
    $id_proveedor = $_GET['id_proveedor'];
    $query_details = "SELECT * FROM tbl_proveedores WHERE id_proveedor = {$id_proveedor}";
    $conexion->query = $query_details;
    $ProveedorSeleccionado = $conexion->execute_query()->fetch_assoc();
}

$modo_edicion = isset($_GET['editar']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listado de Proveedores</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .form-container {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 30px;
    }
    .form-column, .table-column {
        width: 100%;
        max-width: 48%;
    }
    @media (max-width: 768px) {
        .form-container {
            flex-direction: column;
        }
        .form-column, .table-column {
            width: 100%;
        }
    }
    .form-container .form-column {
        background-color: #333;
        padding: 15px;
        border-radius: 5px;
    }
    .btn-group {
        display: flex;
        gap: 10px;
    }
  </style>
</head>
<body class="bg-dark text-white">
<div class="container mt-5">
 <center><h3>Proveedores</h3></center> 

  <form action="" method="get" class="mb-3">
    <input type="text" name="buscar" class="form-control" placeholder="Buscar proveedores..." value="<?php echo htmlspecialchars($buscar); ?>">
  </form>

  <!-- Paginación -->
  <?php echo $paginador->mostrar_paginador(); ?>

  <div class="form-container">

    <div class="table-column">
      <table class="table table-dark">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo Proveedor</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($paginador->registros_pagina as $proveedor) { ?>
            <tr>
              <td><?php echo $proveedor['id_proveedor']; ?></td>
              <td><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
              <td><?php echo htmlspecialchars($proveedor['tipo_proveedor']); ?></td>
              <td><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
              <td><?php echo htmlspecialchars($proveedor['direccion']); ?></td>
              <td>
                <div class="btn-group">
                  <a href="?id_proveedor=<?php echo $proveedor['id_proveedor']; ?>"  class="btn btn-outline-light">Detalles</a>
                  <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $proveedor['id_proveedor']; ?>)" class="btn btn-danger">Eliminar</a>
                </div>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <a href="?agregar=true" class="btn btn-success mb-3">Agregar Proveedor</a>
    </div>

    <!-- Formulario de Detalles o Edición -->
    <div class="form-column">
      <?php if ($ProveedorSeleccionado && !$modo_edicion) { ?>
        <h4>Detalles del Proveedor</h4>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($ProveedorSeleccionado['nombre']); ?></p>
        <p><strong>Tipo Proveedor:</strong> <?php echo htmlspecialchars($ProveedorSeleccionado['tipo_proveedor']); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($ProveedorSeleccionado['telefono']); ?></p>
        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($ProveedorSeleccionado['direccion']); ?></p>
        <p><strong>DUI:</strong> <?php echo htmlspecialchars($ProveedorSeleccionado['dui']); ?></p>
        <p><strong>NIT:</strong> <?php echo htmlspecialchars($ProveedorSeleccionado['nit']); ?></p>
        <p><strong>NRC:</strong> <?php echo htmlspecialchars($ProveedorSeleccionado['nrc']); ?></p>
        <p><strong>Giro:</strong> <?php echo htmlspecialchars($ProveedorSeleccionado['giro']); ?></p>
        <p><strong>Tiene Percepción:</strong> <?php echo $ProveedorSeleccionado['tiene_percepcion'] ? 'Sí' : 'No'; ?></p>
        <a href="?editar=true&id_proveedor=<?php echo $ProveedorSeleccionado['id_proveedor']; ?>" class="btn btn-warning">Editar</a>
      <?php } ?>

      <?php if (isset($_GET['agregar']) || $modo_edicion) { ?>
        <h4><?php echo $modo_edicion ? 'Editar' : 'Agregar'; ?> Proveedor</h4>
        <form method="post">
          <?php if ($modo_edicion) { ?>
            <input type="hidden" name="id_proveedor" value="<?php echo $ProveedorSeleccionado['id_proveedor']; ?>">
          <?php } ?>
          <div class="row mb-3">
            <div class="col-12 col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" value="<?php echo $modo_edicion ? htmlspecialchars($ProveedorSeleccionado['nombre']) : ''; ?>" required autocomplete="off">
            </div>
            <div class="col-12 col-md-6">
              <label for="tipo_proveedor" class="form-label">Tipo Proveedor</label>
              <input type="text" name="tipo_proveedor" class="form-control" value="<?php echo $modo_edicion ? htmlspecialchars($ProveedorSeleccionado['tipo_proveedor']) : ''; ?>" autocomplete="off">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-12 col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" name="telefono" class="form-control" value="<?php echo $modo_edicion ? htmlspecialchars($ProveedorSeleccionado['telefono']) : ''; ?>" required autocomplete="off">
            </div>
            <div class="col-12 col-md-6">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text" name="direccion" class="form-control" value="<?php echo $modo_edicion ? htmlspecialchars($ProveedorSeleccionado['direccion']) : ''; ?>" required autocomplete="off">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-12 col-md-6">
              <label for="dui" class="form-label">DUI</label>
              <input type="text" name="dui" class="form-control" value="<?php echo $modo_edicion ? htmlspecialchars($ProveedorSeleccionado['dui']) : ''; ?>" required autocomplete="off">
            </div>
            <div class="col-12 col-md-6">
              <label for="nit" class="form-label">NIT</label>
              <input type="text" name="nit" class="form-control" value="<?php echo $modo_edicion ? htmlspecialchars($ProveedorSeleccionado['nit']) : ''; ?>" required autocomplete="off">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-12 col-md-6">
              <label for="nrc" class="form-label">NRC</label>
              <input type="text" name="nrc" class="form-control" value="<?php echo $modo_edicion ? htmlspecialchars($ProveedorSeleccionado['nrc']) : ''; ?>" required autocomplete="off">
            </div>
            <div class="col-12 col-md-6">
              <label for="giro" class="form-label">Giro</label>
              <input type="text" name="giro" class="form-control" value="<?php echo $modo_edicion ? htmlspecialchars($ProveedorSeleccionado['giro']) : ''; ?>" required autocomplete="off">
            </div>
          </div>
          <div class="form-check mb-3">
            <input type="checkbox" name="tiene_percepcion" class="form-check-input" <?php echo $modo_edicion && $ProveedorSeleccionado['tiene_percepcion'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="tiene_percepcion">¿Tiene percepción?</label>
          </div>
          <button type="submit" class="btn btn-primary mt-3"><?php echo $modo_edicion ? 'Actualizar' : 'Guardar'; ?></button>
        </form>
      <?php } ?>
    </div>
  </div>
</div>

<script>

  function confirmDelete(id_proveedor) {
    Swal.fire({
      title: '¿Estás seguro?',
      text: 'No podrás revertir esta acción.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '?eliminar=' + id_proveedor;
      }
    });

  }
  
  
  document.addEventListener("DOMContentLoaded", function() {
  const urlParams = new URLSearchParams(window.location.search);
  const accion = urlParams.get("accion");

  if (accion === "guardado") {
    Swal.fire({
      icon: 'success',
      title: 'Guardado',
      text: 'El proveedor ha sido guardado exitosamente.'
    });
  } else if (accion === "actualizado") {
    Swal.fire({
      icon: 'success',
      title: 'Actualizado',
      text: 'El proveedor ha sido actualizado exitosamente.'
    });
  } else if (accion === "eliminado") {
    Swal.fire({
      icon: 'success',
      title: 'Eliminado',
      text: 'El proveedor ha sido eliminado exitosamente.'
    });
  }

  // Eliminar el parámetro 'accion' de la URL después de mostrar el mensaje
  if (accion) {
    urlParams.delete("accion");
    window.history.replaceState({}, document.title, window.location.pathname + "?" + urlParams.toString());
  }
});

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

