<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

$error = "";
$mensaje = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
}

$emailUsuario = $_GET['email_user'] ?? null;
if( $emailUsuario == $_SESSION['email']){
    header('Location: ../Vista/perfil.php');
    exit;
}

if (!isset($_SESSION['emailUser'])) {
    header('Location: ../Controlador/Usuario_controlador.php?Usuarion=true&email_Usur='.$emailUsuario);
    exit;
}


$tituloPagina = "Perfil";

$usuario = json_decode($_SESSION['emailUser'], true);



$nick = $usuario['nick'];
$email = $usuario['email'];
$nombre = $usuario['nombre'];
$seguidores = $usuario['seguidores'];
$siguiendo = $usuario['siguiendo'];

$numseguidores = is_array($seguidores) ? count($seguidores) : 0;
$numsiguiendo = is_array($siguiendo) ? count($siguiendo) : 0;

unset($_SESSION['emailUser']);
// Comprobar si el usuario de la sesión está en el array de seguidores
$emailSesion = $_SESSION['email'];
$esSeguidor = is_array($seguidores) && in_array($emailSesion, $seguidores);

// Determinar el texto del botón
$textoBoton = $esSeguidor ? "Dejar de Seguir" : "Seguir";

// Cambiar la acción del formulario dependiendo del estado
$accionFormulario = $esSeguidor ? "DejarSeguir" : "Seguir";

$contenidoPrincipal = <<<EOS
    <div class="tweet-header">
        <strong>Nick: {$nick}</strong>
    </div>
    <div class="tweet-content">

        <p>Nombre: {$nombre}</p>
        <p>Seguidores: {$numseguidores}</p>
        <p>Siguiendo: {$numsiguiendo}</p>
        <form method="POST" action="../Controlador/Usuario_controlador.php">
                <input type="hidden" name="emailpropio" value="{$emailSesion}">
                <input type="hidden" name="emailseguir" value="{$email}">
                <button type="submit" class="boton_lista" name="Seguir">{$textoBoton}</button>
        </form>

    </div>  
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
