<?php
function conectar()
{
    try {
        $c = new Mysqli("localhost", "root", "", "georginaCelanitp");
        return $c;

    } catch (Throwable $e) {
        echo "Error en el servidor";
        ?><ahref="index.php">Aceptar</a><?php
        exit();


    }
}

?>