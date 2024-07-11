<?php
session_start();
include('../php/Conexion.php');

if (!isset($_SESSION['IdUsuario'])) {
    header("Location: InicioSesion.php");
    exit();
}

$IdUsuario = $_SESSION['IdUsuario'];

$sql = "SELECT * FROM usuario WHERE IdUsuario = $IdUsuario";
$result = mysqli_query($conexion, $sql);

if ($result->num_rows > 0) {
    $usuario = mysqli_fetch_assoc($result);
    $tipo_usuario = $usuario['Tipo_Usuario'];

    // Obtener información adicional según el tipo de usuario
    $info_adicional = [];
    if ($tipo_usuario == 'Postulante') {
        $sql_postulante = "SELECT * FROM postulante WHERE IdUsuario = $IdUsuario";
        $result_postulante = mysqli_query($conexion, $sql_postulante);
        if ($result_postulante->num_rows > 0) {
            $info_adicional = mysqli_fetch_assoc($result_postulante);
        }
    } elseif ($tipo_usuario == 'Empleador') {
        $sql_empleador = "SELECT * FROM empleador WHERE IdUsuario = $IdUsuario";
        $result_empleador = mysqli_query($conexion, $sql_empleador);
        if ($result_empleador->num_rows > 0) {
            $info_adicional = mysqli_fetch_assoc($result_empleador);
        }
    } elseif ($tipo_usuario == 'Empresa') {
        $sql_empresa = "SELECT * FROM empresa WHERE IdUsuario = $IdUsuario";
        $result_empresa = mysqli_query($conexion, $sql_empresa);
        if ($result_empresa->num_rows > 0) {
            $info_adicional = mysqli_fetch_assoc($result_empresa);
        }
    }
} else {
    echo "No se encontraron datos del usuario.";
    exit();
}

// Obtener las publicaciones del usuario
$sql_publicaciones = "
SELECT p.*, s.NombreServicio 
FROM publicaciones p 
JOIN servicios s ON p.IdServicios = s.IdServicios 
WHERE p.IdUsuario = $IdUsuario 
ORDER BY p.Fecha DESC";
$result_publicaciones = mysqli_query($conexion, $sql_publicaciones);
$publicaciones = [];
if ($result_publicaciones->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result_publicaciones)) {
        $publicaciones[] = $row;
    }
}

// Manejar la publicación de un servicio
if (isset($_POST["submit_servicio"])) {
    $tipo_categoria = $_POST['tipo_categoria'];
    $nombre_servicio = $_POST['nombre_servicio'];
    $descripcion = $_POST['descripcion'];
    $fecha = date('Y-m-d H:i:s');

    // Primero, insertar el nuevo servicio en la tabla servicios
    $query_servicio = "INSERT INTO servicios (IdCategoria, NombreServicio) VALUES ('$tipo_categoria', '$nombre_servicio')";
    if (mysqli_query($conexion, $query_servicio)) {
        // Obtener el id del servicio recién insertado
        $id_servicio = mysqli_insert_id($conexion);

        // Insertar la publicación en la tabla publicaciones
        $query_publicacion = "INSERT INTO publicaciones (IdServicios, IdUsuario, Fecha, DescripcionPublicacion, TipoPublicacion) VALUES ('$id_servicio', '$IdUsuario', '$fecha', '$descripcion', 'Oferta')";
        if (mysqli_query($conexion, $query_publicacion)) {
            echo "<script>alert('Servicio publicado con éxito'); document.location.href = 'PaginaPerfilEmpresa.php';</script>";
        } else {
            echo "<script>alert('Error al publicar el Oferta');</script>";
        }
    } else {
        echo "<script>alert('Error al registrar el Oferta');</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Perfil de Usuario</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon"/>
    <!-- CSS Files -->

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/estiloperfilEmpresa.css"/>


</head>
<body>
    <div class="row">
    <div class="col-lg-12 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column flex-md-row align-items-center">
    <img src="<?php echo $imagen_perfil; ?>" alt="Foto de perfil" class="avatar-img" data-toggle="modal" data-target="#uploadModal">
    <div class="text-center text-md-left mt-2 mt-md-0 ml-md-3">
        <h3 class="mb-0"><?php echo $info_adicional['NombreEmpresa']; ?></h3>
    </div>
</div>

                
                </div>
                <hr>
                <div class="profile-info">
                    <h4>Información</h4>
                    <p><strong>Correo Electrónico:</strong> <?php echo $usuario['Correo']; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $info_adicional['TelefonoCelular']; ?></p>
                    <p><strong>Dirección:</strong> <?php echo $info_adicional['Direccion']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>


    </div>
        <div class="row publications">
            <h9>Publicaciones</h9>
            <?php if (count($publicaciones) > 0): ?>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <div class="col-lg-12 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title"><?php echo $publicacion['NombreServicio']; ?></h5>
                                        <p class="card-text"><?php echo $publicacion['DescripcionPublicacion']; ?></p>
                                        <p class="card-text"><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($publicacion['Fecha'])); ?></small></p>
                                    </div>
                                 
                                </div>
                           
                            </div>
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No se encontraron publicaciones.</p>
            <?php endif; ?>
        </div>
    </div>
    

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $('#editPublicacionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var descripcion = button.data('descripcion')

            var modal = $(this)
            modal.find('.modal-body #id_servicio_edit').val(id)
            modal.find('.modal-body #descripcion_edit').val(descripcion)
        })
    </script>
</body>
</html>
