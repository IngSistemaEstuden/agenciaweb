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

// Consulta para obtener las publicaciones de empleadores
$sqlEmpleadores = "
    SELECT 
        p.IdServicios,
        u.IdUsuario,
        CONCAT(e.Nombre, ' ', e.ApellidoP, ' ', e.ApellidoM) AS NombreCompleto,
        ser.NombreServicio, 
        c.TipoDeCategoria,
        p.DescripcionPublicacion, 
        p.Fecha,
        'Empleador' as TipoUsuario
    FROM 
        publicaciones p
    JOIN 
        usuario u ON p.IdUsuario = u.IdUsuario
    JOIN 
        empleador e ON e.IdUsuario = u.IdUsuario
    JOIN 
        servicios ser ON p.IdServicios = ser.IdServicios
    JOIN 
        categorias c ON ser.IdCategoria = c.IdCategoria
    WHERE 
        p.TipoPublicacion = 'Oferta'
";

if (!empty($categoriaSeleccionada)) {
    $sqlEmpleadores .= " AND c.TipoDeCategoria = '$categoriaSeleccionada'";
}

$sqlEmpleadores .= " ORDER BY p.Fecha DESC";

// Consulta para obtener las publicaciones de empresas
$sqlEmpresas = "
    SELECT 
        p.IdServicios,
        u.IdUsuario,
        e.NombreEmpresa AS NombreCompleto,
        ser.NombreServicio, 
        c.TipoDeCategoria,
        p.DescripcionPublicacion, 
        p.Fecha,
        'Empresa' as TipoUsuario
    FROM 
        publicaciones p
    JOIN 
        usuario u ON p.IdUsuario = u.IdUsuario
    JOIN 
        empresa e ON e.IdUsuario = u.IdUsuario
    JOIN 
        servicios ser ON p.IdServicios = ser.IdServicios
    JOIN 
        categorias c ON ser.IdCategoria = c.IdCategoria
    WHERE 
        p.TipoPublicacion = 'Oferta'
";

if (!empty($categoriaSeleccionada)) {
    $sqlEmpresas .= " AND c.TipoDeCategoria = '$categoriaSeleccionada'";
}

$sqlEmpresas .= " ORDER BY p.Fecha DESC";

$resultEmpleadores = $conexion->query($sqlEmpleadores);
$resultEmpresas = $conexion->query($sqlEmpresas);

$publicaciones = [];
while ($row = $resultEmpleadores->fetch_assoc()) {
    $publicaciones[] = $row;
}
while ($row = $resultEmpresas->fetch_assoc()) {
    $publicaciones[] = $row;
}

// Intercalar publicaciones de empleadores y empresas
usort($publicaciones, function($a, $b) {
    return strtotime($b['Fecha']) - strtotime($a['Fecha']);
});
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
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3 sidebar">
                <h4><a href="PaginaPublicacionesOfertas.php">Categorías</a></h4>
                <?php if ($resultCategorias->num_rows > 0): ?>
                    <?php while($rowCategoria = $resultCategorias->fetch_assoc()): ?>
                        <a href="?categoria=<?php echo urlencode($rowCategoria['TipoDeCategoria']); ?>" class="btn btn-category"><?php echo $rowCategoria['TipoDeCategoria']; ?></a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay categorías disponibles.</p>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <h1 class="mb-4">Publicaciones</h1>
                <?php if (count($publicaciones) > 0): ?>
                    <?php foreach ($publicaciones as $row): ?>
                        <div class="job-card">
                            <p><strong><?php echo ($row['TipoUsuario'] == 'Empresa') ? 'Empresa: ' : 'Empleador: '; ?></strong>
                            <a href="<?php echo ($row['TipoUsuario'] == 'Empresa') ? 'vistaPerfilEmpresa.php' : 'vistaPerfilEmpleador.php'; ?>?IdUsuario=<?php echo $row['IdUsuario']; ?>" class="btn btn-link"><?php echo $row['NombreCompleto']; ?></a></p>
                            <h5><?php echo $row['DescripcionPublicacion']; ?></h5>
                            <p><strong>Servicio: </strong><?php echo $row['NombreServicio']; ?></p>
                            <p><strong>Categoría: </strong><?php echo $row['TipoDeCategoria']; ?></p>
                            <p><small><?php echo date('d/M/Y', strtotime($row['Fecha'])); ?></small></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No se encontraron ofertas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.amazonaws.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
