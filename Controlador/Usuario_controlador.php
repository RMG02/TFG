<?php

require_once '../Config/config.php';
require_once '../Modelo/Usuario.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuarioModelo = new Usuario($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['registro'])) {
        $DatosUsuario = [
            'nombre' => $_POST['nombre'],
            'nick' => $_POST['nick'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'admin' => false

        ];
        $usuarioModelo->registro($DatosUsuario);
        header('Location: ../Vista/Login.php');
    }

    if (isset($_POST['login'])) {
        $usuario = $usuarioModelo->login( $_POST['email'], password: $_POST['password']);
        if ($usuario) {
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['nick'] = $usuario['nick'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['password'] = $usuario['password'];
            $_SESSION['login'] = true;
            $_SESSION['admin'] = $usuario['admin'];
            header('Location: ../Vista/Principal.php');
        } else {
            $_SESSION['error'] = "Email o contraseña incorrectos.";
            header('Location: ../Vista/Login.php');
        }
    }

    if (isset($_POST['editar'])) {
        $DatosUsuario = [
            'nombre' => $_POST['nombre'] ?: $_SESSION['nombre'],
            'password' => $_POST['password'] ?: $_SESSION['password'],
            'nick' => $_POST['nick'] ?: $_SESSION['nick'],
            'email' => $_POST['email'] ?: $_SESSION['email'],
            'admin' => $_SESSION['admin']
        ];
        $resultado = $usuarioModelo->editarUsuario($_SESSION['email'],$DatosUsuario);
        if ($resultado == "Email ya registrado.") {
            echo "El email ya está en uso por otro usuario.";
        }
        else{
            $_SESSION['email'] = $DatosUsuario['email'];
            $_SESSION['nick'] = $DatosUsuario['nick'];
            $_SESSION['nombre'] = $DatosUsuario['nombre'];
            $_SESSION['password'] = $DatosUsuario['password'];
            $_SESSION['login'] = true;
            $_SESSION['admin'] = $DatosUsuario['admin'];
            header('Location: ../Vista/perfil.php');
        }
        
    }

    if(isset($_POST['cerrarCuenta'])){
        $usuarioModelo->darBajaUsuario($_SESSION['email']);
        session_unset();
        session_destroy(); 
        header('Location: ../Vista/enter.php');
    }


    
}
