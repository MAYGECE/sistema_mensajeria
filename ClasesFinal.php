<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
require_once "ConexionFinal.php";

//Mejoras: 
//(1)egún la documentación en realidad para chequear si se afectaron columnas luego de una consulta select
//se deberia usar $resulset->num_rows > 0, porque affected_rows devuelve la cantidad de filas afectadas por un insert, update o delete.
//
//(2)Cerrar la conexione a la base de datos cada vez que termino de usarlas en un método $c->close();
//
//



interface abm {
    public function alta();
    public static function eliminar($id);
    public static function listar();
    public function modificar($id);
}

class Usuarios implements abm {
    private $usuario, $pass, $nombre, $apellido, $rol; 

    public function __construct($nom, $ape, $usu, $contra, $rol) {
        $this->nombre = $nom; 
        $this->apellido = $ape;
        $this->usuario = $usu;
        $this->pass = $contra;
        $this->rol = $rol;
    } 
    public static function obtenerUser($id){
        try{
            $c=conectar();
        $sql="SELECT * from usuarios where idUsuario=$id;";
        $resulset=$c->query($sql);

        if ($c->affected_rows>0){
            $user=$resulset->fetch_assoc();
         $usuario= new Usuarios($user['nombre'], $user['apellido'], $user['usuario'], $user['pass'], $user['rol']);
         return $usuario; 

        }else{
            return false; 
        }
    }catch(Trowable $e){
        return false;
    }
    }

    
    public static function iniciarSesion($usu, $contra) {
        try {
            $c = conectar();
            $sql = "SELECT * from usuarios where usuario= '$usu';";
            $resulset = $c->query($sql);

            if ($c->affected_rows > 0) {
                $user = $resulset->fetch_assoc();

                if ($user['pass'] == $contra) {
                    session_start();
                    $_SESSION['idUsuario'] = $user['idUsuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['usuario'] = $user['usuario'];
                    $_SESSION['rol'] = $user['rol'];
                    $_SESSION['tyf_actual'] = localtime(time(), true); // true para que sea array assoc;

                    switch ($_SESSION['rol']){
                        case 1: 
                            echo "¡Hola " . $usu . " y bienvenidx de nuevo!";
                            header("Location:AdminFinal.php");
                            break;
                        case 2: 
                            header("Location:UsuarioFinal.php");
                            break;
                        default:
                            echo "Error";
                            break; 
                    }
                } else {
                    echo "Contraseña incorrecta. Intente de nuevo"; 
                }
            } else {
                echo "Usuario no encontrado";
            }
        } catch (Throwable $e) {
            echo "Error" . $e->getMessage();
            exit();
        }
    }



    public static function cerrarSesion() {
        try {
            //
            // Chequeamos si las cookies se están usando
            if (ini_get("session.use_cookies")) {
                // Obtenemos los parámetros de la cookie de sesión
                $params = session_get_cookie_params();
                // Borramos la cookie
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            } 

            session_unset();
            session_destroy();
        } catch (Throwable $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function accesos($usu) {
        $usu_despe = $usu;
        $tyf_inicio = $_SESSION['tyf_actual'];  
        $tyf_fin = localtime(time(), true);

        // Si la hora de inicio es mayor a la hora de fin, significa que el usuario se conectó un día y se desconectó al otro
        if ($tyf_inicio['tm_hour'] > $tyf_fin['tm_hour']) {
            $horas_totales = $tyf_fin['tm_hour'] - $tyf_inicio['tm_hour'] + 24;
        } else {
            $horas_totales = $tyf_fin['tm_hour'] - $tyf_inicio['tm_hour'];
        }

        $m_totales = $tyf_fin['tm_min'] - $tyf_inicio['tm_min'];
        $s_totales = $tyf_fin['tm_sec'] - $tyf_inicio['tm_sec'];

        if ($m_totales < 0) {
            $horas_totales = $horas_totales - 1;
            $m_totales = $m_totales + 60;
        }

        if ($s_totales < 0) {
            $m_totales = $m_totales - 1;
            $s_totales = $s_totales + 60;
        }

        $minutos_totales = $m_totales + $horas_totales * 60; 

        $acceso = "\nNombre de usuario: " . $usu_despe . "\nFecha de ingreso: " . ($tyf_inicio['tm_year'] + 1900) . "-" . ($tyf_inicio['tm_mon'] + 1) . "-" . $tyf_inicio['tm_mday'] . "\nHora de ingreso: " . $tyf_inicio['tm_hour'] . ":" . $tyf_inicio['tm_min'] . "\nMinutos que estuvo conectado :" . round($minutos_totales, 2) . "\nSegundos que estuvo conectado :" . round($s_totales, 2) . "\n\n";
        $archivo = fopen("accesos.txt", "a");
        fwrite($archivo, $acceso);
        fclose($archivo);
        session_unset();
        session_destroy();
        exit();
    }

    public function alta() {
        try {
            $c = conectar();
            $sql = "SELECT usuario from usuarios where usuario='$this->usuario';";
            $c->query($sql);

            if ($c->affected_rows == 1) {
                echo "Ese nombre de usuario ya existe.";
                exit();
            } else {
                $sql = "INSERT INTO usuarios (nombre, apellido, usuario, pass, rol)
                        values ('$this->nombre', '$this->apellido', '$this->usuario', '$this->pass','$this->rol')";
                $c->query($sql);

                if ($c->affected_rows > 0) {
                    return true;
                    echo "Cargado con éxito usuario: " . $this->usuario;
                } else {
                    return false;
                }
            }
        } catch (Throwable $e) {
            echo "falla alta";
            echo $e->getMessage();
        }
    }

    public static function eliminar($id) {
        try {
            $c = conectar();
            $sql = "DELETE FROM usuarios where idUsuario=$id;";
            $c->query($sql);
            if ($c->affected_rows > 0) {
                echo "Se eliminó al usuario exitosa del usuario con id número: " . $id;
            } else {
                echo "No se logró eliminar.";
            }
        } catch (Throwable $e) {
            echo "Error en eliminar.usu"; 
            echo $e->getMessage();
        }
    }

    public static function listar() {
        echo "<h1>Listado de usuarios</h1>";            
        try {
            $c = conectar();
            $sql = "SELECT * from usuarios;";
            $resulset = $c->query($sql);

            if ($c->affected_rows > 0) {
                while ($user = $resulset->fetch_assoc()) {
                    $usu[] = $user;
                }
            } else {
                $usu = false;
            }
        } catch (Throwable $e) {
            echo "error en lista.usu";
            echo $e->getMessage();
            $usu = false;
        } finally {
            return $usu;
        }
    }


    public static function buscar($opcion, $busqueda) {
        try {
            $c = conectar();
            switch ($opcion) {
                case "nombre_completo":
                    $nom_compl = explode(" ", trim($busqueda));
                    if (count($nom_compl) == 2) {
                        $nom = $nom_compl[0];
                        $ape = $nom_compl[1];
                        $sql = "SELECT * from usuarios where nombre LIKE '%$nom%' OR apellido LIKE '%$ape%';";
                        break;
                    } else if ($busqueda) {
                        $sql = "SELECT * from usuarios where nombre LIKE '%$busqueda%' OR apellido LIKE '%$busqueda%';";
                        break;
                    }
                case "nombre_usuario":
                    $sql = "SELECT * from usuarios where usuario LIKE '%$busqueda%';";
                    break;
                default:
                    echo "Opción de búsqueda no váida";
                    break;
            }

            $resulset = $c->query($sql);
            $usu = array();

            if ($c->affected_rows > 0) {
                while ($user = $resulset->fetch_assoc()) {
                    $usu[] = $user; 
                }
            } else {
                $usu = false;   
            }
        } catch (Throwable $e) {
            $usu = false;
            echo "Error en buscar.usu"; 
            echo $e->getMessage();
        } finally {
            return $usu;
        }
    }

    public function modificar($id) {
        try {
            $c = conectar();
            $sql = "select * from usuarios where idUsuario='$id';";
            $c->query($sql);
            if ($resulset = $c->affected_rows > 0) {
                $sql = "UPDATE usuarios SET nombre='$this->nombre', apellido='$this->apellido', usuario='$this->usuario', pass='$this->pass', rol=$this->rol WHERE idUsuario=$id;";
                $c->query($sql);
                if ($c->affected_rows > 0) {
                    echo "Modificación exitosa de usuario: " . $this->usuario;
                } else {
                    echo "No se logró modificar.";
                }
            }
        } catch (Throwable $e) {
            echo "Error en modificar"; 
            echo $e->getMessage();
        }
    }
}

class Mensajes implements abm {
    private $de, $para, $asunto, $mensaje, $origen, $estado;

    public function __construct($de, $para, $asunto, $mensaje, $origen=0, $estado = 0) {
        $this->de = $de; 
        $this->para = $para;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->origen = $origen;
        $this->estado = $estado;
    }

    public function alta() {
        try {
            $c = conectar();
            if (is_numeric($this->para)) {
                $sql = "SELECT idUsuario FROM usuarios WHERE idUsuario = $this->para;";
            } else {
                $sql = "SELECT idUsuario FROM usuarios WHERE usuario = '$this->para';";
            }

            $resulset = $c->query($sql);

            if ($c->affected_rows > 0) {
                $para_definido = $resulset->fetch_assoc();

                if (isset($para_definido['idUsuario'])) {
                    $this->para = $para_definido['idUsuario'];
                } else {
                    echo "No me trae el id";
                    exit();
                }

                $sql = "INSERT INTO mensajes (de, para, asunto, mensaje, fecha, estado) 
                        VALUES (
                            $this->de, 
                            $this->para, 
                            '$this->asunto', 
                            '" . $c->real_escape_string($this->mensaje) . "',  
                            NOW(), 
                            $this->estado
                        );";
                $resulset = $c->query($sql);

                if ($c->affected_rows > 0) {
                    echo "Mensaje enviado con éxito";
                } else {
                    echo "Error al enviar el mensaje";
                }
            } else {
                echo "No existe ese destinatario";
            } 
        } catch (Throwable $e) {
            echo "falla alta mensaje";
            echo $e->getMessage();
        }
    }

    public static function eliminar($id) {
        try {
            // Bueno, decidí cambiar el estado a 2 en vez de eliminarlo de la base de datos, asi al otro usuario 
            //le sigue apareciendo el mensaje. 
            $c = conectar();
            $sql = "UPDATE mensajes SET estado=2 WHERE idMensaje=$id;";
            $c->query($sql);
            if ($c->affected_rows > 0) {
                echo "Se eliminó el mensaje con éxito";
            } else {
                echo "No se logró eliminar.";
            }
        } catch (Throwable $e) {
            echo "fallamos en elimi.mensaje"; 
            echo $e->getMessage();
        }
    }

    public static function listar() {
        try {
            $c = conectar();
            $sql = "SELECT idMensaje, de, asunto, fecha, mensaje, estado FROM mensajes WHERE para = " . $_SESSION['idUsuario'] . ";";
            $resulset = $c->query($sql);
            $mensajes_recibidos = [];
            $mensajes_enviados = [];
            if ($resulset && $resulset->num_rows > 0) {
                while ($recibidos = $resulset->fetch_assoc()) {
                    if ($recibidos['estado'] !=2){
                    $mensajes_recibidos[] = $recibidos;
                }}
            }

            $sql = "SELECT idMensaje, para, asunto, fecha, mensaje,estado FROM mensajes WHERE de = " . $_SESSION['idUsuario'] . ";";
            $resulset = $c->query($sql);
            if ($resulset && $resulset->num_rows > 0) {
                while ($enviados = $resulset->fetch_assoc()) {
                    if($enviados['estado'] != 2){
                    $mensajes_enviados[] = $enviados;
                }}
            }
        } catch (Throwable $e) {
            echo "fallamos en lista.mensajes";
            echo $e->getMessage();
            return ['recibidos' => [], 'enviados' => []];
        } finally {
            return ['recibidos' => $mensajes_recibidos, 'enviados' => $mensajes_enviados];
        }
    }

    public static function buscar($id) {
        try {
            $c = conectar();
            if ($id){
                $sql= "SELECT de, asunto, fecha, mensaje, estado FROM mensajes WHERE idMensaje = $id;";
                $resulset = $c->query($sql);
                $mensaje = array();
            }

            if ($c->affected_rows > 0) {
                $mensaje = $resulset->fetch_assoc();
            } else {
                $mensaje = false;   
            }
        } catch (Throwable $e) {
            $usu = false;
            echo "Error en buscar.mensaje"; 
            echo $e->getMessage();
        } finally {
            return $mensaje;
        }
    }

    public function modificar($id) {
        try {
            $c = conectar();
            $sql = "SELECT * FROM mensajes WHERE idMensaje=$id;";
            $resulset = $c->query($sql);

            if ($c->affected_rows > 0) {
                $msg = $resulset->fetch_assoc();
                //var_dump($msg);

                if (isset($msg['estado']) && $msg['estado'] == 0) {
                    //Cuando ejecutaba el update, se me actualizaba la fecha automáticamente desde el workbench
                    //para evitar perder la fecha de envio real, hice un update de la fecha con la misma fecha que ya tenía
                    //porque en la base de datos tenía "update on current timestamp" 
                    $sql = "UPDATE mensajes SET estado=1, fecha=fecha WHERE idMensaje=$id;";
                    $c->query($sql);

                    if ($c->affected_rows > 0) {
                        echo "Mensaje cambia estado a leído";
                    } else { 
                        echo "No se logró modificar.";
                    }
                } else { 
                    echo "El mensaje ya fue abierto.";
                }
            } else {
                echo "No se encontró el mensaje.";
            }
        } catch (Throwable $e) {
            echo "Error en modificar.estado.mensaje: "; 
            echo $e->getMessage(); 
        }
    }
}

//*Diccionario de funciones*
// localtime(time(), true); devuekve un array asociativo con la fecha y hora actual. 
//¿cuáles son los índices de esas claves? "tm_sec" - seconds, 0 to 59
//"tm_min" - minutes, 0 to 59
//"tm_hour" - hours, 0 to 23
//"tm_mday" - day of the month, 1 to 31
//"tm_mon" - month of the year, 0 (Jan) to 11 (Dec)
//"tm_year" - years since 1900
//"tm_wday" - day of the week, 0 (Sun) to 6 (Sat)
//"tm_yday" - day of the year, 0 to 365
//"tm_isdst" - is daylight savings time in effect? Positive if yes, 0 if not, negative if unknown.

//2)) real_escape_string();
//Toma un string y lo convierte en un string con caracteres especiales escapados, lo traduce a ASCII y lo guarda en sql.  














?>