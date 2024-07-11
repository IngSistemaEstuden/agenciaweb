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

// Definir la imagen de perfil
$imagen_perfil = !empty($info_adicional['Foto']) ? $info_adicional['Foto'] : '../img/SinPerfil.png';

// Formatear la URL de Google Drive para que sea una URL directa a la imagen
if (strpos($imagen_perfil, 'drive.google.com') !== false) {
    preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $imagen_perfil, $matches);
    if (isset($matches[1])) {
        $imagen_perfil = 'https://drive.google.com/uc?export=view&id=' . $matches[1];
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
    <style>
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
.mb-0{
    color:black; /* Color del texto */
    font-weight: bold; /* Texto en negrita */
    margin-bottom: 30px; /* Espacio inferior */
    font-size: 16px; /* Tamaño de fuente */
    text-transform: uppercase; /* Texto en mayúsculas */
    letter-spacing: 2px; /* Espaciado entre letras */
    background: linear-gradient(to right, #ffffff00, #0153a500); /* Fondo degradado */
}
h1{
    color: #ffffff; /* Color del texto */
    font-weight: bold; /* Texto en negrita */
    text-align: center; /* Centrar el texto */
    margin-bottom: 30px; /* Espacio inferior */
    font-size: 36px; /* Tamaño de fuente */
    text-transform: uppercase; /* Texto en mayúsculas */
    letter-spacing: 3.5px; /* Espaciado entre letras */
    background: linear-gradient(to right, #ffffff, #ffffff); /* Fondo degradado */
    -webkit-background-clip: text; /* Solo usar el fondo para el texto */
    -webkit-text-fill-color: transparent; /* Relleno de color transparente */
    padding: 10px; /* Relleno */
    border-radius: 10px; /* Bordes redondeados */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.334); /* Sombra */
}
        
    </style>
</head>
<body>

    <div class="container profile-container">
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
