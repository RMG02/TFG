<?php

require_once '../Config/config.php';
require_once '../Modelo/Publicacion.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$publicacionModelo = new Publicacion($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['crearPublicacion'])) {
        $DatosPublicacion = [
            'user_email' => $_SESSION['email'],
            'nick' => $_SESSION['nick'],
            'contenido' => $_POST['contenido']
            
        ];
        

        $resultado = $publicacionModelo->crearPublicacion($DatosPublicacion);
        if ($resultado) {
            $_SESSION['mensaje'] = "Publicación creada";
        } else {
            $_SESSION['error'] = "Error al crear la publicación.";
        }
        header('Location: ../Vista/Principal.php');
        exit;
    }

    
}

