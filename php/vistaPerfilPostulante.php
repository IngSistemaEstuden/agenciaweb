<?php
session_start();
include('../php/conexion.php');

if (!isset($_GET['IdUsuario'])) {
    echo "No se ha especificado un usuario.";
    exit();
}

$IdUsuario = $_GET['IdUsuario'];

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
    <link rel="stylesheet" href="../css/estiloperfilpostulante.css"/>
</head>
<body>

    <div class="container profile-container">
        <div class="row">
            <div class="col-lg-4 profile-left">
                <div class="profile-picture">
                    <img src="<?php echo isset($info_adicional['Foto']) ? $info_adicional['Foto'] : '../img/SinPerfil.png'; ?>" alt="Foto de perfil" class="avatar-img" data-toggle="modal" data-target="#uploadModal">
                    <div class="name"><?php echo $info_adicional['Nombre'] . ' ' . $info_adicional['ApellidoP'] . ' ' . $info_adicional['ApellidoM']; ?></div>
                    <div class="description"><?php echo $info_adicional['Descripcion']; ?></div>
                </div>
                <div class="profile-info">
                    <h1>Información</h1>
                    <p><strong>Correo Electrónico:</strong> <?php echo $usuario['Correo']; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $info_adicional['Celular']; ?></p>
                    <p><strong>Dirección:</strong> <?php echo $info_adicional['Direccion']; ?></p>
                </div>
            </div>
            <div class="col-lg-8 profile-right">
                <div class="cv-section">
                    <div class="cv-title">Currículum Vitae</div>
                    <div class="cv-content text-center">
                        <?php if (!empty($info_adicional['CV'])): ?>
                            <a href="#" data-toggle="modal" data-target="#uploadCvModal">
            
                                <img src="../img/CV/"alt="Miniatura del CV" class="img-thumbnail" width="200px" height="auto">
                            </a>
                        <?php else: ?>
                            <a href="#" data-toggle="modal" data-target="#uploadCvModal">
                                <p>Ver CV</p>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($info_adicional['CV'])): ?>
                        <a href="<?php echo $info_adicional['CV']; ?>" class="btn btn-primary mt-3" download>Descargar Currículum</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row publications">
            <h1>Publicaciones</h1>
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
</body>
</html>
