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
        $notificacion = json_decode(file_get_contents('php://input'), true);
        $NotificacionModelo->crearNotificacion($notificacion);
    }
        
}

