<?php
session_start();
include_once('../config/server_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica si el archivo ha sido subido correctamente
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagenNombre = $_FILES['imagen']['name'];
        $imagenTmp = $_FILES['imagen']['tmp_name'];
        $imagenRuta = 'imagenes/' . basename($imagenNombre); // Ruta donde guardarás las imágenes

        // Mueve la imagen a la carpeta deseada
        if (move_uploaded_file($imagenTmp, $imagenRuta)) {
            // Guarda la ruta de la imagen en la base de datos
            $conn = new ServerConnection();
            $conexion = $conn->create_connection();
            $usuario = $_SESSION['usuario'];
            $query = "UPDATE tbl_usuarios SET imagen = ? WHERE usuario = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ss", $imagenRuta, $usuario);
            $stmt->execute();

            echo "Imagen subida con éxito.";
        } else {
            echo "Hubo un error al subir la imagen.";
        }
    }
}
?>

<!-- Formulario para subir imagen -->
<form action="subir_imagen.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="imagen" required>
    <button type="submit" class="btn btn-primary">Subir Imagen</button>
</form>
