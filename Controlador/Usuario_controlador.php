<?php

require_once '../Config/config.php';
require_once '../Modelo/Usuario.php';
require_once '../Modelo/Receta.php';
require_once '../Modelo/Publicacion.php';
require_once '../Modelo/Foro.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuarioModelo = new Usuario($db);
$recetaModelo = new Receta($db);
$publicacionModelo = new Publicacion($db);
$foroModelo = new Foro($db);

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
            'notimensajes' => true,
            'confirmado' => false,
            'forosCreados' => 0,
            'forosSuscrito' => []

        ];
        $resultado = $usuarioModelo->registro($DatosUsuario);
        if($resultado == "Email ya registrado" || $resultado == "Nick ya registrado"){
            $_SESSION['error'] = $resultado;
            header('Location: ../Vista/Registro.php');
        } else{
            $_SESSION['mensaje'] = "Usuario registrado correctamente, revisa tu correo para confirmar";
            $resultado2 = $usuarioModelo->enviarEnlaceConfirmacion($DatosUsuario['email']);
            header('Location: ../Vista/Login.php');
        }
    }

    if (isset($_POST['login'])) {
        $usuario = $usuarioModelo->login( $_POST['email'], password: $_POST['password']);
        if ($usuario) {
            $usuario_resultado = json_decode(json_encode(iterator_to_array($usuario)), true);
            if($usuario_resultado['confirmado'] == false){
                $_SESSION['error'] = "Usuario no confirmado, revisa tu correo";
                header('Location: ../Vista/Login.php');
                exit;
            }else{
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
            }
            

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

    if(isset($_POST['foroEliminado'])){
        $usuarioModelo->foroDesuscrito($_POST['idForo'], $_POST['nick']);
        header('Location: ' . $_SESSION['url_anterior']);
        exit;   
        
    }

    if(isset($_POST['NuevaCon'])){
        $usuario = $usuarioModelo->obtenerUsuarioToken($_POST['token']);

        if($usuario == null){
            $_SESSION['error'] = "El enlace de recuperación ha expirado.";
            header('Location: ../Vista/login.php');
            exit;  
        }
        else{
            if($usuarioModelo->cambiarContraseña($usuario['email'], $_POST['contraseña'], $_POST['contraseña2'])){
                $_SESSION['mensaje'] = "Contraseña cambiada correctamente";
                header('Location: ../Vista/Login.php');
                exit;  
            }
            else{
                $_SESSION['error'] = "Las contraseñas no coinciden, vuelve a introducirlas";
                header('Location: ../Vista/nueva_contraseña.php?token=' . $_POST['token']);
                exit;
            }

        }
         
        
    }

    if(isset($_POST['RecuperarCon'])){
        $usuario = $usuarioModelo->obtenerUsuario($_POST['email']);

        if($usuario == null){
            $_SESSION['error'] = "El email introducido no tiene cuenta asiganda";

            header('Location: ../Vista/recuperar_contraseña.php');
            exit;  
        }
        else{
            if($usuarioModelo->enviarEnlace($_POST['email'])){
                $_SESSION['mensaje'] = "Enlace de recuperación enviado, revisa tu correo";
                header('Location: ../Vista/Login.php');
                exit;  
            }
            else{
                $_SESSION['error'] = "No se ha podido enviar el enlace de recuperación, vuelve a intentarlo";
                header('Location: ../Vista/recuperar_contraseña.php');
                exit;
            }

        }
         
        
    }
    if (isset($_POST['Nuevaconfirmacion'])){
        $usuario = $usuarioModelo->obtenerUsuarioTokenconfirmacion($_POST['tokenconfi']);

        if($usuario == null){
            $_SESSION['error'] = "El enlace de confirmacion ha expirado.";
            header('Location: ../Vista/Login.php');
            exit;  
        }
        else{
                $usuarioModelo->cambiarconfirmacion($usuario['email']);
                $_SESSION['mensaje'] = "Cuenta cofirmada correctamente";
                header('Location: ../Vista/Login.php');
                exit;  
            

        }
    }


    if(isset($_POST['EliminarNick'])){
        $resultado = $usuarioModelo->obtenerUsuario($_SESSION['email']);
        $usuario_json = json_encode(iterator_to_array($resultado));
        $usuario = json_decode($usuario_json, true);
        $cambiarSeguidores = false;
        $cambiarSiguiendo = false;
        $_SESSION['seguidores'] = $usuario_resultado['seguidores'];
        $_SESSION['siguiendo'] = $usuario_resultado['siguiendo'];

        if (in_array($_POST['nick'], $usuario['seguidores'])) {
            $cambiarSeguidores = true;
            
        }
    
        if (in_array($_POST['nick'], $usuario['siguiendo'])) {
            $cambiarSiguiendo = true;
        }
        
        $usuarioModelo->eliminarNick($_POST['nick'], $_SESSION['email'], $cambiarSeguidores, $cambiarSiguiendo);

        if($cambiarSeguidores || $cambiarSiguiendo){
            $resultado = $usuarioModelo->obtenerUsuario($_SESSION['email']);
            $usuario_json = json_encode(iterator_to_array($resultado));
            $usuario = json_decode($usuario_json, true);
            $_SESSION['seguidores'] = $usuario['seguidores'];
            $_SESSION['siguiendo'] = $usuario['siguiendo'];
        }

        header('Location: ' . $_SESSION['url_anterior']);
        exit;  
    }

    if(isset($_POST['cambioNick'])){
        $resultado = $usuarioModelo->obtenerUsuario($_SESSION['email']);
        $usuario_json = json_encode(iterator_to_array($resultado));
        $usuario = json_decode($usuario_json, true);
        $cambiarSeguidores = false;
        $cambiarSiguiendo = false;
        $_SESSION['seguidores'] = $usuario_resultado['seguidores'];
        $_SESSION['siguiendo'] = $usuario_resultado['siguiendo'];

        if (in_array($_POST['nick_pasado'], $usuario['seguidores'])) {
            $cambiarSeguidores = true;
            
        }
    
        if (in_array($_POST['nick_pasado'], $usuario['siguiendo'])) {
            $cambiarSiguiendo = true;
        }
        
        $usuarioModelo->actualizarNick($_POST['nick_pasado'], $_POST['nuevoNick'], $_SESSION['email'], $cambiarSeguidores, $cambiarSiguiendo);

        if($cambiarSeguidores || $cambiarSiguiendo){
            $resultado = $usuarioModelo->obtenerUsuario($_SESSION['email']);
            $usuario_json = json_encode(iterator_to_array($resultado));
            $usuario = json_decode($usuario_json, true);
            $_SESSION['seguidores'] = $usuario['seguidores'];
            $_SESSION['siguiendo'] = $usuario['siguiendo'];
        }

        if($_POST['admin'] && ($_SESSION['nick'] == $_POST['nick_pasado'])){
            $_SESSION['nick'] = $_POST['nuevoNick'];
            $publicacionModelo->cambiarnickpublicacion($_POST['nick_pasado'],$_POST['nuevoNick']);
            $recetaModelo->cambiarnickpublicacion($_POST['nick_pasado'],$_POST['nuevoNick']);
        }

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
            'email' => $_SESSION['email'],
            'admin' => $_SESSION['admin']
        ];
        $resultado = $usuarioModelo->editarUsuario($_SESSION['email'],$DatosUsuario, $_SESSION['nick']);
        if ($resultado == "Email ya registrado" || $resultado == "Nick ya registrado") {
            $_SESSION['error'] = $resultado;
            header('Location: ../Vista/Editarperfil.php');
        }
        else{
            $resultado = $usuarioModelo->obtenerUsuario($_SESSION['email']);
            $usuario_resultado = json_decode(json_encode(iterator_to_array($resultado)), true);
            $_SESSION['seguidores'] = $usuario_resultado['seguidores'];
            $_SESSION['siguiendo'] = $usuario_resultado['siguiendo'];

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
            $foroModelo->eliminarforoscerrar($_SESSION['nick']);
            $usuarioModelo->darBajaUsuario($_SESSION['email']);

            session_unset();
            session_destroy(); 
            header('Location: ../Vista/enter.php');
        }else{
            $_SESSION['error'] = "Contraseña incorrecta.";
            header('Location: ../Vista/perfil.php');
        }
        
    }

    if(isset($_POST['verusuariooo'])){
        $email= $_POST['email'];
        header('Location: ../Vista/unsetPerfilPublico.php?email_user='.$email);
        
    }

    if(isset($_POST['buscarusuario'])){
        $texto = $_POST['filtro'];
        $usuarios = $usuarioModelo->buscarUsuario($texto);
        if($usuarios){
            $_SESSION['usuarioslista'] = json_encode(iterator_to_array($usuarios));
        }else{
            $_SESSION['usuarioslista'] = '';
        }
        
        header('Location: ../Vista/Buscador.php');
    }

    if (isset($_POST["ischanged"])) {
        $type = $_POST["type"];
        $status = $_POST["status"] == "1" ? 1 : 0;
    
        $resultado = $usuarioModelo->preferencias($type,$status,$_SESSION['email']);
        
        $_SESSION[$type] = $status;
        header('Location: ../Vista/Preferencias.php');
        exit;
    
    }

    if (isset($_POST["favoritos"])) {
        $publicacion = $_POST["publi"];
        $nick = $_POST["nick_user"];
        $urlfav = isset($_POST["urlfav"]) && $_POST["urlfav"] === "true";
        $perfilpublico = isset($_POST["perfilPublico"]) && $_POST["perfilPublico"] === "true";  // Convertir a booleano
        $perfil = isset($_POST["perfil"]) && $_POST["perfil"] === "true"; 
        $verpubli = isset($_POST["verpublicacion"]) && $_POST["verpublicacion"] === "true"; 
        $vereceta = isset($_POST["verreceta"]) && $_POST["verreceta"] === "true"; 

        if ($_POST["tipo"] === "true") { // Si es una publicación
            $resultado = $usuarioModelo->favoritospublicacion($publicacion, $nick);
            
            if (!$resultado) {
                $_SESSION['error'] = "Usuario no encontrado";
            } 
        
    
            $usuario = $usuarioModelo->obtenerUsuarioNick($nick);
            $_SESSION['idspublis'] = (array) $usuario['favoritospubli'];
            
            if($perfilpublico){
                header('Location: ../Vista/Perfilpublico.php');
                exit;
            }
            if($perfil){
                header('Location: ../Vista/perfil.php');
                exit;
            }
            if($verpubli){
                header('Location: ../Vista/Verpublicacion.php');
                exit;
            }
            if($vereceta){
                header('Location: ../Vista/Verreceta.php');
                exit;
            }
            header('Location: ' . ($urlfav ? '../Vista/favoritos.php' : '../Vista/Principal.php'));
            exit;
        } else { // Si es una receta
            $resultado = $usuarioModelo->favoritosreceta($publicacion, $nick);
    
            if (!$resultado) {
                $_SESSION['error'] = "Usuario no encontrado";
            } 
    
            $usuario = $usuarioModelo->obtenerUsuarioNick($nick);
            $_SESSION['idsrecetas'] = (array) $usuario['favoritosreceta'];
            
            if($perfilpublico){
                header('Location: ../Vista/Perfilpublico.php');
                exit;
            }
            if($perfil){
                $_SESSION['verRecetasPerfil'] = true;
                header('Location: ../Vista/perfil.php');
                exit;
            }
            if($verpubli){
                header('Location: ../Vista/Verpublicacion.php');
                exit;
            }
            if($vereceta){
                header('Location: ../Vista/Verreceta.php');
                exit;
            }
            header('Location: ' . ($urlfav ? '../Vista/favoritos.php' : '../Vista/Recetas.php'));
            exit;
        }
    }
    

    
    

    
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    if (isset($_GET['publicoemail'])) {
        $email = $_GET['email_Usur'] ?? '';

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
    }
    
    if (isset($_GET['seguidores'])) {
        $resultado = $usuarioModelo->obtenerUsuario($_SESSION['email']);
        $usuario_resultado = json_decode(json_encode(iterator_to_array($resultado)), true);
        $_SESSION['seguidores'] = $usuario_resultado['seguidores'];
        $_SESSION['siguiendo'] = $usuario_resultado['siguiendo'];

        header('Location: ' . $_SESSION['url_anterior']);
        exit;  
    }

    if (isset($_GET['publiconick'])) {
        $nick = $_GET['nick_Usur'] ?? '';

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
                    $_SESSION['nickUserpublico'] = $nick;
                    header('Location: ../Vista/PerfilPublico.php?verreceta=true'); 
                    exit;
                    
                }
                else{
                    $_SESSION['nickUserpublico'] = $nick;
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
        
    }

    if (isset($_GET['arraypublis'])) {
        $usuario = $usuarioModelo->obtenerUsuarioNick($_SESSION['nick']);
        $_SESSION['idspublis'] = (array) $usuario['favoritospubli'];
        header('Location: ../Vista/Principal.php');
        exit;
    }

    if (isset($_GET['arrayrecetas'])) {
        $usuario = $usuarioModelo->obtenerUsuarioNick($_SESSION['nick']);
        $_SESSION['idsrecetas'] = (array) $usuario['favoritosreceta'];
        $_SESSION['paginarecetas'] = $_GET['paginarecetas'];
        header('Location: ../Vista/Recetas.php');
        exit;
    }

    if (isset($_GET['arraypublisperfilpublico'])) {
        $usuario = $usuarioModelo->obtenerUsuarioNick($_SESSION['nick']);
        $user = $_GET['user'];
        if($_GET['verreceta'] == "true"){
            $_SESSION['idsrecetas'] = (array) $usuario['favoritosreceta'];
        }else{
            $_SESSION['idspublis'] = (array) $usuario['favoritospubli'];
        }
        $_SESSION['nickUserpublico'] = $user;
        header('Location: ../Vista/Perfilpublico.php');
        exit;
    }
   

    


}
