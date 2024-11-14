<?php
include_once '../config/server_connection.php';
include_once '../utils/paginador.php';
$conexion = new ServerConnection();

if (isset($_GET['buscar'])) {
    $buscar = $_GET['buscar'];
} else {
    $buscar = "";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && !isset($_POST['id_metodo_pago'])) {
    $nombre = $_POST['nombre'];

    if (!empty($nombre)) {
        $query_insert = "INSERT INTO tbl_metodos_pago (nombre) VALUES ('{$nombre}')";
        $conexion->query = $query_insert;
        $result = $conexion->execute_query();

        if ($result) {
            header("Location: listar.php?buscar=" . urlencode($buscar));
            exit();
        } else {
            echo "Error al agregar un metodo de pago.";
        }
    } else {
        echo "Por favor ingrese un nombre para rl metodo de pago.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_metodo_pago'], $_POST['nombre'])) {
    $id_metodo_pago = $_POST['id_metodo_pago'];
    $nombre = $_POST['nombre'];

    if (!empty($nombre)) {
        $query_update = "UPDATE tbl_metodos_pago SET nombre = '{$nombre}' WHERE id_metodo_pago = {$id_metodo_pago}";
        $conexion->query = $query_update;
        $result = $conexion->execute_query();

        if ($result) {
            header("Location: listar.php?buscar=" . urlencode($buscar));
            exit();
        } else {
            echo "Error al actualizar metodo de pago.";
        }
    } else {
        echo "Por favor ingrese un nombre para metodo de pago.";
    }
}

if (isset($_GET['eliminar'])) {
    $id_marca = $_GET['eliminar'];
    $query_delete = "DELETE FROM tbl_metodos_pago WHERE id_metodo_pago = {$id_metodo_pago}";
    $conexion->query = $query_delete;
    $conexion->execute_query();

    header("Location: listar.php?buscar=" . urlencode($buscar));
    exit();
}

$query = "SELECT * FROM tbl_metodos_pago WHERE nombre LIKE '%{$buscar}%'";
$paginador = new Paginador();
$paginador->query = $query;
$paginador->registros_por_pag = 5;
$paginador->pag_actual = @$_GET['pa'];
$paginador->destino = "listar.php";
$paginador->variables_url = "buscar=" . @$_GET["buscar"];
$paginador->crear_paginador();

$metodoSeleccionada = null;
$modo_edicion = false;
if (isset($_GET['id_metodo_pago'])) {
    $id_metodo_pago = $_GET['id_metodo_pago'];
    foreach ($paginador->registros_pagina as $metodo) {
        if ($metodo['id_metodo_pago'] == $id_metodo_pago) {
            $metodoSeleccionada = $metodo;
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
  <title>Listado de Metodos de Pago</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="paginador.css">
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
    .table-container {
      background-color: #495057;
      border-radius: 8px;
      padding: 20px;
      margin: 0 10px;
    }
    .form-control {
      height: 38px;
    }
    .clientes-table, .detalles-table {
      animation: borderGlow 3s infinite;
      width: 100%;
    }
    .container-fluid {
      max-width: 80%;
      display: flex;
      justify-content: center;
      gap: 20px;
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
    .detalles-table h5 {
      text-align: center;
    }
    @keyframes borderGlow {
      0% { border-color: #ffffff; box-shadow: 0 0 5px rgba(227, 225, 225, 0.5); }
      20% { border-color: #b2ffff; box-shadow: 0 0 15px rgba(227, 225, 225, 0.7); }
      40% { border-color: #00ffff; box-shadow: 0 0 20px rgba(227, 225, 225, 0.9); }
      60% { border-color: #b2ffff; box-shadow: 0 0 15px rgba(227, 225, 225, 0.7); }
      80% { border-color: #ffffff; box-shadow: 0 0 10px rgba(227, 225, 225, 0.6); }
      100% { border-color: #ffffff; box-shadow: 0 0 5px rgba(227, 225, 225, 0.5); }
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="table-container clientes-table">
    <h5 class="text-center">Lista de Metodos de Pago</h5>
    <form action="" method="get" class="d-flex mb-3">
      <input type="text" name="buscar" class="form-control mb-3 me-2" placeholder="Buscar metodo..." value="<?php echo htmlspecialchars($buscar); ?>">
    </form>
    <?php echo $paginador->mostrar_paginador(); ?>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-dark">
        <thead>
          <tr>
            <th>Nombre de Metodo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($paginador->registros_pagina as $metodo) { ?>
          <tr>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($metodo['nombre']); ?>" readonly></td>
            <td>
              <a href="?buscar=<?php echo urlencode($buscar); ?>&id_metodo_pago=<?php echo $metodo['id_metodo_pago']; ?>&pa=<?php echo @$_GET['pa']; ?>" class="btn btn-outline-light">Detalles</a>
              <a href="javascript:void(0);" class="btn btn-danger ms-2" onclick="confirmDelete('<?php echo $metodo['id_metodo_pago']; ?>')">Eliminar</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <a href="?agregar=true" class="btn btn-success mb-3 d-block w-100">Agregar Metodo de Pago</a>
  </div>

  <div class="table-container detalles-table">
    <?php if ($modo_agregar) { ?>
      <div class="form-wrapper">
        <div class="form-center">
          <h5 class="text-center">Agregar Nuevo Metodo de Pago</h5>
          <form method="post" class="w-100">
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <button type="submit" class="btn btn-success">Agregar</button>
            <a href="listar.php?buscar=<?php echo urlencode($buscar); ?>" class="btn btn-secondary mt-3">Cancelar</a>
          </form>
        </div>
      </div>
    <?php } elseif ($metodoSeleccionada) { ?>
      <h5 class="text-center"><?php echo $modo_edicion ? 'Editar Metodo de Pago' : 'Detalles de Metodo de Pago'; ?></h5>
      <form method="post" class="w-100">
        <input type="hidden" name="id_metodo_pago" value="<?php echo $metodoSeleccionada['id_metodo_pago']; ?>">
        <div class="mb-3">
          <label for="id" class="form-label">ID</label>
          <input type="text" class="form-control" id="id" value="<?php echo htmlspecialchars($metodoSeleccionada['id_metodo_pago']); ?>" readonly>
        </div>
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($metodoSeleccionada['nombre']); ?>" <?php echo $modo_edicion ? '' : 'readonly'; ?>>
        </div>
        <?php if ($modo_edicion) { ?>
          <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
          <a href="listar.php?buscar=<?php echo urlencode($buscar); ?>" class="btn btn-secondary mt-3 w-100">Cancelar</a>
        <?php } else { ?>
          <a href="listar.php?buscar=<?php echo urlencode($buscar); ?>&id_metodo_pago=<?php echo $metodoSeleccionada['id_metodo_pago']; ?>&editar=true&pa=<?php echo $paginador->pag_actual = @$_GET['pa'];?>" class="btn btn-warning w-100">Editar</a>
          <a href="listar.php?buscar=<?php echo urlencode($buscar); ?>" class="btn btn-secondary mt-3 w-100">Regresar</a>
        <?php } ?>
      </form>
    <?php } else { ?>
      <h5 class="text-center">Seleccione una metodo de pago para ver los detalles.</h5>
    <?php } ?>
  </div>
</div>

<script>
  function confirmDelete(id) {
    Swal.fire({
      title: '¿Estás seguro?',
      text: 'No podrás recuperar este metodo de pago.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "?buscar=<?php echo urlencode($buscar); ?>&eliminar=" + id;
      }
    });
  }
</script>

</body>
</html>
