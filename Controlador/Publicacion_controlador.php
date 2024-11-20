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
        $dir_archivos = '../Recursos/multimedia';
        $archivo = $_FILES['archivo'];
        $archivo_subido = '';
    
        if ($file['error'] == 0) {
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }
    
        $DatosPublicacion = [
            'user_email' => $_SESSION['email'],
            'nick' => $_SESSION['nick'],
            'contenido' => $_POST['contenido'],
            'multimedia' => $archivo_subido 
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

        if(isset($_POST['principal'])){
            header('Location: ../Vista/Principal.php');
            exit;
        }
        else{
            header('Location: ../Vista/perfil.php');
            exit;
        }
    }

    if(isset($_POST['eliminarPublicacion'])){
        
        $resultado = $publicacionModelo->eliminarPublicacion($_POST['id_publi']);
        if ($resultado) {
            $_SESSION['mensaje'] = "Publicación eliminada";
            
        }
        else{
            $_SESSION['error'] = "Error al eliminar la publicación.";
        }

        if(isset($_POST['principal'])){
            header('Location: ../Vista/Principal.php');
            exit;
        }
        else{
            header('Location: ../Vista/perfil.php');
            exit;
        }
        
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

