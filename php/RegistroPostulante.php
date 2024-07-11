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

    // Validar y obtener datos del segundo formulario
    $nombre = $_POST['nombre'];
    $apellidoP = $_POST['apellidoP'];
    $apellidoM = !empty($_POST['apellidoM']) ? $_POST['apellidoM'] : null; // Validar el segundo apellido
    $celular = $_POST['celular'];
    $direccion = $_POST['direccion'];
    $ci = $_POST['ci'];

    // Verificar si hay algún campo obligatorio vacío
    if (empty($nombre) || empty($apellidoP) || empty($celular) || empty($direccion) || empty($ci)) {
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

        // Insertar datos en la tabla postulante
        if ($apellidoM === null) {
            $sql2 = "INSERT INTO postulante (IdUsuario, Nombre, ApellidoP, Celular, Direccion, CI) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt2 = $conexion->prepare($sql2);
            if (!$stmt2) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt2->bind_param("ississ", $idUsuario, $nombre, $apellidoP, $celular, $direccion, $ci);
        } else {
            $sql2 = "INSERT INTO postulante (IdUsuario, Nombre, ApellidoP, ApellidoM, Celular, Direccion, CI) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt2 = $conexion->prepare($sql2);
            if (!$stmt2) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt2->bind_param("issssis", $idUsuario, $nombre, $apellidoP, $apellidoM, $celular, $direccion, $ci);
        }
        $stmt2->execute();

        // Confirmar la transacción
        $conexion->commit();

        // Limpiar la sesión
        unset($_SESSION['register']);

        // Mostrar mensaje de éxito y redirigir a la página de perfil de postulante
        echo "<script>alert('Registrado correctamente'); window.location.href='InicioSesion.php';</script>";
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conexion->rollback();
        echo "<script>alert('Error al registrar los datos: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Postulante</title>
    <link rel="stylesheet" href="../css/estilopostulante.css">
</head>
<body>
    <div class="container">
        <div class="imagen"></div>
        <div class="formulario">
            <h1>Registro de Postulante</h1>
            <form action="RegistroPostulante.php" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellidoP">Primer Apellido:</label>
                    <input type="text" id="apellidoP" name="apellidoP" placeholder="Ingrese su primer apellido" required>
                </div>
                <div class="form-group">
                    <label for="apellidoM">Segundo Apellido:</label>
                    <input type="text" id="apellidoM" name="apellidoM" placeholder="Ingrese su segundo apellido">
                </div>
                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input type="text" id="celular" name="celular" placeholder="Ingrese su número de celular" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" placeholder="EJ: Distrito/Barrio" required>
                </div>
                <div class="form-group">
                    <label for="ci">Cédula de Identidad:</label>
                    <input type="text" id="ci" name="ci" placeholder="Ingrese su cédula de identidad" required>
                </div>
                <button type="submit">Registrar</button>
            </form>
        </div>
    </div>
</body>
</html>