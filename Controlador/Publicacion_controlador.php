<?php

require_once '../Config/config.php';
require_once '../Modelo/Publicacion.php';
require_once '../Modelo/Notificacion.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$publicacionModelo = new Publicacion($db);
$NotificacionModelo = new Notificacion($db);
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

if (isset($_SESSION['publicacionesUsuarioPublico'])) {
    unset($_SESSION['publicacionesUsuarioPublico']);
}

if (isset($_SESSION['id_publi'])) {
    unset($_SESSION['id_publi']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['crearPublicacion'])) {
        $archivo = $_FILES['archivo'];
        $archivo_subido = '';
    
        if ($archivo && $archivo['error'] == 0) {
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            // Verificar si la extensión es una imagen
            $permitidas = array('jpg', 'jpeg', 'png');
            if (!in_array($extension, $permitidas)) {
                $_SESSION['error'] = "Error al crear la publicación, solo se permiten imágenes.";
                if($_POST['principal']){
                    header('Location: ../Vista/Principal.php');
                    exit;
                }
                else{
                    header('Location: ../Vista/perfil.php');
                    exit;
                }
            }
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }
    
        $DatosPublicacion = [
            'email' => $_SESSION['email'],
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
        if($_POST['principal']){
            header('Location: ../Vista/Principal.php');
            exit;
        }
        else{
            header('Location: ../Vista/perfil.php');
            exit;
        }
    
    }

    
    
    if(isset($_POST['editarPublicacion'])){
        $archivo = $_FILES['nuevo_archivo'];
        $archivo_subido = $_POST['archivo_origen'];
        $id = $_POST['id_publi'] ?? '';  

        if ($archivo && $archivo['error'] == 0) {
            $anterior = "../Recursos/multimedia/$archivo_subido";
            unlink($anterior);
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            // Verificar si la extensión es una imagen
            $permitidas = array('jpg', 'jpeg', 'png');
            if (!in_array($extension, $permitidas)) {
                $_SESSION['error'] = "Error al modificar la publicación, solo se permiten imágenes.";
                $resultado = $publicacionModelo->obtenerPublicacion($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;  
                header('Location: ../Vista/Verpublicacion.php');
                exit;
            }
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
        $resultado = $publicacionModelo->obtenerPublicacion($id);
        $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
        $_SESSION['publidisponible'] = true;  
        header('Location: ../Vista/Verpublicacion.php');
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

    
    if (isset($_POST['eliminarPublicacion'])) {

        if (isset($_SESSION['id_publi'])) {
            unset($_SESSION['id_publi']);
        }

        if (isset($_SESSION['notificaciones_usuario'])) {
            unset($_SESSION['notificaciones_usuario']);            
        }
        $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_SESSION['nick'], "publicacion");
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
    
           
            header('Location: ../Vista/Principal.php');
            exit;
            
        }
    }
    
   
    

    if (isset($_POST['darlike'])) {
        
        $resultado = $publicacionModelo->obtenerPublicacion($_POST['id_publi']);
        $likesArray = (array) $resultado['likes']; // Convierte BSONArray a array PHP
        $dislikesArray = (array) $resultado['dislikes']; // Convierte BSONArray a array PHP

        if(in_array($_POST['nick_user'], $dislikesArray)){
            $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_POST['nick_user'], "dislike");

        }
        if (in_array($_POST['nick_user'], $likesArray)) {
            // Si el usuario ya dio like, quitarlo
            $resultado = $publicacionModelo->Likesq($_POST['nick_user'],$_POST['id_publi']);
            $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_POST['nick_user'], "like");

            if ($resultado) {
                $_SESSION['mensaje'] = "Like quitado";
            }
        } else {
            // Si no ha dado like, agregarlo
            $resultado = $publicacionModelo->Likes($_POST['nick_user'],$_POST['id_publi']);
            if ($resultado) {
                $_SESSION['mensaje'] = "Like dado";
                
            }
        }

        if (!$resultado) {
            $_SESSION['error'] = "Error al dar like.";
            
        }  
        

        if(isset($_POST['principal'])){
            if($_POST['principal']){
                header('Location: ../Vista/Principal.php');
                exit;
            }
            header('Location: ../Vista/perfil.php');
            exit;
        }

        if(isset($_POST['perfilPublico'])){
            $_SESSION['verreceta'] = false;
            header('Location: ../Vista/unsetPerfilPublico.php?nick_user=' . $_POST['nick_user']);
            exit;
        }

        if (!preg_match('/^[a-f0-9]{24}$/i', $_POST['id_publi'])) { 
            $_SESSION['publidisponible'] = false;
            $_SESSION['id_publi'] = "";
        } else {
            $resultado = $publicacionModelo->obtenerPublicacion($_POST['id_publi']);
    
            if ($resultado) {
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;
            } else {
                $_SESSION['publidisponible'] = false;
                $_SESSION['id_publi'] = NULL;
            }
        }
    
        header('Location: ../Vista/Verpublicacion.php');
        exit;    
    }
    
    if (isset($_POST['dardislike'])) {

        $resultado = $publicacionModelo->obtenerPublicacion($_POST['id_publi']);
        $likesArray = (array) $resultado['likes']; // Convierte BSONArray a array PHP
        $dislikesArray = (array) $resultado['dislikes']; // Convierte BSONArray a array PHP
        
        if(in_array($_POST['nick_user'], $likesArray)){
            
            $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_POST['nick_user'], "like");

        }
        if (in_array($_POST['nick_user'],  $dislikesArray)) {
            // Si el usuario ya dio like, quitarlo
            $resultado = $publicacionModelo->DisLikesq($_POST['nick_user'],$_POST['id_publi']);
            //eliminar notificacion anterior
            $NotificacionModelo->borrarNotificacion($_POST['id_publi'], $_POST['nick_user'], "dislike");

            if ($resultado) {
                $_SESSION['mensaje'] = "Dislike quitado";
                
            }
        } else {
            // Si no ha dado like, agregarlo
            $resultado = $publicacionModelo->DisLikes($_POST['nick_user'],$_POST['id_publi']);
            if ($resultado) {
                $_SESSION['mensaje'] = "Dislike dado";
                
            }
        }
        
        if (!$resultado) {
            $_SESSION['error'] = "Error al dar dislike.";
            
        }
        
        if(isset($_POST['principal'])){
            if($_POST['principal']){
                header('Location: ../Vista/Principal.php');
                exit;
            }
            header('Location: ../Vista/perfil.php');
            exit;
        }

        if(isset($_POST['perfilPublico'])){
            $_SESSION['verreceta'] = false;
            header('Location: ../Vista/unsetPerfilPublico.php?nick_user=' . $_POST['nick_user']);
            exit;
        }

        if (!preg_match('/^[a-f0-9]{24}$/i', $_POST['id_publi'])) { 
            $_SESSION['publidisponible'] = false;
            $_SESSION['id_publi'] = "";
        } else {
            $resultado = $publicacionModelo->obtenerPublicacion($_POST['id_publi']);
    
            if ($resultado) {
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;
            } else {
                $_SESSION['publidisponible'] = false;
                $_SESSION['id_publi'] = NULL;
            }
        }
    
        header('Location: ../Vista/Verpublicacion.php');
        exit;  
        
    }

    if (isset($_POST['agregarComentario'])) {
        $archivo = $_FILES['archivo'];
        $archivo_subido = '';
        $id_com_origen = isset($_POST['esRespuesta']) ? $_POST['id_comen'] : null;
        $id = $_POST['id_publi'] ?? ''; 
    
        if ($archivo && $archivo['error'] == 0) {
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            // Verificar si la extensión es una imagen
            $permitidas = array('jpg', 'jpeg', 'png');
            if (!in_array($extension, $permitidas)) {
                $_SESSION['error'] = "Error al crear el comentario, solo se permiten imágenes.";
                $resultado = $publicacionModelo->obtenerPublicacion($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;
                header('Location: ../Vista/Verpublicacion.php');        
                exit;
            }
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
        if ($resultado['resultado']) {
            $_SESSION['mensaje'] = "Comentario añadido";
        } else {
            $_SESSION['error'] = "Error al añadir el comentario.";
        }

        if ($resultado['resultado']) {
            $id_comentario = $resultado['id_comentario']->__toString();
            if($_POST['usuario_origen'] != $_SESSION['nick']){
                $resultado = $publicacionModelo->obtenerPublicacion($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;
                $mirar = json_encode(['success' => true,'id_comentario' => $id_comentario,'redirect_url' => '/Controlador/Publicacion_controlador.php?publi_id=true&id=' . $id]);
                echo json_encode(['success' => true, 'id_comentario' => $id_comentario, 'redirect_url' => '/Controlador/Publicacion_controlador.php?publi_id=true&id=' . $id]);
            }
            else{
                $resultado = $publicacionModelo->obtenerPublicacion($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;     
                header('Location: ../Vista/Verpublicacion.php');   
            }
        } else {
                $resultado = $publicacionModelo->obtenerPublicacion($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;   
                header('Location: ../Vista/Verpublicacion.php');    
        }
        exit;
        
    }
    
    if (isset($_POST['eliminarComentario'])) {
        if(isset($_POST['multi'])){
            unlink($_POST['multi']);
        }
        
        $esRespuesta = isset($_POST['esRespuesta']) ? true : null;
        $id = $_POST['id_publi'] ?? ''; 
        
        if($esRespuesta){
            $NotificacionModelo->borrarNotificacionComentario($_POST['id_publi'], $_POST['id_comen'], $_POST['id_comentario_origen']);
        }
        else{
            $NotificacionModelo->borrarNotificacionComentario($_POST['id_publi'], $_POST['id_comen'], null);
        }

        $resultado = $publicacionModelo->eliminarComentario($_POST['id_publi'], $_POST['id_comen'], $esRespuesta);
        if ($resultado) {
            $_SESSION['mensaje'] = "Comentario eliminado";
        } else {
            $_SESSION['error'] = "Error al eliminar el comentario.";
        }

        $resultado = $publicacionModelo->obtenerPublicacion($id);
        $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
        $_SESSION['publidisponible'] = true; 
       
        header('Location: ../Vista/Verpublicacion.php');
        exit;
    }

    if (isset($_POST['editarComentario'])) {

        $archivo = $_FILES['nuevo_archivo'];
        $archivo_subido = $_POST['archivo_origen'];
        $esRespuesta = isset($_POST['esRespuesta']) ? true : null;
        $id = $_POST['id_publi'] ?? ''; 

        if ($archivo && $archivo['error'] == 0) {
            $anterior = "../Recursos/multimedia/$archivo_subido";
            unlink($anterior);
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            // Verificar si la extensión es una imagen
            $permitidas = array('jpg', 'jpeg', 'png');
            if (!in_array($extension, $permitidas)) {
                $_SESSION['error'] = "Error al editar el comentario, solo se permiten imágenes.";
                $resultado = $publicacionModelo->obtenerPublicacion($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;  
                header('Location: ../Vista/Verpublicacion.php');
                exit;
            }
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
        $resultado = $publicacionModelo->obtenerPublicacion($id);
        $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
        $_SESSION['publidisponible'] = true;  
        header('Location: ../Vista/Verpublicacion.php');
        exit;
    }

    if(isset($_POST["prueba_id"])){
        $id = $_POST['idprueba'] ?? ''; 
    
        if (!preg_match('/^[a-f0-9]{24}$/i', $id)) { 
            $_SESSION['publidisponible'] = false;
            $_SESSION['id_publi'] = "";
        } else {
            $resultado = $publicacionModelo->obtenerPublicacion($id);
    
            if ($resultado) {
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;
            } else {
                $_SESSION['publidisponible'] = false;
                $_SESSION['id_publi'] = NULL;
            }
        }
    
        header('Location: ../Vista/Verpublicacion.php');
        exit;
    }

    if(isset($_POST["seguidores"])){
        $_SESSION['verseguidores'] = $_POST["verseguidores"];
        header('Location: ../Vista/Principal.php');
    }

    
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    if (isset($_GET['listarPublicaciones'])) { 
        $publicaciones = $publicacionModelo->ListaPublicacion(); 
        $_SESSION['publicaciones'] = json_encode(iterator_to_array($publicaciones));
        $_SESSION['verseguidores'] = $_GET['verseguidores'];
        header('Location: ../Vista/Principal.php'); 
        exit; 
    } 

    if(isset($_GET['PubliUsuario'])){
        $publicaciones = $publicacionModelo->ListaPublicacionUsuario($_SESSION['nick']);
        $_SESSION['publicacionesUsuario'] = json_encode(iterator_to_array($publicaciones));
        header('Location: ../Vista/perfil.php'); 
        exit; 
    }
    if(isset($_GET['PubliUsuarioPublico'])){
        if (isset($_GET['nick'])) {
            $nick = $_GET['nick'];
            $publicaciones = $publicacionModelo->ListaPublicacionUsuario($nick);
            $_SESSION['publicacionesUsuarioPublico'] = json_encode(iterator_to_array($publicaciones));
            $_SESSION['nickUserpublico'] = $nick;
            header('Location: ../Vista/PerfilPublico.php'); 
            exit; 
        }
        else{
            $email = $_GET['email'];
            $publicaciones = $publicacionModelo->ListaPublicacionUsuarioEmail($email);
            $_SESSION['publicacionesUsuarioPublico'] = json_encode(iterator_to_array($publicaciones));
            $_SESSION['emailUserpublico'] = $email;
            header('Location: ../Vista/PerfilPublico.php'); 
            exit; 
        }
        
    }

    if (isset($_GET['publi_id'])) {
        $id = $_GET['id'] ?? ''; 
    
        if (!preg_match('/^[a-f0-9]{24}$/i', $id)) { 
            $_SESSION['publidisponible'] = false;
            $_SESSION['id_publi'] = "";
        } else {
            $resultado = $publicacionModelo->obtenerPublicacion($id);
    
            if ($resultado) {
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['publidisponible'] = true;
            } else {
                $_SESSION['publidisponible'] = false;
                $_SESSION['id_publi'] = NULL;
            }
        }
    
        header('Location: ../Vista/Verpublicacion.php');
        exit;
    }
    
    
    
}

