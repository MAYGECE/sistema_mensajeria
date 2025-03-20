<?php
header("Cache-Control: no-store");
require_once "ClasesFinal.php";
session_start();


if(!isset($_SESSION['usuario'])){
    header("Location:index.php");
    exit();
}

$usu=$_SESSION['usuario'];
   
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión cerrada</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>


    <a href='index.php'><button>Iniciar Sesion </button></a>

    <?php

 echo "Adios ".$usu." ¡Nos vemos pronto!";
 Usuarios::accesos($usu);
 Usuarios::cerrarSesion();
 
 
 
 


    ?>
</body>

</html>