<?php

require_once '../Config/config.php';
require_once '../Modelo/Notificacion.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$NotificacionModelo = new Notificacion($db);


if (isset($_SESSION['publicaciones'])) {
    unset($_SESSION['publicaciones']);
}

if (isset($_SESSION['listaUsuarios'])) {
    unset($_SESSION['listaUsuarios']);
}

if (isset($_SESSION['publicacionesUsuario'])) {
    unset($_SESSION['publicacionesUsuario']);
}

if (isset($_SESSION['id_publi'])) {
    unset($_SESSION['id_publi']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['notificacion'])) {
        $servidor_post = json_decode($_POST['notificacion'], true);
        $notificacion = [
            'usuario_publi'=> $servidor_post['usuario_publi'], 
            'usuario_accion' => $servidor_post['usuario_accion'],
            'mensaje' => $servidor_post['mensaje'],
            'id_publi' => $servidor_post['id_publi'],
            'enlace' => $servidor_post['enlace'],
            'tipo' => $servidor_post['tipo'],
            'fecha' => $servidor_post['fecha'],
            'vista' => $servidor_post['vista'],
        ];
        $NotificacionModelo->crearNotificacion($notificacion);
        
    }
    if(isset($_POST['accion'])){
        if (isset($_SESSION['notificaciones_usuario'])) {
            unset($_SESSION['notificaciones_usuario']);
            header('Location: ' . $_SESSION['url_anterior']);

        }
    }

    
    
        
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
        
    if(isset($_GET['listarNotificacionesUsuario'])){
        $notificaciones = $NotificacionModelo->obtenerTodasNotificaciones($_SESSION['nick']);
        $_SESSION['notificaciones_usuario'] = json_encode(iterator_to_array($notificaciones));
        //header('Location: ../Vista/Notificaciones.php'); 
        header('Location: ' . $_SESSION['url_anterior']);
        exit; 
    }

    
}



