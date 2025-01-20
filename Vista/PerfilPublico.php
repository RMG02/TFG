<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

require_once __DIR__ . "/plantillas/respuestas.php";

$modalId = 0;
$modalComId = 0;
$principal = true;
$tipo_publicacion = "publicacion";
$error = "";
$mensaje = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
}
$usuario = "";

$nickUsuario = $_GET['nick_Usuario'] ?? null;

if (!isset($_SESSION['nick_Usuario'])) {
    header('Location: ../Controlador/Usuario_controlador.php?Usuarion=true&nick_Usuario='.$nickUsuario);
    exit;
}

$usuario = json_decode($_SESSION['nickUser'], true);
date_default_timezone_set('Europe/Madrid');


$tituloPagina = "Perfil";

    $nickuser = $usuario['nick'];
    $email = $usuario['email'];
    $nombre = $usuario['nombre'];
    $seguidores = $usuario['seguidores'];
    $siguiendo = $usuario['siguiendo'];
    $numseguidores = count($seguidores);
    $numsiguiendo = count($siguiendo);

    

$contenidoPrincipal = <<<EOS
    <div class="tweet-header">
        <strong>$nick</strong>
    </div>
    <div class="tweet-content">

        <p>$email</p>
        <p>$nombre</p>
        <p>$numseguidores</p>
        <p>$numsiguiendo</p>

    <7div>  
EOS;



if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <div class="error_div">
            <p>$error</p>
        </div>
    EOS;
    unset($_SESSION['error']);
}

if ($mensaje != "") {
    $contenidoPrincipal .= <<<EOS
        <div class="mensaje_div">
            <p>$mensaje</p>
        </div>
    EOS;
    unset($_SESSION['mensaje']);
}

require_once __DIR__ . "/plantillas/plantilla.php";
?>

<script src="../Recursos/js/Verpubli.js"></script>

