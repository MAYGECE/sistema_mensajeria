<?php
header("Cache-Control: no-store");
session_start();

date_default_timezone_set("America/Argentina/Buenos_Aires");

if (!isset($_SESSION['usuario'])) {
    header("Location:index.php");
}

require "ClasesFinal.php";


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <title>Bandeja de mensajes</title>
</head>

<body>

    <h1>Bandeja de mensajes</h1>
    <div>
        <a href="?bandejallegada"><button>Bandeja llegada de mensajes</button></a>
        <a href="?bandejasalida"><button>Bandeja de mensajes enviados</button></a>
        <a href="?crear"><button>Enviar nuevo mensaje</button></a>
    </div>
    <div class="container">



        <?php
        if (isset($_GET['crear'])) {
        ?>
        <form method="post">
            <input type="hidden" name="nuevo_mensaje">
            Para: <input type="text" name="destinatario" placeholder="Ingrese usuario" required>
            Asunto: <input type="text" name="asunto" placeholder="Ingrese asunto" maxlength=30 required>
            Mensaje: <textarea name="mensaje" rows="5" cols="40" required></textarea>
            <input type="submit" value="Enviar mensaje">
        </form>
        <?php
        }

        if (isset($_POST['nuevo_mensaje'])) {
            if ($_POST['destinatario'] == $_SESSION['idUsuario']) {
                echo "Error. Usuario emisor no puede ser igual a usuario receptor.";
                exit();
            } else {
                $mensaje = new Mensajes($_SESSION['idUsuario'], $_POST['destinatario'], $_POST['asunto'], $_POST['mensaje']);
                $mensaje->alta();
            }
        }

        if (isset($_GET['bandejallegada'])) {
            $bandeja = Mensajes::listar();
            $recibidos = $bandeja['recibidos'];

            if ($recibidos) {
                
        ?>
        <form action="" method="post">
            <table>
                <tr>
                    <th>De</th>
                    <th>Asunto</th>
                    <th>Fecha</th>
                    <th>Mensaje</th>
                    <th></th>
                </tr>
                <?php
                        foreach ($recibidos as $mensaje) {
                        ?>
                <tr class="<?= ($mensaje['estado'] == 1) ? 'estado-leido' : 'estado-noleido'; ?>">
                    <td><?php echo $mensaje['de'] ?>
                        <input type="hidden" name="emisor" value="<?php echo $mensaje['de']; ?>">
                    </td>
                    <td><?= htmlspecialchars($mensaje['asunto']) ?>
                        <input type="hidden" name="asunto_mensaje" value="<?= htmlspecialchars($mensaje['asunto']) ?>">
                    </td>
                    <td><?= htmlspecialchars($mensaje['fecha']) ?>
                        <input type="hidden" name="fecha_mensaje" value="<?= htmlspecialchars($mensaje['fecha']) ?>">
                    </td>
                    <td><?= htmlspecialchars($mensaje['mensaje']) ?>
                        <input type="hidden" name="mensaje_contenido"
                            value="<?= htmlspecialchars($mensaje['mensaje']) ?>">
                    </td>
                    <td><input type="radio" name="id" value="<?= htmlspecialchars($mensaje['idMensaje']) ?>" required>
                    </td>
                </tr>
                <?php
                        }
                        ?>
                <tr>
                    <th colspan="7">
                        <input type="submit" name="abrir" value="Abrir">
                        <input type="submit" name="responder" value="Responder">
                    </th>
                </tr>
            </table>
        </form>
        <?php
            }
        }

        
        if (isset($_POST['abrir'])) {
            if (isset($_POST['id'])) {
                // Retrieve and process the selected message
                $mensaje_abierto = new Mensajes($_POST['emisor'], $_SESSION['idUsuario'], $_POST['asunto_mensaje'], $_POST['mensaje_contenido']);
                $mensaje_abierto->modificar($_POST['id']);
            }
        }

    
        if (isset($_POST['responder'])) {
            if (isset($_POST['id'])) {
                $mensaje_a_responder = Mensajes::buscar($_POST['id']);

               
        ?>
        <form action="" method="post">
            <input type="hidden" name="respuesta_mensaje" value="1">
            <input type="hidden" name="para" value="<?php echo htmlspecialchars($mensaje_a_responder['de']); ?>">
            <input type="hidden" name="asunto" value="<?php echo htmlspecialchars($mensaje_a_responder['asunto']); ?>">
            <input type="hidden" name="id_mensaje" value="<?php echo $_POST['id']; ?>">
            Mensaje: <textarea name="respuesta" rows="5" cols="40" required></textarea>
            <input type="submit" value="Enviar mensaje">
        </form>
        <?php
            }
        }

        
        if (isset($_POST['respuesta_mensaje'])) {
            $mensaje_responder = new Mensajes($_SESSION['idUsuario'], $_POST['para'], "RE: " . $_POST['asunto'], $_POST['respuesta']);
            $mensaje_responder->alta();
        }

        if (isset($_GET['bandejasalida'])) {
            $bandeja = Mensajes::listar();
            $enviados = $bandeja['enviados'];

            if ($enviados) {
                
        ?>
        <form action="" method="post">
            <table>
                <tr>
                    <th>Para</th>
                    <th>Asunto</th>
                    <th>Fecha</th>
                    <th>Mensaje</th>
                    <th></th>
                </tr>
                <?php
                        foreach ($enviados as $mensaje) {
                        ?>
                <tr>
                    <td><?php echo htmlspecialchars($mensaje['para']) ?></td>
                    <td><?= htmlspecialchars($mensaje['asunto']) ?> </td>
                    <td><?= htmlspecialchars($mensaje['fecha']) ?></td>
                    <td><?= htmlspecialchars($mensaje['mensaje']) ?> </td>
                    <td><input type="radio" name="id" value="<?= htmlspecialchars($mensaje['idMensaje']); ?>" required>
                    </td>
                </tr>
                <tr>
                    <th colspan="7">
                        <input type="submit" value="Eliminar" name="eliminar">
                        <input type="submit" value="Reenviar" name="reenviar">
                    </th>
                </tr>
                <?php
                        }
                        ?>
            </table>
        </form>
        <?php
            }
        }

        if (isset($_POST['eliminar'])) {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                echo "ADVERTENCIA: <br>¿Está seguro que desea eliminar este mensaje ?";
        ?>
        <form method="post">
            <input type="hidden" name="codEliminar" value="<?= htmlspecialchars($id) ?>">
            <input type="submit" value="Sí" name="confirmado">
            <input type="submit" value="No" name="cancelar">
        </form>
        <?php
            } else {
                echo "No se ha seleccionado ningún usuario para eliminar.";
            }
        }

        if (isset($_POST['codEliminar']) && isset($_POST['confirmado']) && $_POST['confirmado'] == 'Sí') {
            Mensajes::eliminar($_POST['codEliminar']);
           
        }

        if (isset($_POST['reenviar'])) {
            if (isset($_POST['id'])) {
                $mensaje_reenviar = Mensajes::buscar($_POST['id']);
        ?>
        <form action="" method="post">
            <input type="hidden" name="reenviar_mensaje" value="1">
            <input type="hidden" name="asunto" value="<?php echo htmlspecialchars($mensaje_reenviar['asunto']); ?>">
            <input type="hidden" name="id_mensaje" value="<?php echo $_POST['id']; ?>">
            <input type="hidden" name="mensaje_conte"
                value="<?php echo htmlspecialchars($mensaje_reenviar['mensaje']); ?>">
            Para: <input type="text" name="destinatario" placeholder="Ingrese usuario" required>
            <input type="submit" value="Reenviar mensaje">
        </form>
        <?php
            }
        }

        if (isset($_POST['reenviar_mensaje'])) {
            if ($_POST['destinatario'] == $_SESSION['idUsuario']) {
                echo "Error. Usuario emisor no puede ser igual a usuario receptor.";
                exit();
            } else {
                $mensaje_reenviado = new Mensajes($_SESSION['idUsuario'], $_POST['destinatario'], "FWD: " . $_POST['asunto'], $_POST['mensaje_conte']);
                $mensaje_reenviado->alta();
            }
        }
        ?>

    </div>
    <div>
        <a href="?volver"><button>Volver a inicio</button></a>
    </div>
    <?php
    if (isset($_GET['volver'])) {
        if ($_SESSION['rol'] == 1) {
            header("Location:AdminFinal.php");
        }
        if ($_SESSION['rol'] == 2) {
            header("Location:UsuarioFinal.php");
        }
    }
    ?>
</body>

</html>