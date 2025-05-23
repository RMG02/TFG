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

if (isset($_SESSION['RecetasUsuarioPublico'])) {
    unset($_SESSION['RecetasUsuarioPublico']);
}

if (isset($_SESSION['id_publi'])) {
    unset($_SESSION['id_publi']);
}
if (isset($_SESSION['RecetasUsuario'])) {
    unset($_SESSION['RecetasUsuario']);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['crearReceta'])) {
        $archivo = $_FILES['archivo'];
        $archivo_subido = '';
    
        if ($archivo && $archivo['error'] == 0) {
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            // Verificar si la extensión es una imagen
            $permitidas = array('jpg', 'jpeg', 'png');
            if (!in_array($extension, $permitidas)) {
                $_SESSION['error'] = "Error al crear la receta, solo se permiten imágenes.";
                header('Location: ../Vista/Recetas.php');
                exit;
            }
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }
    
        $DatosReceta = [
            'email' => $_SESSION['email'],
            'nick' => $_SESSION['nick'],
            'titulo' => $_POST['titulo'],
            'ingredientes' => $_POST['ingredientes'],
            'preparacion' => $_POST['preparacion'],
            'tipo' => $_POST['tiporeceta'],
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
                $_SESSION['error'] = "Error al editar la receta, solo se permiten imágenes.";
                $resultado = $recetaModelo->obtenerReceta($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['recedisponible'] = true; 
                header('Location: ../Vista/Verreceta.php');
                exit;
            }
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }

        $resultado = $recetaModelo->EditarReceta($_POST['titulo'],$_POST['ingredientes'],$_POST['preparacion'],(int) $_POST['dificultad'],$_POST['tiempo'], $id, $archivo_subido);
        if ($resultado) {
            $_SESSION['mensaje'] = "Receta editada";
            $resultadox = $recetaModelo->obtenerReceta($id);
            $_SESSION['id_publi'] = json_encode(iterator_to_array($resultadox));
            $_SESSION['recedisponible'] = true; 
            
        }
        else{
            $_SESSION['error'] = "Error al editar la receta.";
        }
        
        header('Location: ../Vista/Verreceta.php');
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

        if(isset($_POST['perfilPublico'])){
            $_SESSION['verreceta'] = true;
            header('Location: ../Vista/unsetPerfilPublico.php?nick_user=' . $_POST['nick_perfil']);
            exit;
        }

        if(isset($_POST['perfilPublico'])){
            $_SESSION['verreceta'] = true;
            header('Location: ../Vista/unsetPerfilPublico.php?nick_user=' . $_POST['nick_perfil']);
            exit;
        }

        if (!preg_match('/^[a-f0-9]{24}$/i', $_POST['id_publi'])) { 
            $_SESSION['recedisponible'] = false;
            $_SESSION['id_publi'] = "";
        } else {
            $resultado = $recetaModelo->obtenerReceta($_POST['id_publi']);
    
            if ($resultado) {
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['recedisponible'] = true;
            } else {
                $_SESSION['recedisponible'] = false;
                $_SESSION['id_publi'] = NULL;
            }
        }
        header('Location: ../Vista/Verreceta.php'); 
        exit; 
            
    }
    

    if(isset($_POST['EliminarNick'])){
        

        $resultado = $recetaModelo->obtenerRecetasLikes($_POST['nick']);
        $recetas_likes_json = json_encode(iterator_to_array($resultado));
        $recetas_likes = json_decode($recetas_likes_json, true);

        $resultado = $recetaModelo->obtenerRecetasDislikes($_POST['nick']);
        $recetas_dislikes_json = json_encode(iterator_to_array($resultado));
        $recetas_dislikes = json_decode($recetas_dislikes_json, true);

        if(!empty($recetas_likes)){
            foreach ($recetas_likes as $receta) {
                $recetaModelo->eliminarNickLikes($_POST['nick'], $receta['_id']['$oid']);
            }
        }

        if(!empty($recetas_dislikes)){
            foreach ($recetas_dislikes as $receta) {
                $recetaModelo->eliminarNickDislikes($_POST['nick'], $receta['_id']['$oid']);
            }
        }

        header('Location: ' . $_SESSION['url_anterior']);
        exit;  
    }

    if(isset($_POST['cambioNick'])){
        

        $resultado = $recetaModelo->obtenerRecetasLikes($_POST['nick_pasado']);
        $recetas_likes_json = json_encode(iterator_to_array($resultado));
        $recetas_likes = json_decode($recetas_likes_json, true);

        $resultado = $recetaModelo->obtenerRecetasDislikes($_POST['nick_pasado']);
        $recetas_dislikes_json = json_encode(iterator_to_array($resultado));
        $recetas_dislikes = json_decode($recetas_dislikes_json, true);

        if(!empty($recetas_likes)){
            foreach ($recetas_likes as $receta) {
                $recetaModelo->actualizarNickLikes($_POST['nick_pasado'], $_POST['nuevoNick'], $receta['_id']['$oid']);
            }
        }

        if(!empty($recetas_dislikes)){
            foreach ($recetas_dislikes as $receta) {
                $recetaModelo->actualizarNickDislikes($_POST['nick_pasado'], $_POST['nuevoNick'], $receta['_id']['$oid']);
            }
        }

        header('Location: ' . $_SESSION['url_anterior']);
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

        if(isset($_POST['perfilPublico'])){
            $_SESSION['verreceta'] = true;
            header('Location: ../Vista/unsetPerfilPublico.php?nick_user=' . $_POST['nick_perfil']);
            exit;
        }

        if (!preg_match('/^[a-f0-9]{24}$/i', $_POST['id_publi'])) { 
            $_SESSION['recedisponible'] = false;
            $_SESSION['id_publi'] = "";
        } else {
            $resultado = $recetaModelo->obtenerReceta($_POST['id_publi']);
    
            if ($resultado) {
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['recedisponible'] = true;
            } else {
                $_SESSION['recedisponible'] = false;
                $_SESSION['id_publi'] = NULL;
            }
        }
        header('Location: ../Vista/Verreceta.php'); 
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
                $_SESSION['error'] = "Error al editar la receta, solo se permiten imágenes.";
                $resultado = $recetaModelo->obtenerReceta($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['recedisponible'] = true; 
                header('Location: ../Vista/Verreceta.php');
                exit;
            }
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }
    
        $comentario = [
            'usuario' => $_SESSION['nick'],
            'email' => $_SESSION['email'],
            'texto' => $_POST['texto'],
            'fecha' => date(DATE_ISO8601),
            'multimedia' => $archivo_subido
        ];
    
        $resultado = $recetaModelo->agregarComentario($_POST['id_publi'], $comentario, $id_com_origen);
        if ($resultado['resultado']) {
            $_SESSION['mensaje'] = "Comentario añadido";
        } else {
            $_SESSION['error'] = "Error al añadir el comentario.";
        }

        if ($resultado['resultado']) {
            $id_comentario = $resultado['id_comentario']->__toString();
            if($_POST['usuario_origen'] != $_SESSION['nick']){
                $resultado = $recetaModelo->obtenerReceta($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['recedisponible'] = true; 
                echo json_encode(['success' => true, 'id_comentario' => $id_comentario, 'redirect_url' => '/Controlador/Receta_controlador.php?publi_id=true&id=' . $id]);
            }
            else{
                $resultado = $recetaModelo->obtenerReceta($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['recedisponible'] = true; 
                header('Location: ../Vista/Verreceta.php');
            }
        } else {
            $resultado = $recetaModelo->obtenerReceta($id);
            $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
            $_SESSION['recedisponible'] = true; 
            header('Location: ../Vista/Verreceta.php');
        }
        exit;
    }
    
    if (isset($_POST['eliminarComentario'])) {
        if(isset($_POST['multi'])){
            unlink($_POST['multi']);
        }
        $id = $_POST['id_publi'] ?? ''; 
        $esRespuesta = isset($_POST['esRespuesta']) ? true : null;
        if($esRespuesta){
            $NotificacionModelo->borrarNotificacionComentario($_POST['id_publi'], $_POST['id_comen'], $_POST['id_comentario_origen']);
        }
        else{
            $NotificacionModelo->borrarNotificacionComentario($_POST['id_publi'], $_POST['id_comen'], null);
        }
        $resultado = $recetaModelo->eliminarComentario($_POST['id_publi'], $_POST['id_comen'], $esRespuesta);
        if ($resultado) {
            $_SESSION['mensaje'] = "Comentario eliminado";
        } else {
            $_SESSION['error'] = "Error al eliminar el comentario.";
        }
        $resultado = $recetaModelo->obtenerReceta($id);
        $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
        $_SESSION['recedisponible'] = true; 
       
        header('Location: ../Vista/Verreceta.php');
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
                $_SESSION['error'] = "Error al editar la receta, solo se permiten imágenes.";
                $resultado = $recetaModelo->obtenerReceta($id);
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['recedisponible'] = true; 
                header('Location: ../Vista/Verreceta.php');
                exit;
            }
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
        $resultado = $recetaModelo->obtenerReceta($id);
        $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
        $_SESSION['recedisponible'] = true; 
        header('Location: ../Vista/Verreceta.php');
        exit;
    }

    if(isset($_POST["pruebareceta_id"])){
        $id = $_POST['idpruebareceta'] ?? ''; 
        if (!preg_match('/^[a-f0-9]{24}$/i', $id)) { 
            $_SESSION['recedisponible'] = false;
            $_SESSION['id_publi'] = "";
        } else {
            $resultado = $recetaModelo->obtenerReceta($id);
    
            if ($resultado) {
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['recedisponible'] = true;
            } else {
                $_SESSION['recedisponible'] = false;
                $_SESSION['id_publi'] = NULL;
            }
        }
        header('Location: ../Vista/Verreceta.php'); 
        exit; 
    }

    if(isset($_POST["seguidoresreceta"])){
        $_SESSION['verseguidoresreceta'] = $_POST["verseguidoresreceta"];
        header('Location: ../Vista/Recetas.php');
    }

    
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    if (isset($_GET['listarRecetas'])) { 
        $recetas = $recetaModelo->ListaReceta(); 
        $_SESSION['recetas'] = json_encode(iterator_to_array($recetas)); 
        $_SESSION['verseguidoresreceta'] = $_GET['verseguidores'];
        $_SESSION['paginarecetas'] = $_GET['paginarecetas'];
        header('Location: ../Vista/Recetas.php'); 
        exit; 
    } 

    if(isset($_GET['ReceUsuario'])){
        $recetas = $recetaModelo->ListaRecetaUsuario($_SESSION['nick']);
        $_SESSION['RecetasUsuario'] = json_encode(iterator_to_array($recetas));
        header('Location: ../Vista/perfil.php'); 
        exit; 
    }
    if(isset($_GET['ReceUsuarioPublico'])){
        if (isset($_GET['nick'])) {
            $nick = $_GET['nick'];
            $recetas = $recetaModelo->ListaRecetaUsuario($nick);
            $_SESSION['RecetasUsuarioPublico'] = json_encode(iterator_to_array($recetas));
            $_SESSION['nickUserpublico'] = $nick;
            header('Location: ../Vista/PerfilPublico.php'); 
            exit; 
        }
        else{
            $email = $_GET['email'];
            $recetas = $recetaModelo->ListaRecetaUsuarioEmail($email);
            $_SESSION['RecetasUsuarioPublico'] = json_encode(iterator_to_array($recetas));
            $_SESSION['emailUserpublico'] = $email;
            header('Location: ../Vista/PerfilPublico.php'); 
            exit; 
        }
    }

    if(isset($_GET['publi_id'])){
        $id = $_GET['id'] ?? ''; 
        if (!preg_match('/^[a-f0-9]{24}$/i', $id)) { 
            $_SESSION['recedisponible'] = false;
            $_SESSION['id_publi'] = "";
        } else {
            $resultado = $recetaModelo->obtenerReceta($id);
    
            if ($resultado) {
                $_SESSION['id_publi'] = json_encode(iterator_to_array($resultado));
                $_SESSION['recedisponible'] = true;
            } else {
                $_SESSION['recedisponible'] = false;
                $_SESSION['id_publi'] = NULL;
            }
        }
        header('Location: ../Vista/Verreceta.php'); 
        exit; 
        
    }
    
}

