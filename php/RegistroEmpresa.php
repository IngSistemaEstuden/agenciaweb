<?php
session_start();
include('Conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar que los datos del primer formulario estén en la sesión
    if (!isset($_SESSION['register'])) {
        echo "<script>alert('No se ha completado el primer formulario'); window.location.href='../php/InicioSesion.php';</script>";
        exit();
    }

    // Obtener los datos del primer formulario desde la sesión
    $registro = $_SESSION['register'];
    $usuario = $registro['Usuario'];
    $email = $registro['Correo'];
    $password = $registro['Password'];
    $tipoUsuario = $registro['TipoUsuario'];

    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $NIT = $_POST['NIT'];
    $telefono = $_POST['telefono'];

    // Verificar si hay algún campo obligatorio vacío
    if (empty($nombre) || empty($direccion) || empty($NIT) || empty($telefono)) {
        echo "<script>alert('Por favor, complete todos los campos obligatorios.');</script>";
        exit();
    }

    // Iniciar una transacción
    $conexion->begin_transaction();

    try {
        // Insertar datos en la tabla usuarios
        $sql1 = "INSERT INTO usuario (Usuario, Correo, Password, Tipo_Usuario) VALUES (?, ?, ?, ?)";
        $stmt1 = $conexion->prepare($sql1);
        if (!$stmt1) {
            throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt1->bind_param("ssss", $usuario, $email, $password, $tipoUsuario);
        $stmt1->execute();

        // Obtener el ID del usuario recién insertado
        $idUsuario = $stmt1->insert_id;

        // Insertar datos en la tabla empresa
        $sql2 = "INSERT INTO empresa (IdUsuario, NombreEmpresa, Direccion, NIT, TelefonoCelular) VALUES (?, ?, ?, ?, ?)";
        $stmt2 = $conexion->prepare($sql2);
        if (!$stmt2) {
            throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt2->bind_param("issss", $idUsuario, $nombre, $direccion, $NIT, $telefono);
        $stmt2->execute();

        // Confirmar la transacción
        $conexion->commit();

        // Limpiar la sesión
        unset($_SESSION['register']);

        // Mostrar mensaje de éxito y redirigir a la página de inicio de sesión
        echo "<script>alert('Empresa registrada correctamente'); window.location.href='InicioSesion.php';</script>";
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conexion->rollback();
        echo "<script>alert('Error al registrar la empresa: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Empresa</title>
    <link rel="stylesheet" href="../css/EstiloEmpresa.css">
</head>
<body>
    <div class="container">
        <div class="imagen"></div>
        <div class="formulario">
            <h1>Registro Empresa</h1>
            <form action="RegistroEmpresa.php" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre de la Empresa:</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre de la empresa" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Ingrese la dirección" required>
                </div>
                <div class="form-group">
                    <label for="NIT">NIT:</label>
                    <input type="text" id="NIT" name="NIT" placeholder="Ingrese el NIT">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" placeholder="Ingrese el número de teléfono" required>
                </div>
                <button type="submit">Registrar</button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
