<?php

require_once '../Config/config.php';
require_once '../Modelo/Publicacion.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$publicacionModelo = new Publicacion($db);

if (isset($_SESSION['publicaciones'])) {
    unset($_SESSION['publicaciones']);
}

if (isset($_SESSION['listaUsuarios'])) {
    unset($_SESSION['listaUsuarios']);
}

if (isset($_SESSION['publicacionesUsuario'])) {
    unset($_SESSION['publicacionesUsuario']);
}

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
    
    if(isset($_POST['editarPublicacion'])){
        
        $resultado = $publicacionModelo->EditarPublicacion($_POST['contenido'], $_POST['id_publi']);
        if ($resultado) {
            $_SESSION['mensaje'] = "Publicación editada";
            
        }
        else{
            $_SESSION['error'] = "Error al editar la publicación.";
        }
        header('Location: ../Vista/perfil.php');
        exit;
    }
    if(isset($_POST['editarPublicacionp'])){
        
        $resultado = $publicacionModelo->EditarPublicacion($_POST['contenido'], $_POST['id_publi']);
        if ($resultado) {
            $_SESSION['mensaje'] = "Publicación editada";
            
        }
        else{
            $_SESSION['error'] = "Error al editar la publicación.";
        }
        header('Location: ../Vista/Principal.php');
        exit;
    }

    if(isset($_POST['eliminarPublicacion'])){
        
        $resultado = $publicacionModelo->eliminarPublicacion($_POST['id_publi']);
        if ($resultado) {
            $_SESSION['mensaje'] = "Publicación eliminada";
            
        }
        else{
            $_SESSION['error'] = "Error al eliminar la publicación.";
        }
        header('Location: ../Vista/perfil.php');
        exit;
    }
    if(isset($_POST['eliminarPublicacionp'])){
        
        $resultado = $publicacionModelo->eliminarPublicacion($_POST['id_publi']);
        if ($resultado) {
            $_SESSION['mensaje'] = "Publicación eliminada";
            
        }
        else{
            $_SESSION['error'] = "Error al eliminar la publicación.";
        }
        header('Location: ../Vista/Principal.php');
        exit;
    }
    
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    if (isset($_GET['listarPublicaciones'])) { 
        $publicaciones = $publicacionModelo->ListaPublicacion(); 
        $_SESSION['publicaciones'] = json_encode(iterator_to_array($publicaciones)); 
        header('Location: ../Vista/Principal.php'); 
        exit; 
    } 

    if(isset($_GET['PubliUsuario'])){
        $publicaciones = $publicacionModelo->ListaPublicacionUsuario($_SESSION['nick']);
        $_SESSION['publicacionesUsuario'] = json_encode(iterator_to_array($publicaciones));
        header('Location: ../Vista/perfil.php'); 
        exit; 
    }
}

