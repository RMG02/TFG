<?php

require_once '../Config/config.php';
require_once '../Modelo/Usuario.php';

session_start();
$usuarioModelo = new Usuario($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['registro'])) {
        $DatosUsuario = [
            'nombre' => $_POST['nombre'],
            'nick' => $_POST['nick'],
            'email' => $_POST['email'],
            'password' => $_POST['password']

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
            header('Location: ../Vista/Principal.php');
        } else {
            echo "Email o contraseña incorrectos.";
        }
    }
}
