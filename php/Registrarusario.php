<?php
session_start();
include('Conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Validar y obtener datos del formulario
    $usuario = $_POST['Usuario'];
    $email = $_POST['Correo'];
    $password = $_POST['Password'];
    $confirmPassword = $_POST['ConfirmPassword'];
    $tipoUsuario = $_POST['TipoUsuario'];

    if ($password !== $confirmPassword) {
        header("Location: InicioSesion.php?error=Las contraseñas no coinciden");
        exit();
    }

    // Guardar los datos en la sesión
    $_SESSION['register'] = [
        'Usuario' => $usuario,
        'Correo' => $email,
        'Password' => $password,
        'TipoUsuario' => $tipoUsuario
    ];

    // Redirigir según el tipo de usuario
    if ($tipoUsuario === 'Empleador') {
        header("Location: RegistroEmpleador.php");
    } elseif ($tipoUsuario === 'Postulante') {
        header("Location: RegistroPostulante.php");
    } elseif ($tipoUsuario === 'Empresa') {
        header("Location: RegistroEmpresa.php");
    } else {
        header("Location: InicioSesion.php?error=Tipo de usuario no válido");
    }
    exit();
}
?>
