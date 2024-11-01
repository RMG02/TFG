<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: ../Vista/Principal.php');
}

$tituloPagina = "Panel Admin";

$contenidoPrincipal = <<<EOS
    
    <h1>Panel de Administración</h1>
    <p><a href="añadir_usuario.php">Añadir Usuario</a></p>
    <p><a href="modificar_usuario.php">Modificar Usuario</a></p>
EOS;

require_once __DIR__."/plantillas/plantilla.php";

