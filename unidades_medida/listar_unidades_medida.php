<?php
include_once '../config/server_connection.php';
include_once '../utils/paginador.php';
$conexion = new ServerConnection();

if (isset($_GET['buscar'])) {
    $buscar = $_GET['buscar'];
} else {
    $buscar = "";
}

// 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && !isset($_POST['id_unidad_medida'])) {
    $nombre = $_POST['nombre'];

    if (!empty($nombre)) {
        $query_insert = "INSERT INTO tbl_unidades_medida (nombre) VALUES ('{$nombre}')";
        $conexion->query = $query_insert;
        $result = $conexion->execute_query();

        if ($result) {
            header("Location: listar_unidades_medida.php?buscar=" . urlencode($buscar) . "&accion=guardado");
            exit();
        } else {
            echo "Error al agregar la unidad de medida.";
        }
    } else {
        echo "Por favor ingrese un nombre para la unidad de medida.";
    }
}

// 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_unidad_medida'], $_POST['nombre'])) {
    $id_unidad_medida = $_POST['id_unidad_medida'];
    $nombre = $_POST['nombre'];

    if (!empty($nombre)) {
        $query_update = "UPDATE tbl_unidades_medida SET nombre = '{$nombre}' WHERE id_unidad_medida = {$id_unidad_medida}";
        $conexion->query = $query_update;
        $result = $conexion->execute_query();

        if ($result) {
            header("Location: listar_unidades_medida.php?buscar=" . urlencode($buscar) . "&accion=actualizado");
            exit();
        } else {
            echo "Error al actualizar la unidad de medida.";
        }
    } else {
        echo "Por favor ingrese un nombre para la Unidad de medida.";
    }
}


if (isset($_GET['eliminar'])) {
    $id_unidad_medida = $_GET['eliminar'];
    $query_delete = "DELETE FROM tbl_unidades_medida WHERE id_unidad_medida = {$id_unidad_medida}";
    $conexion->query = $query_delete;
    $conexion->execute_query();

    header("Location: listar_unidades_medida.php?buscar=" . urlencode($buscar) . "&accion=eliminado");
    exit();
}


$query = "SELECT * FROM tbl_unidades_medida WHERE nombre LIKE '%{$buscar}%'";
$paginador = new Paginador();
$paginador->query = $query;
$paginador->registros_por_pag = 5;
$paginador->pag_actual = @$_GET['pa'];
$paginador->destino = "listar_unidades_medida.php";
$paginador->variables_url = "buscar=" . @$_GET["buscar"];
$paginador->crear_paginador();

$UnidadSeleccionada = null;
$modo_edicion = false;
if (isset($_GET['id_unidad_medida'])) {
    $id_unidad_medida = $_GET['id_unidad_medida'];
    {foreach ($paginador->registros_pagina as $unidad)
        if ($unidad['id_unidad_medida'] == $id_unidad_medida) {
            $UnidadSeleccionada = $unidad;
            $modo_edicion = isset($_GET['editar']);
            break;
        }
    }
}

$modo_agregar = isset($_GET['agregar']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listado de Unidades</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="./paginador.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #343a40;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .container-central {
      display: flex;
      gap: 20px;
      width: 90%;
      max-width: 1000px;
      justify-content: center;
    }
    .table-container, .form-container {
      background-color: #495057;
      border-radius: 8px;
      padding: 20px;
      flex: 1;
    }
    .form-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100%;
    }
    .form-center {
      width: 100%;
      max-width: 400px;
    }
    .form-center .btn {
      width: 100%;
    }
    h5 {
      text-align: center;
    }
  </style>
</head>
<body>
<div class="container-central">
  <div class="table-container">
    <h5 class="text-center">Lista de Unidades Físicas</h5>
    <form action="" method="get" class="d-flex mb-3">
      <input type="text" name="buscar" class="form-control mb-3 me-2" placeholder="Buscar Unidades..." value="<?php echo htmlspecialchars($buscar); ?>">
    </form>
    <?php echo $paginador->mostrar_paginador(); ?>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-dark">
        <thead>
          <tr>
            <th>Nombre de la Ubicación</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($paginador->registros_pagina as $unidad) { ?>
          <tr>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($unidad['nombre']); ?>" readonly></td>
            <td>
              <a href="?buscar=<?php echo urlencode($buscar); ?>&id_unidad_medida=<?php echo $unidad['id_unidad_medida']; ?>&pa=<?php echo @$_GET['pa']; ?>" class="btn btn-outline-light">Detalles</a>
              <a href="javascript:void(0);" class="btn btn-danger ms-2" onclick="confirmDelete('<?php echo $unidad['id_unidad_medida']; ?>')">Eliminar</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <a href="?agregar=true" class="btn btn-success mb-3 d-block w-100">Agregar Ubicación</a>
  </div>

  <div class="form-container">
    <?php if ($modo_agregar) { ?>
      <div class="form-wrapper">
        <div class="form-center">
          <h5 class="text-center">Agregar Nueva Unidad de Medida</h5>
          <form method="post" class="w-100">
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <button type="submit" class="btn btn-success">Agregar</button>
            <a href="listar_unidades_medida.php?buscar=<?php echo urlencode($buscar); ?>" class="btn btn-secondary mt-3">Cancelar</a>
          </form>
        </div>
      </div>
    <?php } elseif ($UnidadSeleccionada) { ?>
      <div class="form-wrapper">
        <div class="form-center">
          <h5 class="text-center"><?php echo $modo_edicion ? 'Editar Unidad' : 'Detalles de la Unidad'; ?></h5>
          <form method="post" class="w-100">
            <input type="hidden" name="id_unidad_medida" value="<?php echo $UnidadSeleccionada['id_unidad_medida']; ?>">
            <div class="mb-3">
              <label for="id" class="form-label">ID</label>
              <input type="text" class="form-control" id="id" value="<?php echo htmlspecialchars($UnidadSeleccionada['id_unidad_medida']); ?>" readonly>
            </div>
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($UnidadSeleccionada['nombre']); ?>" <?php echo $modo_edicion ? '' : 'readonly'; ?>>
            </div>
            <?php if ($modo_edicion) { ?>
              <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
              <a href="listar_unidades_medida.php?buscar=<?php echo urlencode($buscar); ?>" class="btn btn-secondary mt-3 w-100">Cancelar</a>
            <?php } else { ?>
              <a href="listar_unidades_medida.php?buscar=<?php echo urlencode($buscar); ?>&id_unidad_medida=<?php echo $UnidadSeleccionada['id_unidad_medida']; ?>&editar=true&pa=<?php echo $paginador->pag_actual = @$_GET['pa'];?>" class="btn btn-warning w-100">Editar</a>
              <a href="listar_unidades_medida.php?buscar=<?php echo urlencode($buscar); ?>" class="btn btn-secondary mt-3 w-100">Regresar</a>
            <?php } ?>
          </form>
        </div>
      </div>
    <?php } else { ?>
      <p class="text-center">Seleccione una unidad de medida para ver los detalles.</p>
    <?php } ?>
  </div>
</div>

<script>
function confirmDelete(id) {
  Swal.fire({
    title: '¿Estás seguro de eliminar esta ubicación?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "?eliminar=" + id;
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
      text: 'La unidad ha sido guardada exitosamente.'
    });
  } else if (accion === "actualizado") {
    Swal.fire({
      icon: 'success',
      title: 'Actualizado',
      text: 'La unidad ha sido actualizada exitosamente.'
    });
  } else if (accion === "eliminado") {
    Swal.fire({
      icon: 'success',
      title: 'Eliminado',
      text: 'La unidad ha sido eliminada exitosamente.'
    });
  }
});
</script>
</body>
</html>
