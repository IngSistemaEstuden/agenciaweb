<?php
$host = "localhost";
$User = "Sander";
$pass = "77034216";
$db = "agenciaweb";



$conexion = mysqli_connect($host, $User, $pass, $db);

if (!$conexion) {
    echo "Conexion fallida";
} 
?>
