<?php

require_once '../Config/config.php';
require_once '../Modelo/Receta.php';
require_once '../Modelo/Notificacion.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$recetaModelo = new Receta($db);
$NotificacionModelo = new Notificacion($db);
$dir_archivos = '../Recursos/multimedia';


if (isset($_SESSION['recetas'])) {
    unset($_SESSION['recetas']);
}

if (isset($_SESSION['listaUsuarios'])) {
    unset($_SESSION['listaUsuarios']);
}

if (isset($_SESSION['recetasUsuario'])) {
    unset($_SESSION['recetasUsuario']);
}

if (isset($_SESSION['id_publi'])) {
    unset($_SESSION['id_publi']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['crearReceta'])) {
        $archivo = $_FILES['archivo'];
        $archivo_subido = '';
    
        if ($archivo && $archivo['error'] == 0) {
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }
    
        $DatosReceta = [
            'user_email' => $_SESSION['email'],
            'nick' => $_SESSION['nick'],
            'titulo' => $_POST['titulo'],
            'ingredientes' => $_POST['ingredientes'],
            'preparacion' => $_POST['preparacion'],
            'dificultad' => (int) $_POST['dificultad'],
            'tiempo' => $_POST['tiempo'],
            'multimedia' => $archivo_subido,
            'likes' => [],
            'dislikes' => []
        ];
    
        $resultado = $recetaModelo->crearReceta($DatosReceta);
        if ($resultado) {
            $_SESSION['mensaje'] = "Receta creada";
        } else {
            $_SESSION['error'] = "Error al crear la receta.";
        }

        header('Location: ../Vista/Recetas.php');
    
    }

    
    
    if(isset($_POST['editarReceta'])){
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

        $resultado = $recetaModelo->EditarReceta($_POST['contenido'], $_POST['id_publi'], $archivo_subido);
        if ($resultado) {
            $_SESSION['mensaje'] = "Receta editada";
            
        }
        else{
            $_SESSION['error'] = "Error al editar la receta.";
        }

        header('Location: ../Vista/Verreceta.php?id='.$_POST['id_publi']);
        exit;
        
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

    
    if (isset($_POST['eliminarReceta'])) {

        if (isset($_SESSION['id_publi'])) {
            unset($_SESSION['id_publi']);
        }

        if (isset($_SESSION['notificaciones_usuario'])) {
            unset($_SESSION['notificaciones_usuario']);            
        }
        
        $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_SESSION['nick'], "publicacion");
        $resultado = $recetaModelo->obtenerReceta($_POST['id_publi']);
        $receta = json_decode(json_encode($resultado), true);
    
        if ($receta) {
            eliminarImagenes($receta['comentarios']);
            
            if (!empty($receta['multimedia'])) {
                $archivo = "../Recursos/multimedia/{$receta['multimedia']}";
                unlink($archivo);
            }
            
            $resultado = $recetaModelo->eliminarReceta($_POST['id_publi']);
            
            if ($resultado) {
                $_SESSION['mensaje'] = "Receta eliminada";
            } else {
                $_SESSION['error'] = "Error al eliminar la receta.";
            }
    
           
            header('Location: ../Vista/Recetas.php');
            exit;
            
        }
    }
    
   
    

    if (isset($_POST['darlike'])) {
        
        $resultado = $recetaModelo->obtenerReceta($_POST['id_publi']);
        $likesArray = (array) $resultado['likes']; // Convierte BSONArray a array PHP
        $dislikesArray = (array) $resultado['dislikes']; // Convierte BSONArray a array PHP

        if(in_array($_POST['nick_user'], $dislikesArray)){
            $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_POST['nick_user'], "dislike");

        }
        if (in_array($_POST['nick_user'], $likesArray)) {
            // Si el usuario ya dio like, quitarlo
            $resultado = $recetaModelo->Likesq($_POST['nick_user'],$_POST['id_publi']);
            $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_POST['nick_user'], "like");

            if ($resultado) {
                $_SESSION['mensaje'] = "Like quitado";
            }
        } else {
            // Si no ha dado like, agregarlo
            $resultado = $recetaModelo->Likes($_POST['nick_user'],$_POST['id_publi']);
            if ($resultado) {
                $_SESSION['mensaje'] = "Like dado";
                
            }
        }

        if (!$resultado) {
            $_SESSION['error'] = "Error al dar like.";
            
        }  
        

        if(isset($_POST['principal'])){
            if($_POST['principal']){
                header('Location: ../Vista/Recetas.php');
                exit;
            }
            header('Location: ../Vista/perfil.php');
            exit;
        }

        header('Location: ../Vista/Verreceta.php?id='.$_POST['id_publi']);
        exit;    
    }
    
    if (isset($_POST['dardislike'])) {

        $resultado = $recetaModelo->obtenerReceta($_POST['id_publi']);
        $likesArray = (array) $resultado['likes']; // Convierte BSONArray a array PHP
        $dislikesArray = (array) $resultado['dislikes']; // Convierte BSONArray a array PHP
        
        if(in_array($_POST['nick_user'], $likesArray)){
            
            $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_POST['nick_user'], "like");

        }
        if (in_array($_POST['nick_user'],  $dislikesArray)) {
            // Si el usuario ya dio like, quitarlo
            $resultado = $recetaModelo->DisLikesq($_POST['nick_user'],$_POST['id_publi']);
            //eliminar notificacion anterior
            $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_POST['nick_user'], "dislike");

            if ($resultado) {
                $_SESSION['mensaje'] = "Dislike quitado";
                
            }
        } else {
            // Si no ha dado like, agregarlo
            $resultado = $recetaModelo->DisLikes($_POST['nick_user'],$_POST['id_publi']);
            if ($resultado) {
                $_SESSION['mensaje'] = "Dislike dado";
                
            }
        }
        
        if (!$resultado) {
            $_SESSION['error'] = "Error al dar dislike.";
            
        }
        
        if(isset($_POST['principal'])){
            if($_POST['principal']){
                header('Location: ../Vista/Recetas.php');
                exit;
            }
            header('Location: ../Vista/perfil.php');
            exit;
        }

        header('Location: ../Vista/Verreceta.php?id='.$_POST['id_publi']);
        exit;  
        
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
    
        $resultado = $recetaModelo->agregarComentario($_POST['id_publi'], $comentario, $id_com_origen);
        if ($resultado) {
            $_SESSION['mensaje'] = "Comentario añadido";
        } else {
            $_SESSION['error'] = "Error al añadir el comentario.";
        }
        
        header('Location: ../Vista/Verreceta.php?id='.$_POST['id_publi']);
        exit;
    }
    
    if (isset($_POST['eliminarComentario'])) {
        if(isset($_POST['multi'])){
            unlink($_POST['multi']);
        }
        
        $esRespuesta = isset($_POST['esRespuesta']) ? true : null;

        $resultado = $recetaModelo->eliminarComentario($_POST['id_publi'], $_POST['id_comen'], $esRespuesta);
        if ($resultado) {
            $_SESSION['mensaje'] = "Comentario eliminado";
        } else {
            $_SESSION['error'] = "Error al eliminar el comentario.";
        }
        
       
        header('Location: ../Vista/Verreceta.php?id='.$_POST['id_publi']);
        exit;
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
       
        $resultado = $recetaModelo->editarComentario($_POST['id_publi'], $_POST['id_comen'], $_POST['contenido'], $archivo_subido, $esRespuesta);
        if ($resultado) {
            $_SESSION['mensaje'] = "Comentario editado";
        } else {
            $_SESSION['error'] = "Error al editar el comentario.";
        }
        
        header('Location: ../Vista/Verreceta.php?id='.$_POST['id_publi']);
        exit;
    }

    
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    if (isset($_GET['listarRecetas'])) { 
        $recetas = $recetaModelo->ListaReceta(); 
        $_SESSION['recetas'] = json_encode(iterator_to_array($recetas)); 
        header('Location: ../Vista/Recetas.php'); 
        exit; 
    } 

    if(isset($_GET['ReceUsuario'])){
        $recetas = $recetaModelo->ListaRecetaUsuario($_SESSION['nick']);
        $_SESSION['RecetasUsuario'] = json_encode(iterator_to_array($recetas));
        header('Location: ../Vista/perfil.php'); 
        exit; 
    }

    if(isset($_GET['publi_id'])){
        $resultado = $recetaModelo->obtenerReceta($_GET['id']);
        $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
        header('Location: ../Vista/Verreceta.php'); 
        exit; 
        
    }
    
}

