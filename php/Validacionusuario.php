<?php
session_start();
include('../php/Conexion.php');

class InicioSesion {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    private function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function autenticar($usuario, $clave) {
        $usuario = $this->validate($usuario);
        $clave = $this->validate($clave);

        if (empty($usuario)) {
            header("Location: ../InicioSesion.php?error=El Usuario Es Requerido");
            exit();
        } elseif (empty($clave)) {
            header("Location: ../InicioSesion.php?error=La clave Es Requerida");
            exit();
        } else {
            $sql = "SELECT * FROM usuario WHERE Usuario = '$usuario' AND Password='$clave'";
            $result = mysqli_query($this->conexion, $sql);

            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                if ($row['Usuario'] === $usuario && $row['Password'] === $clave) {
                    $_SESSION['Usuario'] = $row['Usuario'];
                    $_SESSION['Tipo_Usuario'] = $row['Tipo_Usuario'];
                    $_SESSION['IdUsuario'] = $row['IdUsuario'];

                    switch ($row['Tipo_Usuario']) {
                        case 'Admin':
                            header('Location: ../kaiadmin-lite-1.0.0/PaginaAdmin.php');
                            break;
                        case 'Empleador':
                            header("Location: ../php/PaginaPerfilEmpleador.php");
                            break;
                        case 'Postulante':
                            header("Location: ../Php/PaginaPerfilPostulante.php");
                            break;
                        case 'Empresa':
                            header("Location: ../php/PaginaPerfilEmpresa.php");
                            break;
                        default:
                            header("Location: ../php/InicioSesion.php?error=Tipo de usuario desconocido");
                            break;
                    }
                    exit();
                } else {
                    header("Location: ../php/InicioSesion.php?error=El usuario o la clave son incorrectas");
                    exit();
                }
            } else {
                header("Location: ../php/InicioSesion.php?error=El usuario o la clave son incorrectas");
                exit();
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Usuario']) && isset($_POST['Password'])) {
    $inicioSesion = new InicioSesion($conexion);
    $inicioSesion->autenticar($_POST['Usuario'], $_POST['Password']);
}
?>