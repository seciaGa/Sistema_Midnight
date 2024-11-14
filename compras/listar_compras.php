<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php
include_once '../config/server_connection.php';
include_once '../utils/paginador.php';

// Conexión a la base de datos
$conexion = new ServerConnection();

$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : "";
// Establecer el número de registros por página
$paginador = new Paginador();
$paginador->registros_por_pag = 5; // Cantidad de compras por página
$paginador->pag_actual = isset($_GET['pa']) ? $_GET['pa'] : 1; // Página actual
$paginador->destino = "listar_compras.php"; // URL donde se encuentra la paginación
$paginador->variables_url = "buscar=" . urlencode($buscar); // Parámetros de búsqueda para mantener la búsqueda al cambiar de página

// Consulta base
$query = "SELECT * FROM tbl_cab_compras WHERE 1=1";

// Si se realiza una búsqueda, agregar el filtro
if (!empty($buscar)) {
    $query .= " AND (fecha_compras LIKE ?)";
    $params[] = "%$buscar%";
}
// Asignar la consulta al paginador
$paginador->query = $query;
$paginador->crear_paginador();
// Obtener los registros con paginación
$datos_compras = $paginador->registros_pagina;


$conexion->query = "SELECT * FROM tbl_cab_compras WHERE 1=1";
// Añadir el filtro de búsqueda si está definido
if (!empty($buscar)) {
    $conexion->query .= " AND (fecha_compras LIKE ?)";
    $params[] = "%$buscar%"; 
}

// Obtener datos de las compras
$conexion->query = 'SELECT * FROM tbl_cab_compras';
$datos_compras = $conexion->get_records() ?: [];


// Obtener datos de los documentos fiscales
$conexion->query = 'SELECT * FROM tbl_documentos_fiscales';
$datos_fiscal = $conexion->get_records() ?: [];

// Obtener datos de los proveedores
$conexion->query = 'SELECT * FROM tbl_proveedores';
$datos_proveedor = $conexion->get_records() ?: [];

// Obtener datos de los productos
$conexion->query = 'SELECT * FROM tbl_productos';
$datos_producto = $conexion->get_records() ?: [];

// Obtener datos de las categorías
$conexion->query = 'SELECT * FROM tbl_categorias';
$datos_categoria = $conexion->get_records() ?: [];

// Obtener datos de las marcas
$conexion->query = 'SELECT * FROM tbl_marcas';
$datos_marcas = $conexion->get_records() ?: [];

if (isset($_GET['id_cab_compra'])) {
  $id_cab_compra = $_GET['id_cab_compra'];
  $conexion->query = "
      SELECT 
          tbl_cab_compras.id_cab_compra,
          tbl_cab_compras.id_documento_fiscal,
          tbl_documentos_fiscales.nombre AS nombre_documento_fiscal,
          tbl_cab_compras.correlativo_documento_fiscal,
          tbl_cab_compras.condicion_compra,
          tbl_cab_compras.fecha_compra,
          tbl_cab_compras.suma,
          tbl_cab_compras.IVA,
          tbl_cab_compras.percepcion,
          tbl_productos.nombre AS nombre_producto,
          tbl_det_compras.cantidad,
          tbl_det_compras.precio_unitario,
          tbl_proveedores.nombre AS nombre_proveedor,
          tbl_categorias.nombre AS nombre_categoria,
          tbl_marcas.nombre AS nombre_marca
      FROM tbl_cab_compras
      JOIN tbl_documentos_fiscales ON tbl_cab_compras.id_documento_fiscal = tbl_documentos_fiscales.id_documento_fiscal
      JOIN tbl_det_compras ON tbl_cab_compras.id_cab_compra = tbl_det_compras.id_cab_compra
      JOIN tbl_productos ON tbl_det_compras.id_producto = tbl_productos.id_producto
      JOIN tbl_proveedores ON tbl_cab_compras.id_proveedor = tbl_proveedores.id_proveedor
      JOIN tbl_categorias ON tbl_productos.id_categoria = tbl_categorias.id_categoria
      JOIN tbl_marcas ON tbl_productos.id_marca = tbl_marcas.id_marca
      WHERE tbl_cab_compras.id_cab_compra = ?
  ";
  $detalles = $conexion->get_records([$id_cab_compra]);

  // Enviar respuesta JSON
  echo json_encode($detalles ? $detalles[0] : null);
  exit;
}

$query = "SELECT * FROM tbl_cab_compras WHERE id_documento_fiscal LIKE '%{$buscar}%'";
$paginador = new Paginador();
$paginador->query = $query;
$paginador->registros_por_pag = 5;
$paginador->pag_actual = @$_GET['pa'];
$paginador->destino = "listar_compras.php";
$paginador->variables_url = "buscar=" . @$_GET["buscar"];
$paginador->crear_paginador();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar y Listar Compras</title>
  <link rel="stylesheet" href="paginador.css">
  <style>
    /* Estilos generales */
    body {
      font-family: Arial, sans-serif;
      background-color: #2c2c2c;
      color: #fff;
      margin: 0;
      padding: 0;
      position: relative;
    }
    
    h2, h3 {
      text-align: center;
    }
    form {
      width: 80%;
      margin: 20px auto;
      background-color: #333;
      padding: 20px;
      border-radius: 8px;
    }
    .form-container {
      display: flex;
      justify-content: space-between;
      gap: 20px;
    }
    .form-container > div {
      width: 48%;
      background-color: #444;
      padding: 20px;
      border-radius: 8px;
    }
    label {
      display: block;
      margin-bottom: 5px;
    }
    input, select {
      width: 100%;
      padding: 8px;
      margin-bottom: 12px;
      border: 1px solid #444;
      border-radius: 4px;
      background-color: #222;
      color: #fff;
      font-size: 14px;
      height: 30px; 
    }
    .inline {
      display: inline-block;
      width: 48%;
    }
    .inline-container {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }
    button {
      display: block;
      width: 100%;
      padding: 15px;
      margin-top: 20px;
      background-color: #28a745;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background-color: #218838;
    }
  </style>
  <script>
    function mostrarFormularioAgregar() {
      document.getElementById("detalles-compra").style.display = "none";
      document.getElementById("formulario-agregar").style.display = "block";
    }

    function mostrarDetallesCompra(idCabCompra) {
      document.getElementById("formulario-agregar").style.display = "none";
      document.getElementById("detalles-compra").style.display = "block";

      // Hacer una solicitud para obtener los detalles de la compra
      fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?id_cab_compra=${idCabCompra}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
              document.getElementById("detalle_id_compra").value = data.id_cab_compra || '';
              document.getElementById("detalle_id_documento_fiscal").value = data.id_documento_fiscal || '';
              document.getElementById("detalle_correlativo_documento_fiscal").value = data.correlativo_documento_fiscal || '';
              document.getElementById("detalle_condicion_compra").value = data.condicion_compra || '';
              document.getElementById("detalle_fecha_compra").value = data.fecha_compra || '';
              document.getElementById("detalle_suma").value = data.suma || '';
              document.getElementById("detalle_IVA").value = data.IVA || '';
              document.getElementById("detalle_percepcion").value = data.percepcion || '';
              document.getElementById("detalle_id_producto").value = data.id_producto || '';
              document.getElementById("detalle_cantidad").value = data.cantidad || '';
              document.getElementById("detalle_precio_unitario").value = data.precio_unitario || '';

            } else {
                alert('No se encontraron detalles para esta compra.');
            }
        })
        .catch(error => console.error('Error al obtener los detalles de la compra:', error));
    }


    function calcularTotal() {
      var precioUnitario = parseFloat(document.getElementsByName('precio_unitario[]')[0].value) || 0;
      var cantidad = parseInt(document.getElementsByName('cantidad[]')[0].value) || 0;
      var iva = parseFloat(document.getElementsByName('IVA')[0].value) || 0;

      var suma = precioUnitario * cantidad;
      var total = suma + (suma * (iva / 100));

      document.getElementsByName('total')[0].value = total.toFixed(2);
    }
  </script>
</head>
<body>


<form action="insertar_compra.php" method="POST">
  <div class="form-container">
    <!-- Lado izquierdo: Listado de compras -->
    <div>
      <h3>Lista de Compras</h3>
      <form action="" method="get" class="d-flex mb-3">
      <input type="text" name="buscar" class="form-control mb-3 me-2" placeholder="Buscar..." value="<?php echo htmlspecialchars($buscar); ?>">
      <div class="container">
  
  </div>
  <form>
  <?php echo $paginador->mostrar_paginador(); ?>
  
  <!-- Tabla de compras -->
  <table style="width: 100%; border-collapse: collapse; background-color: #333; color: #fff;">
    <thead>
      <tr style="background-color: #444;">
        <th>ID Compra</th>
        <th id="fecha_compras">Fecha</th>
        <th>Total</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($paginador->registros_pagina as $compra) { ?>
      <tr style="border-bottom: 1px solid #555;">
        <td><?php echo $compra['id_cab_compra']; ?></td>
        <td><?php echo $compra['fecha_compra']; ?></td>
        <td><?php echo number_format($compra['total'], 2); ?></td>
        <td>
        <button type="button" onclick="mostrarDetallesCompra(<?php echo $compra['id_cab_compra']; ?>);" style="padding: 5px 10px; background-color: #007bff; color: #fff; border: none; border-radius: 3px; cursor: pointer;">
    Detalles
</button>

        </td>
      </tr>
      <?php } ?>
              </tr>
          </tbody>
        </table>
      <div style="text-align: center; margin-top: 15px;">
        <button type="button" onclick="mostrarFormularioAgregar();" style="padding: 10px 20px; background-color: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
          Agregar Compra
        </button>
      </div>
    </div>

    

    <!-- Lado derecho: Formulario de nueva compra o detalles -->
    <div>
    <div id="formulario-agregar" style="display: none;">
                <h3 style=" margin-top: 5px;">Datos de la Compra</h3>
                <div class="inline-container">
                    <div class="inline">
                        <label  for="id_documento_fiscal">Documento Fiscal:</label>
                        <select style="height: 50%;   width: 108%;" name="id_documento_fiscal" required>
                            <?php foreach ($datos_fiscal as $fiscal): ?>
                                <option value="<?php echo $fiscal['id_documento_fiscal']; ?>"><?php echo $fiscal['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="inline">
                        <label for="correlativo_documento_fiscal">Correlativo Documento Fiscal:</label>
                        <input style="height: 50%; width: 108%;" type="text" name="correlativo_documento_fiscal" required>
                    </div>
                </div>

                <div class="inline-container">
                    <div class="inline">
                        <label for="id_proveedor">Proveedor:</label>
                        <select style="height: 50%; width: 108%;" name="id_proveedor" required>
                            <?php foreach ($datos_proveedor as $proveedor): ?>
                                <option value="<?php echo $proveedor['id_proveedor']; ?>"><?php echo $proveedor['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="inline">
                        <label for="id_marca">Marca:</label>
                        <select  style="height: 50%; width: 107%;" name="id_marca" required>
                            <?php foreach ($datos_marcas as $marca): ?>
                                <option value="<?php echo $marca['id_marca']; ?>"><?php echo $marca['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="inline-container">
                    <div class="inline">
                        <label for="id_categoria">Categoría:</label>
                        <select style="height: 50%; width: 108%;" name="id_categoria" required>
                            <?php foreach ($datos_categoria as $categoria): ?>
                                <option value="<?php echo $categoria['id_categoria']; ?>"><?php echo $categoria['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="inline">
                        <label for="condicion_compra">Condición Compra:</label>
                        <input style="height: 50%;  width: 108%;" type="text" name="condicion_compra" required>
                    </div>
                </div>

                <div class="inline-container">
                    <div class="inline">
                        <label for="fecha_compra">Fecha Compra:</label>
                        <input style="height: 50%; width: 108%;" type="date" name="fecha_compra" required>
                    </div>
                    <div class="inline">
                        <label for="IVA">IVA (%):</label>
                        <input  style="height: 50%; width: 108%;" type="number" step="0.01" name="IVA" required oninput="calcularTotal()">
                    </div>
                </div>

                <div class="inline-container">
                    <div class="inline">
                        <label for="percepcion">Percepción:</label>
                        <input style="height: 50%; width: 108%;" type="number" step="0.01" name="percepcion" required>
                    </div>
                    <div class="inline">
                        <label for="id_producto">Producto:</label>
                        <select style="height: 50%; width: 108%;"  name="id_producto[]" required>
                            <?php foreach ($datos_producto as $producto): ?>
                                <option value="<?php echo $producto['id_producto']; ?>"><?php echo $producto['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="inline-container">
                    <div class="inline">
                        <label for="precio_unitario">Precio Unitario:</label>
                        <input style="height: 50%; width: 108%;" type="number" step="0.01" name="precio_unitario[]" required oninput="calcularTotal()">
                    </div>
                    <div class="inline">
                        <label for="cantidad">Cantidad:</label>
                        <input  style="height: 50%; width: 108%;" type="number" name="cantidad[]" required oninput="calcularTotal()">
                    </div>
                </div>

                <input type="hidden" name="total" required>

                <button type="submit">Guardar Compra</button>
            </div>

      <!-- Sección de detalles de la compra seleccionada -->
      <div>
    <div id="detalles-compra" style="display: none;">
        <h3 style="margin-top: 5px;">Detalles de la Compra Seleccionada</h3>
        
        <div class="inline-container">
            <div class="inline">
                <label for="detalle_id_compra">ID Compra:</label>
                <input type="text" id="detalle_id_compra" name="detalle_id_compra" readonly style="height: 50%; width: 100%;">
            </div>
            <div class="inline">
                <label for="detalle_id_cab_compra">ID Cabecera Compra:</label>
                <input type="text" id="detalle_id_cab_compra" name="detalle_id_cab_compra" readonly style="height: 50%; width: 100%;">
            </div>
        </div>

        <div class="inline-container">
            <div class="inline">
                <label for="detalle_id_producto">ID Producto:</label>
                <input type="text" id="detalle_id_producto" name="detalle_id_producto" readonly style="height: 50%; width: 100%;">
            </div>
            <div class="inline">
                <label for="detalle_cantidad">Cantidad:</label>
                <input type="text" id="detalle_cantidad" name="detalle_cantidad" readonly style="height: 50%; width: 100%;">
            </div>
        </div>

        <div class="inline-container">
            <div class="inline">
                <label for="detalle_precio_unitario">Precio Unitario:</label>
                <input type="text" id="detalle_precio_unitario" name="detalle_precio_unitario" readonly style="height: 50%; width: 100%;">
            </div>
        </div>
    </div>
</div>

    </div>
  </div>
</form>

</body>
</html>