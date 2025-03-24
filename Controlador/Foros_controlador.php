<?php

require_once '../Config/config.php';
require_once '../Modelo/Foro.php';
require_once '../Modelo/Notificacion.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$forosModelo = new Foro($db);
$NotificacionModelo = new Notificacion($db);



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['CrearForo'])) {
        $DatosForo = [
            'titulo' => $_POST['titulo'],
            'descripcion' => $_POST['descripcion'],
            'creador' => $_SESSION['nick'],
            'suscriptores' =>[$_SESSION['nick']],
            'mensajes' => []
        ];
        $resultado = $forosModelo->crearForo($DatosForo);
        if($resultado == "Título de foro ya registrado"){
            $_SESSION['error'] = "Título de foro ya registrado";
            header('Location: ../Vista/crear_foro.php'); 
        }
        else{
            $id = $resultado->getInsertedId()->__toString();
            header('Location: ../Vista/foro.php?foroId=' . $id); 
        }
        
        exit; 
    }
    
    if(isset($_POST['Desuscribirforo'])){
        $resultado = $forosModelo->desuscribirForo($_POST['id'], $_SESSION['nick']);
        if($resultado == null){
            $_SESSION['error'] = "Error al desuscribirse del foro";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        else{
            $_SESSION['mensaje'] = "Desuscripción correcta";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        
        exit;
    }
    if(isset($_POST['Suscribirforo'])){
        $resultado = $forosModelo->suscribirForo($_POST['id'], $_SESSION['nick']);
        if($resultado == null){
            $_SESSION['error'] = "Error al suscribirse del foro";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        else{
            $_SESSION['mensaje'] = "suscripción correcta";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        
        exit;
    }

    if (isset($_POST['eliminarForo'])) {
        $resultado = $forosModelo->eliminarForo($_POST['id']);
        if($resultado == null){
            $_SESSION['error'] = "Error al eliminar el foro";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        else{
            $_SESSION['mensaje'] = "Foro eliminado";
            header('Location: ../Vista/Foros.php'); 
        }
        
        exit; 
    }



    
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    if (isset($_GET['ObtenerListaForos'])) {
        $resultado = $forosModelo->obtenerForos();
        $foros = json_decode($resultado, true);
        $_SESSION['foros'] = $foros;
        
        header('Location: ../Vista/Foros.php'); 
        exit; 
    }

    if (isset($_GET['ObtenerForoId'])) {
        $resultado = $forosModelo->obtenerForoId($_GET['foroId']);
        if($resultado != null){
            $foro = json_decode($resultado, true);
            $_SESSION['foro'] = $foro;
        }
        else{
            $id = null;
            $_SESSION['foro'] = null;
        }
        
        header('Location: ../Vista/foro.php?foroId=' . $_GET['foroId']); 
        exit; 
    }
    

}
?>
<script src="../Recursos/js/socket.js"></script>
