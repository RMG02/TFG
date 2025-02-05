<?php

require_once '../Config/config.php';
require_once '../Modelo/Conversaciones.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conversacionesModelo = new Conversacion($db);



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
        $_SESSION['conversacion'] = $conversacion[0];
        header('Location: ../Vista/chat.php?conversacionId=' . $id); 
        exit; 
    }

    if(isset($_POST['AgregarMensaje'])){
        $servidor_post = json_decode($_POST['AgregarMensaje'], true);
    
        $resultado = $conversacionesModelo->agregarMensaje($servidor_post['conversacion_id'], $servidor_post['usuario_emisor'], $servidor_post['contenido'], $servidor_post['usuario_receptor'], $servidor_post['hora']);
        $resultado = $conversacionesModelo->obtenerConversaciones(usuario: $_SESSION['nick']);
        $_SESSION['conversaciones'] = json_encode(iterator_to_array($resultado));
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

    if(isset($_GET['ObtenerConversacion'])){

        $resultado = $conversacionesModelo->obtenerConversacionId($_GET['conversacionId']);
        $conversacion = json_decode($resultado, true);
        $id = $conversacion['_id']['$oid'];
        $_SESSION['conversacion'] = $conversacion;
        header('Location: ../Vista/chat.php?conversacionId=' . $id); 
        exit; 
    }

}
