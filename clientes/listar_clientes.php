<?php
include_once '../config/server_connection.php';
include_once '../utils/paginador.php';
$conexion = new ServerConnection();

if (isset($_GET['buscar'])) {
    $buscar = $_GET['buscar'];
} else {
    $buscar = "";
}

$query = "SELECT * FROM tbl_clientes WHERE nombre LIKE '%{$buscar}%'";

$paginador = new Paginador();
$paginador->query = $query;
$paginador->registros_por_pag = 5;
$paginador->pag_actual = @$_GET['pa'];
$paginador->destino = "listar_clientes.php";
$paginador->variables_url = "buscar=" . @$_GET["buscar"];

$paginador->crear_paginador();

$clienteSeleccionado = null;
if (isset($_GET['id_cliente'])) {
    $id_cliente = $_GET['id_cliente'];
    foreach ($paginador->registros_pagina as $cliente) {
        if ($cliente['id_cliente'] == $id_cliente) {
            $clienteSeleccionado = $cliente;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listado de Clientes</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="./paginador.css">
  <style>
    body {
      background-color: #343a40; 
      color: white;
    }
    .table-container {
      background-color: #495057;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
    }
    .form-control {
      height: 38px;
    }
    .clientes-table {
  max-width: 100%; 
  margin-left: 20px;
  animation: borderGlow 3s infinite;
}

.detalles-table {
  max-width: 100%;
  margin-right: 20px;
  animation: borderGlow 3s infinite;
}

@keyframes borderGlow {
  0% {
    border-color: #ffffff;
    box-shadow: 0 0 5px rgba(227, 225, 225, 0.5);
  }

  20% {
    border-color: #b2ffff;
    box-shadow: 0 0 15px rgba(227, 225, 225, 0.7);
  }

  40% {
    border-color: #00ffff;
    box-shadow: 0 0 20px rgba(227, 225, 225, 0.9);
  }

  60% {
    border-color: #b2ffff;
    box-shadow: 0 0 15px rgba(227, 225, 225, 0.7);
  }

  80% {
    border-color: #ffffff;
    box-shadow: 0 0 10px rgba(227, 225, 225, 0.6);
  }

  100% {
    border-color: #ffffff;
    box-shadow: 0 0 5px rgba(227, 225, 225, 0.5);
  }
}
</style>
</head>
<body>
<div class="container-fluid vh-100">
  <div class="row h-100">
    <div class="col-md-8 d-flex align-items-center">
      <div class="table-container clientes-table">
        <h5 class="text-center">Lista de Clientes</h5>
        <form action="" method="get">
          <input type="text" name="buscar" class="form-control mb-3" id="busqueda" placeholder="Buscar cliente..." value="<?php echo htmlspecialchars($buscar); ?>">
          <button type="submit" hidden>Enviar</button>
        </form>
        <?php
          echo $paginador->mostrar_paginador();
        ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-dark">
            <thead>
              <tr>
                <th>Nombre Del Cliente</th>
                <th>Tipo</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              foreach ($paginador->registros_pagina as $cliente) { 
              ?>
              <tr>
                <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" readonly></td>
                <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($cliente['tipo_cliente']); ?>" readonly></td>
                <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($cliente['direccion']); ?>" readonly></td>
                <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" readonly></td>
                <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($cliente['fecha_registro']); ?>" readonly></td>
                <td>
                  <a href="?buscar=<?php echo urlencode($buscar); ?>&id_cliente=<?php echo $cliente['id_cliente']; ?>&pa=<?php echo @$_GET['pa']; ?>" class="btn btn-outline-light">Detalles</a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-4 d-flex align-items-center">
      <div class="table-container detalles-table">
        <h5 class="text-center">Detalles del Cliente</h5>
        <form class="w-100">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="id" class="form-label">ID</label>
              <input type="text" class="form-control" id="id" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['id_cliente']) : ''; ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['nombre']) : ''; ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="tipo_cliente" class="form-label">Tipo</label>
              <input type="text" class="form-control" id="tipo_cliente" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['tipo_cliente']) : ''; ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text" class="form-control" id="direccion" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['direccion']) : ''; ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control" id="telefono" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['telefono']) : ''; ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="dui" class="form-label">DUI</label>
              <input type="text" class="form-control" id="dui" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['dui']) : ''; ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="nit" class="form-label">NIT</label>
              <input type="text" class="form-control" id="nit" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['nit']) : ''; ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="nrc" class="form-label">NRC</label>
              <input type="text" class="form-control" id="nrc" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['nrc']) : ''; ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="giro" class="form-label">Giro</label>
              <input type="text" class="form-control" id="giro" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['giro']) : ''; ?>" readonly>
            </div>
            <div class="col-md-6">
              <label for="fecha_registro" class="form-label">Fecha de Registro</label>
              <input type="text" class="form-control" id="fecha_registro" value="<?php echo isset($clienteSeleccionado) ? htmlspecialchars($clienteSeleccionado['fecha_registro']) : ''; ?>" readonly>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
