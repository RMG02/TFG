<?php

require_once '../Config/config.php';
require_once '../Modelo/Usuario.php';

$usuarioModelo = new Usuario($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['registro'])) {
        $DatosUsuario = [
            'email' => $_POST['email'],
            'password' => $_POST['password']
        ];
        $usuarioModelo->registro($DatosUsuario);
        header('Location: ../Vista/Login.php');
    }

    if (isset($_POST['login'])) {
        $usuario = $usuarioModelo->login( $_POST['email'], password: $_POST['password']);
        if ($usuario) {
            session_start();
            $_SESSION['user'] = $usuario;
            $_SESSION['login'] = true;
            header('Location: ../Vista/Principal.php');
        } else {
            echo "Email o contraseña incorrectos.";
        }
    }
}
