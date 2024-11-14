<?php
include_once '../config/server_connection.php';
$conexion = new ServerConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Recibiendo los datos del formulario
        $id_documento_fiscal = $_POST['id_documento_fiscal'];
        $correlativo_documento_fiscal = $_POST['correlativo_documento_fiscal'];
        $id_proveedor = $_POST['id_proveedor'];
        $condicion_compra = $_POST['condicion_compra'];
        $fecha_compra = $_POST['fecha_compra'];
        $suma = $_POST['suma'];
        $IVA = $_POST['IVA'];
        $percepcion = $_POST['percepcion'];
        $total = $_POST['total'];

        // Insertando datos en la tabla tbl_cab_compras
        $conexion->query = "INSERT INTO tbl_cab_compras (id_documento_fiscal, correlativo_documento_fiscal, id_proveedor, condicion_compra, fecha_compra, suma, IVA, percepcion, total) 
                            VALUES ('$id_documento_fiscal', '$correlativo_documento_fiscal', '$id_proveedor', '$condicion_compra', '$fecha_compra', '$suma', '$IVA', '$percepcion', '$total')";
        
        if (!$conexion->execute_query()) {
            throw new Exception("Error al insertar en tbl_cab_compras");
        }

        // Obtener el último id insertado en la tabla tbl_cab_compras
        $sql_last_id = "SELECT MAX(id_cab_compra) AS last_id FROM tbl_cab_compras";
        $conexion->query = $sql_last_id;
        $result = $conexion->get_records();
        
        if (is_array($result) && count($result) > 0) {
            $id_cab_compra = $result[0]['last_id'];
        } else {
            throw new Exception("No se pudo obtener el último ID de compra.");
        }

        // Insertando detalles de la compra
        $id_producto = $_POST['id_producto'];
        $cantidad = $_POST['cantidad'];
        $precio_unitario = $_POST['precio_unitario'];

        if (is_array($id_producto) && is_array($cantidad) && is_array($precio_unitario)) {
            foreach ($id_producto as $index => $producto) {
                $query_detalle = "INSERT INTO tbl_det_compras (id_cab_compra, id_producto, cantidad, precio_unitario) 
                                  VALUES ('$id_cab_compra', '{$producto}', '{$cantidad[$index]}', '{$precio_unitario[$index]}')";
                $conexion->query = $query_detalle;
                
                if (!$conexion->execute_query()) {
                    throw new Exception("Error al insertar en tbl_det_compras para el producto ID $producto");
                }
            }
        }

        echo "Compra registrada exitosamente!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
