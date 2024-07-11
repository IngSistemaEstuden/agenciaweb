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

// Definir la imagen de perfil
$imagen_perfil = !empty($info_adicional['Foto']) ? $info_adicional['Foto'] : '../img/SinPerfil.png';

// Formatear la URL de Google Drive para que sea una URL directa a la imagen
if (strpos($imagen_perfil, 'drive.google.com') !== false) {
    preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $imagen_perfil, $matches);
    if (isset($matches[1])) {
        $imagen_perfil = 'https://drive.google.com/uc?export=view&id=' . $matches[1];
    }
}

// Manejar la carga de imagen de perfil
if (isset($_POST["submit_image"])) {
    if ($_FILES["image"]["error"] == 4) {
        echo "<script>alert('Imagen no encontrada');</script>";
    } else {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = explode('.', $fileName);
        $imageExtension = strtolower(end($imageExtension));
        if (!in_array($imageExtension, $validImageExtension)) {
            echo "<script>alert('Extensión de imagen inválida');</script>";
        } else if($fileSize > 10000000) {  // 10 MB
            echo "<script>alert('El tamaño de la imagen es muy grande');</script>";
        } else {
            $newImageName = uniqid();
            $newImageName .= '.' . $imageExtension;

            if (move_uploaded_file($tmpName, '../img/' . $newImageName)) {
                // Eliminar la imagen anterior si existe y no es la imagen por defecto
                if ($imagen_perfil != '../img/SinPerfil.png' && file_exists($imagen_perfil)) {
                    unlink($imagen_perfil);
                }

                // Actualizar la base de datos con la nueva URL de la imagen
                $query = "UPDATE empleador SET Foto = '../img/$newImageName' WHERE IdUsuario = $IdUsuario";
                if (mysqli_query($conexion, $query)) {
                    echo "<script>alert('Imagen actualizada con éxito'); document.location.href = 'PaginaPerfilEmpleador.php';</script>";
                } else {
                    echo "<script>alert('Error al actualizar la base de datos');</script>";
                }
            } else {
                echo "<script>alert('Error al mover el archivo subido');</script>";
            }
        }
    }
}


// Manejar la actualización de la información del perfil
if (isset($_POST["submit_info"])) {
    $nombre = $_POST['nombre'];
    $apellidoP = $_POST['apellidoP'];
    $apellidoM = $_POST['apellidoM'];
    $correo = $_POST['correo'];
    $celular = $_POST['celular'];
    $direccion = $_POST['direccion'];
    $descripcion = $_POST['descripcion'];

    // Asegurarse de que IdUsuario esté correctamente rodeado de comillas
    $query = "UPDATE empleador SET Nombre='$nombre', ApellidoP='$apellidoP', ApellidoM='$apellidoM', Celular='$celular', Direccion='$direccion', Descripcion='$descripcion' WHERE IdUsuario='$IdUsuario'";
    $query2 = "UPDATE usuario SET Correo='$correo' WHERE IdUsuario='$IdUsuario'";

    if (mysqli_query($conexion, $query) && mysqli_query($conexion, $query2)) {
        echo "<script>alert('Información actualizada con éxito'); document.location.href = 'PaginaPerfilEmpleador.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la información');</script>";
    }
}




// Manejar la publicación de un servicio
if (isset($_POST["submit_servicio"])) {
    $tipo_categoria = $_POST['tipo_categoria'];
    $nombre_servicio = $_POST['nombre_servicio'];
    $descripcion = $_POST['descripcion'];
     // Configurar la zona horaria a la hora local de Bolivia (La Paz)
     date_default_timezone_set('America/La_Paz');
     $fecha = date('Y-m-d H:i:s');
    // Primero, insertar el nuevo servicio en la tabla servicios
    $query_servicio = "INSERT INTO servicios (IdCategoria, NombreServicio) VALUES ('$tipo_categoria', '$nombre_servicio')";
    if (mysqli_query($conexion, $query_servicio)) {
        // Obtener el id del servicio recién insertado
        $id_servicio = mysqli_insert_id($conexion);

        // Insertar la publicación en la tabla publicaciones
        $query_publicacion = "INSERT INTO publicaciones (IdServicios, IdUsuario, Fecha, DescripcionPublicacion, TipoPublicacion) VALUES ('$id_servicio', '$IdUsuario', '$fecha', '$descripcion', 'Oferta')";
        if (mysqli_query($conexion, $query_publicacion)) {
            echo "<script>alert('Servicio publicado con éxito'); document.location.href = 'PaginaPerfilEmpleador.php';</script>";
        } else {
            echo "<script>alert('Error al publicar el Oferta');</script>";
        }
    } else {
        echo "<script>alert('Error al registrar el Oferta');</script>";
    }
}

// Manejar la eliminación de una publicación
if (isset($_POST["delete_publicacion"])) {
    $id_servicio = $_POST['id_servicio'];
    $query_delete = "DELETE FROM publicaciones WHERE IdServicios = '$id_servicio' AND IdUsuario = '$IdUsuario'";
    if (mysqli_query($conexion, $query_delete)) {
        echo "<script>alert('Publicación eliminada con éxito'); document.location.href = 'PaginaPerfilEmpleador.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar la publicación');</script>";
    }
}

// Manejar la edición de una publicación
if (isset($_POST["edit_publicacion"])) {
    $id_servicio = $_POST['id_servicio'];
    $descripcion = $_POST['descripcion'];
    $query_edit = "UPDATE publicaciones SET DescripcionPublicacion = '$descripcion' WHERE IdServicios = '$id_servicio' AND IdUsuario = '$IdUsuario'";
    if (mysqli_query($conexion, $query_edit)) {
        echo "<script>alert('Publicación editada con éxito'); document.location.href = 'PaginaPerfilEmpleador.php';</script>";
    } else {
        echo "<script>alert('Error al editar la publicación');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Agencia web Yapacani</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon"/>
    <!-- CSS Files -->

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/estiloperfilempleador.css"/>
    <style>
        .card-title {
         

        .card-titl

        .card-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title .btn {
            margin-left: 10px;
        }

        .card-title div {
            display: flex;
            align-items: center;
        }
    }
    </style>


</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="PaginaPerfilEmpleador.php">MENU</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="PaginaPublicacionesServicios.php">Publicaciones</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-toggle="modal" data-target="#publicarModal">Publicar</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-toggle="modal" data-target="#editInfoModal">Editar Información</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php">Salir</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="row">
    <div class="col-lg-12 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                    <img src="<?php echo $imagen_perfil; ?>" alt="Foto de perfil" class="avatar-img" data-toggle="modal" data-target="#uploadModal">
                        <div class="ml-3">
                            <h3 class="mb-0"><?php echo $info_adicional['Nombre'] . ' ' . $info_adicional['ApellidoP'] . ' ' . $info_adicional['ApellidoM']; ?></h3>
                            <p class="text-muted"><?php echo $info_adicional['Descripcion']; ?></p>
                        </div>
                    </div>
                
                </div>
                <hr>
                <div class="profile-info">
                    <h4>Información</h4>
                    <p><strong>Correo Electrónico:</strong> <?php echo $usuario['Correo']; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $info_adicional['Celular']; ?></p>
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
                                <div>
                                        <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#editPublicacionModal" data-id="<?php echo $publicacion['IdServicios']; ?>" data-descripcion="<?php echo $publicacion['DescripcionPublicacion']; ?>">Editar</button>
                                        <form action="" method="post" style="display:inline;">
                                            <input type="hidden" name="id_servicio" value="<?php echo $publicacion['IdServicios']; ?>">
                                            <button type="submit" name="delete_publicacion" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
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

    <!-- Modal para subir la imagen de perfil -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Cambiar Foto de Perfil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="image"style="color: black;">Seleccionar imagen:</label>
                            <input type="file" name="image" id="image" class="form-control" accept=".jpg, .jpeg, .png" required>
                        </div>
                        <button type="submit" name="submit_image" class="btn btn-primary">Subir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para editar la información del perfil -->
    <div class="modal fade" id="editInfoModal" tabindex="-1" role="dialog" aria-labelledby="editInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInfoModalLabel">Editar Información</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="nombre"style="color: black;">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $info_adicional['Nombre']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="apellidoP"style="color: black;">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellidoP" name="apellidoP" value="<?php echo $info_adicional['ApellidoP']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="apellidoM"style="color: black;">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellidoM" name="apellidoM" value="<?php echo $info_adicional['ApellidoM']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="correo"style="color: black;">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $usuario['Correo']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="celular"style="color: black;">Celular</label>
                            <input type="text" class="form-control" id="celular" name="celular" value="<?php echo $info_adicional['Celular']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="direccion"style="color: black;">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $info_adicional['Direccion']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion"style="color: black;">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo $info_adicional['Descripcion']; ?></textarea>
                        </div>
                        <button type="submit" name="submit_info" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para publicar Oferta -->
    <div class="modal fade" id="publicarModal" tabindex="-1" role="dialog" aria-labelledby="publicarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="publicarModalLabel">Publicar Oferta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="tipo_categoria"style="color: black;">Tipo de Categoría</label>
                            <select class="form-control" id="tipo_categoria" name="tipo_categoria" required>
                                <?php
                                $sql_categorias = "SELECT * FROM categorias";
                                $result_categorias = mysqli_query($conexion, $sql_categorias);
                                while ($row = mysqli_fetch_assoc($result_categorias)) {
                                    echo "<option value='{$row['IdCategoria']}'>{$row['TipoDeCategoria']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nombre_servicio"style="color: black;">Nombre del Servicio</label>
                            <input type="text" class="form-control" id="nombre_servicio" name="nombre_servicio" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion"style="color: black;">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                        </div>
                        <button type="submit" name="submit_servicio" class="btn btn-primary">Publicar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar publicación -->
    <div class="modal fade" id="editPublicacionModal" tabindex="-1" role="dialog" aria-labelledby="editPublicacionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPublicacionModalLabel">Editar Publicación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <input type="hidden" id="id_servicio_edit" name="id_servicio">
                        <div class="form-group">
                            <label for="descripcion_edit"style="color: black;">Descripción</label>
                            <textarea class="form-control" id="descripcion_edit" name="descripcion" required></textarea>
                        </div>
                        <button type="submit" name="edit_publicacion" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
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