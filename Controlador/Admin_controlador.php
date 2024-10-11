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
        $DatosUsuario = [
            'nombre' => $_POST['nombre'],
            'nick' => $_POST['nick'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'admin' => $_POST['admin']

        ];
        $usuarioModelo->registro($DatosUsuario);
        echo "Usuario añadido.";
    }

    if(isset($_POST['darBajaUsuario']) && $admin){
        $email = $_POST['email'];
        $usuarioModelo->darBajaUsuario($email);
        echo "Usuario dado de baja.";
    }

    if(isset($_POST['modificarUsuario']) && $admin){
        $email = $_POST['email'];
        $nuevosDatos = [
            'nombre' => $_POST['nombre'],
            'nick' => $_POST['nick'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'admin' => $_POST['admin']
        ];

        $usuarioModelo->modificarUsuario($email, $nuevosDatos);
        echo "Datos de usuario modificados.";
    }
}




