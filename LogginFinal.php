<?php
header("Cache-Control: no-store");

require_once "ClasesFinal.php";
require_once "index.php";

if (isset($_POST['initiateSession'])){
$usuario=$_POST['usuario'];
$contraseña=$_POST['pass'];
Usuarios::iniciarSesion($usuario, $contraseña);


}





?>