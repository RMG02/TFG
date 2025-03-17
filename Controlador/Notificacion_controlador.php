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
        if(isset($servidor_post['id_comentario'])){
            $notificacion = [
                'usuario_publi'=> $servidor_post['usuario_publi'], 
                'usuario_accion' => $servidor_post['usuario_accion'],
                'mensaje' => $servidor_post['mensaje'],
                'id_publi' => $servidor_post['id_publi'],
                'enlace' => $servidor_post['enlace'],
                'tipo' => $servidor_post['tipo'],
                'fecha' => $servidor_post['fecha'],
                'tipo_publicacion' => $servidor_post['tipo_publicacion'],
                'id_comentario' => $servidor_post['id_comentario']
            ];
        }
        else{
            $notificacion = [
                'usuario_publi'=> $servidor_post['usuario_publi'], 
                'usuario_accion' => $servidor_post['usuario_accion'],
                'mensaje' => $servidor_post['mensaje'],
                'id_publi' => $servidor_post['id_publi'],
                'enlace' => $servidor_post['enlace'],
                'tipo' => $servidor_post['tipo'],
                'fecha' => $servidor_post['fecha'],
                'tipo_publicacion' => $servidor_post['tipo_publicacion'],
            ];
        }
        
        $NotificacionModelo->crearNotificacion($notificacion);
        
    }
    if(isset($_POST['accion'])){
        if (isset($_SESSION['notificaciones_usuario'])) {
            unset($_SESSION['notificaciones_usuario']);
            header('Location: ' . $_SESSION['url_anterior']);
            
        }
    }

    if(isset($_POST['eliminarNotificacion'])){
        if (isset($_SESSION['notificaciones_usuario'])) {
            unset($_SESSION['notificaciones_usuario']);
            
        }

        $resultado = $NotificacionModelo->borrarNotificacionUnica($_POST['id_noti']);

        if ($resultado) {
            $_SESSION['mensaje'] = "Notificación eliminada";
        } else {
            $_SESSION['error'] = "Error al eliminar la notificación.";
        }

        header('Location: ../Vista/Notificaciones.php');
        exit;
    }

    if(isset($_POST['eliminarNotificacionNick'])){
        if($_POST['nick'] == $_SESSION['nick']){
            $resultado = $NotificacionModelo->borrarTodasNotificaciones($_SESSION['notificaciones_usuario']);

            if ($resultado) {
                $_SESSION['mensaje'] = "Notificación eliminada";
            } else {
                $_SESSION['error'] = "Error al eliminar la notificación.";
            }
    
            header('Location: ./logout.php');
            exit;
        }
        else{
            header('Location: ' . $_SESSION['url_anterior']);
            exit;  
        }

        
    }

    if(isset($_POST['eliminarTodas'])){
    
        $resultado = $NotificacionModelo->borrarTodasNotificaciones($_SESSION['notificaciones_usuario']);

        if ($resultado) {
            $_SESSION['mensaje'] = "Notificaciones eliminadas";
            unset($_SESSION['notificaciones_usuario']);
        } else {
            $_SESSION['error'] = "Error al eliminar las notificaciones";
        }

        header('Location: ../Vista/Notificaciones.php');
        exit;
    }

    
    
        
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
        
    if(isset($_GET['listarNotificacionesUsuario'])){
        $notificaciones = $NotificacionModelo->obtenerTodasNotificaciones($_SESSION['nick']);
        $_SESSION['notificaciones_usuario'] = json_encode(iterator_to_array($notificaciones));
        header('Location: ../Vista/Notificaciones.php');
        
    }

    if (isset($_GET['get_session_vars'])) {
        echo json_encode([
            'notilikes' => $_SESSION['notilikes'],
            'noticomentarios' => $_SESSION['noticomentarios'],
            'notiseguidores' => $_SESSION['notiseguidores'],
            'notimensajes' => $_SESSION['notimensajes']
        ]);
        exit;
    }


    
}



