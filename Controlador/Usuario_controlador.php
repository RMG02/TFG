<?php

require_once '../Config/config.php';
require_once '../Modelo/Usuario.php';
require_once '../Modelo/Receta.php';
require_once '../Modelo/Publicacion.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuarioModelo = new Usuario($db);
$recetaModelo = new Receta($db);
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
    if (isset($_POST['registro'])) {
        $DatosUsuario = [
            'nombre' => $_POST['nombre'],
            'nick' => $_POST['nick'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'seguidores' =>[],
            'siguiendo' =>[],
            'favoritospubli' =>[],
            'favoritosreceta' =>[],
            'admin' => false,
            'notilikes' => true,
            'notiseguidores' => true,
            'noticomentarios' => true,
            'notimensajes' => true

        ];
        $resultado = $usuarioModelo->registro($DatosUsuario);
        if($resultado == "Email ya registrado" || $resultado == "Nick ya registrado"){
            $_SESSION['error'] = $resultado;
            header('Location: ../Vista/Registro.php');
        } else{
            header('Location: ../Vista/Login.php');
        }
    }

    if (isset($_POST['login'])) {
        $usuario = $usuarioModelo->login( $_POST['email'], password: $_POST['password']);
        $usuario_resultado = json_decode(json_encode(iterator_to_array($usuario)), true);
        if ($usuario) {
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['nick'] = $usuario['nick'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['password'] = $usuario['password'];
            $_SESSION['seguidores'] = $usuario_resultado['seguidores'];
            $_SESSION['siguiendo'] = $usuario_resultado['siguiendo'];
            $_SESSION['usuariopropio'] = json_encode($usuario);
            $_SESSION['login'] = true;
            $_SESSION['admin'] = $usuario['admin'];
            $_SESSION['notilikes'] = $usuario['notilikes'];
            $_SESSION['idsrecetas'] = (array) $usuario['favoritosreceta'];
            $_SESSION['idspublis'] = (array) $usuario['favoritospubli'];
            $_SESSION['notiseguidores'] = $usuario['notiseguidores'];
            $_SESSION['noticomentarios'] = $usuario['noticomentarios'];
            $_SESSION['notimensajes'] = $usuario['notimensajes'];

            header('Location: ../Vista/Principal.php');
        } else {
            $_SESSION['error'] = "Email o contraseña incorrectos.";
            header('Location: ../Vista/Login.php');
        }
    }


    if(isset($_POST['usuariopropio'])){

        $usuarioActualizadox = $usuarioModelo->obtenerUsuario($_SESSION['email']);
        $_SESSION['usuariopropio'] = json_encode(iterator_to_array($usuarioActualizadox));
        header('Location: ' . $_SESSION['url_anterior']);
        exit;   
        
    }

    if (isset($_POST['Seguir'])) {
        
        $usuarioseguir = $usuarioModelo->obtenerUsuarioNick($_POST['nickSeguir']);
        $seguidores = (array) $usuarioseguir['seguidores']; 

        if(in_array($_POST['nickPropio'], $seguidores)){
            $resultado = $usuarioModelo->noSeguir($_POST['nickPropio'], $_POST['nickSeguir']);
            if ($resultado) {
                $_SESSION['mensaje'] = "Lo has dejado de seguir";
            }
        
        }else {
            
            $resultado = $usuarioModelo->Seguir($_POST['nickPropio'], $_POST['nickSeguir']);
            if ($resultado) {
                $_SESSION['mensaje'] = "Lo has empezado a seguir";
                
            }
        }

        if (!$resultado) {
            $_SESSION['error'] = "Error al seguir.";
            
        } else{
            $usuarioActualizado = $usuarioModelo->obtenerUsuarioNick($_POST['nickSeguir']);
            $_SESSION['emailUser'] = json_encode(iterator_to_array($usuarioActualizado));
            $usuarioActualizadox = $usuarioModelo->obtenerUsuarioNick($_POST['nickPropio']);
            $_SESSION['usuariopropio'] = json_encode(iterator_to_array($usuarioActualizadox));
            if($usuarioActualizadox){
                $usuario = json_decode($_SESSION['usuariopropio'], true);
                $_SESSION['siguiendo'] = $usuario['siguiendo'];
                $_SESSION['seguidores'] = $usuario['seguidores'];
            }
            
            
        }
        
        header('Location: ../Vista/unsetPerfilPublico.php?nick_user='.$_POST['nickSeguir']);
        exit;    
    }

    if (isset($_POST['editar'])) {
        $DatosUsuario = [
            'nombre' => $_POST['nombre'] ?: $_SESSION['nombre'],
            'password' => $_POST['password'] ?: $_SESSION['password'],
            'nick' => $_POST['nick'] ?: $_SESSION['nick'],
            'email' => $_POST['email'] ?: $_SESSION['email'],
            'admin' => $_SESSION['admin']
        ];
        $resultado = $usuarioModelo->editarUsuario($_SESSION['email'],$DatosUsuario, $_SESSION['nick']);
        if ($resultado == "Email ya registrado" || $resultado == "Nick ya registrado") {
            $_SESSION['error'] = $resultado;
            header('Location: ../Vista/Editarperfil.php');
        }
        else{
            $antiguonick = $_SESSION['nick'];
            $nuevonick = $DatosUsuario['nick'];
            $_SESSION['email'] = $DatosUsuario['email'];
            $_SESSION['nick'] = $DatosUsuario['nick'];
            $_SESSION['nombre'] = $DatosUsuario['nombre'];
            $_SESSION['password'] = $DatosUsuario['password'];
            $_SESSION['login'] = true;
            $_SESSION['admin'] = $DatosUsuario['admin'];
            $_SESSION['mensaje'] = "Datos modificados";
            $publicacionModelo->cambiarnickpublicacion($antiguonick,$nuevonick);
            $recetaModelo->cambiarnickpublicacion($antiguonick,$nuevonick);
            header('Location: ../Vista/perfil.php');
        }
        
    }

    if(isset($_POST['cerrarCuenta'])){
        if($usuarioModelo->confirmar($_POST['password'],$_SESSION['email']) == true){
            $publicacionModelo->eliminarpublicacionescerrar($_SESSION['email']);
            $recetaModelo->eliminarrecetascerrar($_SESSION['email']);
            $usuarioModelo->darBajaUsuario($_SESSION['email']);

            session_unset();
            session_destroy(); 
            header('Location: ../Vista/enter.php');
        }else{
            $_SESSION['error'] = "Contraseña incorrecta.";
            header('Location: ../Vista/perfil.php');
        }
        
        
    }

    if (isset($_POST["ischanged"])) {
        $type = $_POST["type"];
        $status = $_POST["status"] == "1" ? 1 : 0;
    
        // Guardar en sesión (opcionalmente, podrías guardarlo en una BD)
        $resultado = $usuarioModelo->preferencias($type,$status,$_SESSION['email']);
        
        $_SESSION[$type] = $status;
        header('Location: ../Vista/Preferencias.php');
        exit;
    
    }

    if (isset($_POST["favoritos"])) {
        $publicacion = $_POST["publi"];
        $nick = $_POST["nick_user"];
        $urlfav = isset($_POST["urlfav"]) && $_POST["urlfav"] === "true"; // Convertir a booleano
    
        if ($_POST["tipo"] === "true") { // Si es una publicación
            $resultado = $usuarioModelo->favoritospublicacion($publicacion, $nick);
    
            if ($resultado) {
                $_SESSION['mensaje'] = "Publicación añadida a favoritos";
            } else {
                $_SESSION['error'] = "Usuario no encontrado";
            }
    
            $usuario = $usuarioModelo->obtenerUsuarioNick($nick);
            $_SESSION['idspublis'] = (array) $usuario['favoritospubli'];
    
            // Redirigir a la URL correspondiente
            header('Location: ' . ($urlfav ? '../Vista/favoritos.php' : '../Vista/Principal.php'));
            exit;
        } else { // Si es una receta
            $resultado = $usuarioModelo->favoritosreceta($publicacion, $nick);
    
            if ($resultado) {
                $_SESSION['mensaje'] = "Receta añadida a favoritos";
            } else {
                $_SESSION['error'] = "Usuario no encontrado";
            }
    
            $usuario = $usuarioModelo->obtenerUsuarioNick($nick);
            $_SESSION['idsrecetas'] = (array) $usuario['favoritosreceta'];
    
            // Redirigir a la URL correspondiente
            header('Location: ' . ($urlfav ? '../Vista/favoritos.php' : '../Vista/Recetas.php'));
            exit;
        }
    }
    

    
    

    
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    if (isset($_GET['publicoemail'])) {
        $email = $_GET['email_Usur'] ?? '';

        /*if(isset($_SESSION['nickUserpublico'] )){
            unset($_SESSION['nickUserpublico'] );
        } */

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            $_SESSION['usudisponible'] = false;
            $_SESSION['emailUser'] = "";
            header('Location: ../Vista/PerfilPublico.php');
            exit;
        } else {
            $resultado = $usuarioModelo->obtenerUsuario($email);
            if ($resultado) {
                $_SESSION['emailUser'] = json_encode(iterator_to_array($resultado));
                $_SESSION['usudisponible'] = true;
                if(isset($_GET['verreceta'])){
                    $_SESSION['emailUserpublico'] = $email;
                    header('Location: ../Vista/PerfilPublico.php?verreceta=true');  
                    exit;
        
                }
                else{
                    $_SESSION['emailUserpublico'] = $email;
                    header('Location: ../Vista/PerfilPublico.php');  
                    exit;
                }
            } else {
                $_SESSION['usudisponible'] = false;
                $_SESSION['emailUser'] = NULL;
                header('Location: ../Vista/PerfilPublico.php');
                exit;
            }   
            
        }
        exit; 
    }


    if (isset($_GET['publiconick'])) {
        $nick = $_GET['nick_Usur'] ?? '';

        /*if(isset($_SESSION['emailUserpublico'])){
            unset($_SESSION['emailUserpublico']);
        }*/

        if (empty($nick)) { 
            $_SESSION['usudisponible'] = false;
            $_SESSION['nickUser'] = "";
            header('Location: ../Vista/PerfilPublico.php');
            exit;
        } else {
            $resultado = $usuarioModelo->obtenerUsuarioNick($nick);
            if ($resultado) {
                $_SESSION['nickUser'] = json_encode(iterator_to_array($resultado));
                $_SESSION['usudisponible'] = true;
                if(isset($_GET['verreceta'])){
                    //header('Location: ../Vista/PerfilPublico.php?nick_user=' . $nick); 
                    $_SESSION['nickUserpublico'] = $nick;
                    header('Location: ../Vista/PerfilPublico.php?verreceta=true'); 
                    exit;
                    
                }
                else{
                    $_SESSION['nickUserpublico'] = $nick;
                    //header('Location: ../Vista/PerfilPublico.php?verreceta=true&nick_user=' . $nick); 
                    header('Location: ../Vista/PerfilPublico.php');
                    exit;
                }
            } else {
                $_SESSION['usudisponible'] = false;
                $_SESSION['nickUser'] = NULL;
                header('Location: ../Vista/PerfilPublico.php');
                exit;
            }   
            
        }
        
        exit; 
    }

    


}
