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
    
    if(file_get_contents('php://input')){
        $servidor_post = json_decode(file_get_contents('php://input'), true);
        $notificacion = [
            'usuario_publi'=> $servidor_post['notificacion']['usuario_publi'], 
            'usuario_accion' => $servidor_post['notificacion']['usuario_accion'],
            'mensaje' => $servidor_post['notificacion']['mensaje'],
            'id_publi' => $servidor_post['notificacion']['id_publi'],
            'enlace' => $servidor_post['notificacion']['enlace'],
            'tipo' => $servidor_post['notificacion']['tipo'],
            'fecha' => $servidor_post['notificacion']['fecha'],
            'vista' => $servidor_post['notificacion']['vista'],
            
        ];
        $NotificacionModelo->crearNotificacion($notificacion);
        if (isset($_SESSION['notificaciones_usuario'])) {
            unset($_SESSION['notificaciones_usuario']);
        }
    }
    if(isset($_POST['accion'])){
        if (isset($_SESSION['notificaciones_usuario'])) {
            unset($_SESSION['notificaciones_usuario']);
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
