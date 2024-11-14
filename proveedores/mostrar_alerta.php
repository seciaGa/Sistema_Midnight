<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El proveedor con el mismo DUI ya existe.',
            confirmButtonText: 'Aceptar',
            customClass: {
                popup: 'animate__animated animate__fadeInDown'
            }
        }).then(() => {
            window.history.back();
        });
    </script>
</body>
</html>
