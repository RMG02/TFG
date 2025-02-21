<?php

require_once '../Config/config.php';
require_once '../Modelo/Conversaciones.php';
require_once '../Modelo/Notificacion.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conversacionesModelo = new Conversacion($db);
$NotificacionModelo = new Notificacion($db);



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['abrirConversacion'])) {
        $resultado = $conversacionesModelo->obtenerConversacion($_POST['usuario1'], $_POST['usuario2']);
        $conversacion = json_decode($resultado, true);
        if(empty($conversacion)){
            $conversacion = $conversacionesModelo->crearConversacion($_POST['usuario1'], $_POST['usuario2']);
            $id = $conversacion->getInsertedId()->__toString();
        }else{
            $id = $conversacion[0]['_id']['$oid'];
        }
        if(isset($_POST['compartir'])){
            header('Location: ../Vista/chat.php?conversacionId=' . $id . '&compartir=' . $_POST['compartir'] . '&id=' . $_POST['id_comp']); 
        }
        else{
            header('Location: ../Vista/chat.php?conversacionId=' . $id); 
        }
        
        exit; 
    }

    if (isset($_POST['eliminarConversacion'])) {
        if (isset($_SESSION['notificaciones_usuario'])) {
            unset($_SESSION['notificaciones_usuario']);            
        }
        $NotificacionModelo->borrarNotificacionesConver($_SESSION['nick'], "mensaje"); 
        $resultado = $conversacionesModelo->eliminarConversacion($_POST['id_conver'], $_SESSION['nick']);

        header('Location: ../Vista/chats.php'); 
        exit; 

    }

    if(isset($_POST['AgregarMensaje'])){
        $servidor_post = json_decode($_POST['AgregarMensaje'], true);
        $men_id = $conversacionesModelo->agregarMensaje($servidor_post['conversacion_id'], $servidor_post['usuario_emisor'], $servidor_post['contenido'], $servidor_post['usuario_receptor'], $servidor_post['hora']);
        if (isset($_SESSION['nick'])) {
            $resultado = $conversacionesModelo->obtenerConversaciones(usuario: $_SESSION['nick']);
            $_SESSION['conversaciones'] = json_encode(iterator_to_array($resultado));

        }
        // Enviar el ID del mensaje como respuesta JSON
        $respuesta = [
            'mensaje_id' => (string) $men_id // Devolver el ID como string
        ];
        
        echo json_encode($respuesta);
        exit;
    }

    if(isset($_POST['EliminarMensaje'])){
        $servidor_post = json_decode($_POST['EliminarMensaje'], true);
        $resultado = $conversacionesModelo->eliminarMensaje($servidor_post['mensaje_id']);
        exit;
    }
    
    if(isset($_POST['EditarMensaje'])){
        $servidor_post = json_decode($_POST['EditarMensaje'], true);
        $resultado = $conversacionesModelo->editarMensaje($servidor_post['mensaje_id'], $servidor_post['contenido']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    if (isset($_GET['listarConversaciones'])) {
        $nick = $_GET['nick_Usur'];
        $resultado = $conversacionesModelo->obtenerConversaciones($nick);
        $_SESSION['conversaciones'] = json_encode(iterator_to_array($resultado));
        
        header('Location: ../Vista/chats.php'); 
        exit; 
    }
    
    if (isset($_GET['listarConversacionesAbiertas'])) {
        $nick = $_GET['nick_Usur'];
        $resultado = $conversacionesModelo->obtenerConversaciones($nick);
        $_SESSION['conversaciones_abiertas'] = json_encode(iterator_to_array($resultado));
        header('Location: ' . $_SESSION['url_anterior']);
        exit;
        
    }

    if(isset($_GET['ObtenerConversacion'])){
        
        $resultado = $conversacionesModelo->obtenerConversacionId($_GET['conversacionId']);
        if($resultado != null){
            $conversacion = json_decode($resultado, true);
            $id = $conversacion['_id']['$oid'];
            $_SESSION['conversacion'] = $conversacion;
        }
        else{
            $id = null;
            $_SESSION['conversacion'] = null;
        }
        
        header('Location: ../Vista/chat.php?conversacionId=' . $id . '&compartir=' . $_GET['compartir'] . '&id=' . $_GET['id']); 
        exit; 
    }

}
?>
<script src="../Recursos/js/socket.js"></script>
