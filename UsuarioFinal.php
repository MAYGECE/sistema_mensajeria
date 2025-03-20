<?php
header("Cache-Control: no-store");
session_start();
date_default_timezone_set("America/Argentina/Buenos_Aires");

require_once "ClasesFinal.php";

// var_dump($_SESSION); 

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 2){
    header("Location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario</title>
    <link rel="stylesheet" href="stylesheet.css">

</head>


<body>
    <div class="header">

        <?php
  
  echo "¡Hola ".$_SESSION['usuario']." y bienvenidx de nuevo!";
  //var_dump($_COOKIE)?>

        <button onclick="window.location.href='LogOut.php';">Cerrar sesión</button>
    </div>

    <div class="mensajeria">
        <a href="mensajes.php"><button>Bandeja de mensajes</button></a>
    </div>



    </div>


</body>

</html>