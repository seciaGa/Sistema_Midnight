<?php
include_once '../config/server_connection.php';
include_once '../utils/paginador.php';
$conexion = new ServerConnection();

if (isset($_GET['buscar'])) {
    $buscar = $_GET['buscar'];
} else {
    $buscar = "";
}

// Consulta para mostrar los movimientos con búsqueda
$query = "SELECT id_movimiento, id_producto, tipo_movimiento, fecha, hora, detalles, cantidad, existencia_anterior, existencia_actual
          FROM tbl_movimientos_productos 
          WHERE id_producto LIKE '%{$buscar}%' OR tipo_movimiento LIKE '%{$buscar}%'";
$paginador = new Paginador();
$paginador->query = $query;
$paginador->registros_por_pag = 5;
$paginador->pag_actual = @$_GET['pa'];
$paginador->destino = "listar_movimientos.php";
$paginador->variables_url = "buscar=" . @$_GET["buscar"];
$paginador->crear_paginador();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listado de Movimientos</title>
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
    .table-container {
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
    <h5 class="text-center">Lista de Movimientos de Productos</h5>
    <form action="" method="get" class="d-flex mb-3">
      <input type="text" name="buscar" class="form-control mb-3 me-2" placeholder="Buscar por producto o tipo de movimiento..." value="<?php echo htmlspecialchars($buscar); ?>">
    </form>
    <?php echo $paginador->mostrar_paginador(); ?>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-dark">
        <thead>
          <tr>
            <th>ID Movimiento</th>
            <th>ID Producto</th>
            <th>Tipo Movimiento</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Detalles</th>
            <th>Cantidad</th>
            <th>Existencia Anterior</th>
            <th>Existencia Actual</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($paginador->registros_pagina as $movimiento) { ?>
          <tr>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($movimiento['id_movimiento']); ?>" readonly></td>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($movimiento['id_producto']); ?>" readonly></td>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($movimiento['tipo_movimiento']); ?>" readonly></td>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($movimiento['fecha']); ?>" readonly></td>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($movimiento['hora']); ?>" readonly></td>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($movimiento['detalles']); ?>" readonly></td>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($movimiento['cantidad']); ?>" readonly></td>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($movimiento['existencia_anterior']); ?>" readonly></td>
            <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($movimiento['existencia_actual']); ?>" readonly></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
