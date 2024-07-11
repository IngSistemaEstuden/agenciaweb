<?php
session_start();
include('../php/conexion.php'); // Incluir el archivo de conexión

if (!isset($_SESSION['IdUsuario'])) {
    header("Location: InicioSesion.php");
    exit();
}

// Consulta para obtener las categorías
$sqlCategorias = "SELECT TipoDeCategoria FROM categorias";
$resultCategorias = $conexion->query($sqlCategorias);

// Obtener la categoría seleccionada de la URL
$categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Consulta para obtener las publicaciones de tipo 'Servicio'
$sql = "
    SELECT 
        p.IdServicios,
        u.IdUsuario,
        CONCAT(e.Nombre, ' ', e.ApellidoP, ' ', e.ApellidoM) AS NombreCompleto,
        ser.NombreServicio, 
        c.TipoDeCategoria,
        p.DescripcionPublicacion, 
        p.Fecha 
    FROM 
        publicaciones p
    JOIN 
        usuario u ON p.IdUsuario = u.IdUsuario
    JOIN 
        postulante e ON e.IdUsuario = u.IdUsuario
    JOIN 
        servicios ser ON p.IdServicios = ser.IdServicios
    JOIN 
        categorias c ON ser.IdCategoria = c.IdCategoria
    WHERE 
        p.TipoPublicacion = 'Servicio'
";

if (!empty($categoriaSeleccionada)) {
    $sql .= " AND c.TipoDeCategoria = '$categoriaSeleccionada'";
}

$sql .= " ORDER BY p.Fecha DESC";

$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleos de trabajo en Santa Cruz</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilopaginaPublicacionesServicio.css">
       
</head>
<body>
    <!-- Menú de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MENU</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto"> <!-- ml-auto alinea a la derecha -->
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="tooltip" title="Cerrar sesión">Salir</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h1 class="mb-4 title-white"><a href="PaginaPublicacionesServicios.php">Publicaciones</a></h1> <!-- Título convertido en enlace -->
        <div class="row">
            <div class="col-md-3">
                <div class="sidebar">
                    <h4><a href="PaginaPublicacionesServicios.php">Categorías</a></h4>
                    <?php if ($resultCategorias->num_rows > 0): ?>
                        <?php while($rowCategoria = $resultCategorias->fetch_assoc()): ?>
                            <a href="?categoria=<?php echo urlencode($rowCategoria['TipoDeCategoria']); ?>" class="btn btn-category"><?php echo $rowCategoria['TipoDeCategoria']; ?></a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No hay categorías disponibles.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-9">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="job-card">
                            <p><strong>Nombre: </strong><a href="vistaPerfilPostulante.php?IdUsuario=<?php echo $row['IdUsuario']; ?>" class="btn btn-link"><?php echo $row['NombreCompleto']; ?></a></p>
                            <h5><?php echo $row['DescripcionPublicacion']; ?></h5>
                            <p><strong>Servicio: </strong><?php echo $row['NombreServicio']; ?></p>
                            <p><strong>Categoría: </strong><?php echo $row['TipoDeCategoria']; ?></p>
                            <p><small><?php echo date('d/M/Y', strtotime($row['Fecha'])); ?></small></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No se encontraron publicaciones de servicios.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>

<?php
$conexion->close();
?>