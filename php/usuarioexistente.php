<?php
include('../php/Conexion.php');

$respuesta = [];

if (isset($_POST['Usuario'])) {
    $usuario = trim($_POST['Usuario']);
    $stmt = $conexion->prepare("SELECT * FROM usuario WHERE Usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $respuesta['usuario'] = 'existe';
    } else {
        $respuesta['usuario'] = 'no_existe';
    }
}

if (isset($_POST['Correo'])) {
    $correo = trim($_POST['Correo']);
    $stmt = $conexion->prepare("SELECT * FROM usuario WHERE Correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $respuesta['correo'] = 'existe';
    } else {
        $respuesta['correo'] = 'no_existe';
    }
}

echo json_encode($respuesta);
?>
