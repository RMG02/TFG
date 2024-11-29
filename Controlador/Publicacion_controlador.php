<?php

require_once '../Config/config.php';
require_once '../Modelo/Publicacion.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$publicacionModelo = new Publicacion($db);
$dir_archivos = '../Recursos/multimedia';

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
        $archivo = $_FILES['archivo'];
        $archivo_subido = '';
    
        if ($archivo && $archivo['error'] == 0) {
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
            'multimedia' => $archivo_subido,
            'likes' => [],
            'dislikes' => []
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
        $archivo = $_FILES['nuevo_archivo'];
        $archivo_subido = $_POST['archivo_origen'];

        if ($archivo && $archivo['error'] == 0) {
            $anterior = "../Recursos/multimedia/$archivo_subido";
            unlink($anterior);
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }

        $resultado = $publicacionModelo->EditarPublicacion($_POST['contenido'], $_POST['id_publi'], $archivo_subido);
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

    function eliminarImagenes($comentarios) {
        foreach ($comentarios as $comentario) {
            if (!empty($comentario['multimedia'])) {
                $archivo = "../Recursos/multimedia/{$comentario['multimedia']}";
                unlink($archivo);
            }
    
            if (!empty($comentario['respuestas'])) {
                eliminarImagenes($comentario['respuestas']);
            }
        }
    }
    
    if (isset($_POST['eliminarPublicacion'])) {
        $resultado = $publicacionModelo->obtenerPublicacion($_POST['id_publi']);
        $publicacion = json_decode(json_encode($resultado), true);
    
        if ($publicacion) {
            eliminarImagenes($publicacion['comentarios']);
            
            if (!empty($publicacion['multimedia'])) {
                $archivo = "../Recursos/multimedia/{$publicacion['multimedia']}";
                unlink($archivo);
            }
            
            $resultado = $publicacionModelo->eliminarPublicacion($_POST['id_publi']);
            
            if ($resultado) {
                $_SESSION['mensaje'] = "Publicación eliminada";
            } else {
                $_SESSION['error'] = "Error al eliminar la publicación.";
            }
    
            if (isset($_POST['principal'])) {
                header('Location: ../Vista/Principal.php');
                exit;
            } else {
                header('Location: ../Vista/perfil.php');
                exit;
            }
        }
    }
    
   
    

    if (isset($_POST['darlike'])) {
        
        $resultado = $publicacionModelo->obtenerPublicacion($_POST['id_publi']);
        $likesArray = (array) $resultado['likes']; // Convierte BSONArray a array PHP
        if (in_array($_POST['nick_user'], $likesArray)) {
            // Si el usuario ya dio like, quitarlo
            $resultado = $publicacionModelo->Likesq($_POST['nick_user'],$_POST['id_publi']);
        } else {
            // Si no ha dado like, agregarlo
            $resultado = $publicacionModelo->Likes($_POST['nick_user'],$_POST['id_publi']);
        }

        if ($resultado) {
            $_SESSION['mensaje'] = "Like dado";
            
        }
        else{
            $_SESSION['error'] = "Error al dar like.";
        }
        header('Location: ../Vista/Principal.php');
    }
    
    if (isset($_POST['dardislike'])) {

        $resultado = $publicacionModelo->obtenerPublicacion($_POST['id_publi']);
    
        $dislikesArray = (array) $resultado['dislikes']; // Convierte BSONArray a array PHP
    
        if (in_array($_POST['nick_user'],  $dislikesArray)) {
            // Si el usuario ya dio like, quitarlo
            $resultado = $publicacionModelo->DisLikesq($_POST['nick_user'],$_POST['id_publi']);
        } else {
            // Si no ha dado like, agregarlo
            $resultado = $publicacionModelo->DisLikes($_POST['nick_user'],$_POST['id_publi']);
        }
        if ($resultado) {
            $_SESSION['mensaje'] = "Dislike dado";
            
        }
        else{
            $_SESSION['error'] = "Error al dar dislike.";
        }
        header('Location: ../Vista/Principal.php');
        
    }

    if (isset($_POST['agregarComentario'])) {
        $archivo = $_FILES['archivo'];
        $archivo_subido = '';
        $id_com_origen = isset($_POST['esRespuesta']) ? $_POST['id_comen'] : null;
    
        if ($archivo && $archivo['error'] == 0) {
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }
    
        $comentario = [
            'usuario' => $_SESSION['nick'],
            'texto' => $_POST['texto'],
            'fecha' => date(DATE_ISO8601),
            'multimedia' => $archivo_subido
        ];
    
        $resultado = $publicacionModelo->agregarComentario($_POST['id_publi'], $comentario, $id_com_origen);
        if ($resultado) {
            $_SESSION['mensaje'] = "Comentario añadido";
        } else {
            $_SESSION['error'] = "Error al añadir el comentario.";
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
    
    if (isset($_POST['eliminarComentario'])) {
        if(isset($_POST['multi'])){
            unlink($_POST['multi']);
        }
        
        $esRespuesta = isset($_POST['esRespuesta']) ? true : null;

        $resultado = $publicacionModelo->eliminarComentario($_POST['id_publi'], $_POST['id_comen'], $esRespuesta);
        if ($resultado) {
            $_SESSION['mensaje'] = "Comentario eliminado";
        } else {
            $_SESSION['error'] = "Error al eliminar el comentario.";
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

    if (isset($_POST['editarComentario'])) {

        $archivo = $_FILES['nuevo_archivo'];
        $archivo_subido = $_POST['archivo_origen'];
        $esRespuesta = isset($_POST['esRespuesta']) ? true : null;

        if ($archivo && $archivo['error'] == 0) {
            $anterior = "../Recursos/multimedia/$archivo_subido";
            unlink($anterior);
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }
       
        $resultado = $publicacionModelo->editarComentario($_POST['id_publi'], $_POST['id_comen'], $_POST['contenido'], $archivo_subido, $esRespuesta);
        if ($resultado) {
            $_SESSION['mensaje'] = "Comentario editado";
        } else {
            $_SESSION['error'] = "Error al editar el comentario.";
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

