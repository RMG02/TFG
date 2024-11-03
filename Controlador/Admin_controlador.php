<?php
require_once '../Config/config.php';
require_once '../Modelo/Usuario.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuarioModelo = new Usuario($db);
$admin = $_SESSION['admin'];

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(isset($_POST['añadirUsuario']) && $admin){

        if($_POST['rol'] == "admin"){
            $rol = True;
        }
        else{
            $rol = False;
        }

        $DatosUsuario = [
            'nombre' => $_POST['nombre'],
            'nick' => $_POST['nick'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'admin' => $rol

        ];

        $resultado = $usuarioModelo->registro($DatosUsuario);

        if ($resultado == "Email ya registrado" || $resultado == "Nick ya registrado") {
            $_SESSION['error'] = $resultado;
            header('Location: ../Vista/añadir_usuario.php');
        }
        else{
            $_SESSION['mensaje'] = "Usuario añadido";
            header('Location: ../Vista/panel_admin.php');
        }
    }

    if(isset($_POST['eliminarUsuario']) && $admin){
        if($usuarioModelo->confirmar($_POST['password'],$_SESSION['email']) == true){
            $email = $_POST['email'];
            $usuarioModelo->darBajaUsuario($email);
            $_SESSION['mensaje'] = "Usuario eliminado";
        }else{
            $_SESSION['error'] = "Contraseña incorrecta.";
        }
        
        header('Location: ../Vista/modificar_usuario.php');
    }

    if(isset($_POST['modificarUsuario']) && $admin){

        if($_POST['admin_nuevo'] == "admin"){
            $admin = True;
        }
        else{
            $admin = False;
        }
        $DatosUsuario = [
            'nombre' => $_POST['nombre_nuevo'] ?: $_POST['nombre'],
            'password' => $_POST['pass_nuevo'] ?: $_POST['pass'],
            'nick' => $_POST['nick_nuevo'] ?: $_POST['nick'],
            'email' => $_POST['email_nuevo'] ?: $_POST['email'],
            'admin' => $admin
        ];
        $resultado = $usuarioModelo->editarUsuario($_POST['email'],$DatosUsuario, $_POST['nick']);
        if ($resultado == "Email ya registrado" || $resultado == "Nick ya registrado") {
            $_SESSION['error'] = $resultado;
            header('Location: ../Vista/editar_perfil_admin.php');
        }else{
            $_SESSION['mensaje'] = "Usuario modificado";
            header('Location: ../Vista/modificar_usuario.php');
        }
    }
    
}




