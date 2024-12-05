<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: ../Vista/Principal.php');
}

$mensaje = "";

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}



$tituloPagina = "Panel Admin";

$contenidoPrincipal = <<<EOS
    
    <h1>Panel de Administración</h1>
    <p class ="panel"><a href="añadir_usuario.php">Añadir Usuario</a></p>
    <p class ="panel"><a href="modificar_usuario.php">Modificar Usuario</a></p>
EOS;

if ($mensaje != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="mensaje">$mensaje</p>
    EOS;
}


require_once __DIR__."/plantillas/plantilla.php";

