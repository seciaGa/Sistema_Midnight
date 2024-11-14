<?php
// Incluye la conexión o configuración de base de datos según sea necesario.
require_once '../config/server_connection.php'; // Ajusta el nombre del archivo de conexión si es diferente

// Verifica que el parámetro id_cab_compra esté presente
if (isset($_GET['id_cab_compra'])) {
    $id_cab_compra = $_GET['id_cab_compra'];
    
    // Configura la consulta SQL
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
    
    // Obtén los detalles
    $detalles = $conexion->get_records([$id_cab_compra]);

    // Configura el encabezado JSON y envía la respuesta
    header('Content-Type: application/json');
    echo json_encode($detalles ? $detalles[0] : null);
    exit; // Termina el script aquí
}

// Si no se pasa el parámetro id_cab_compra, envía un error
header('Content-Type: application/json');
echo json_encode(['error' => 'No se proporcionó el id_cab_compra']);
exit;
