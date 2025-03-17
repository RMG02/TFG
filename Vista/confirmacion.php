
<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";

$token = $_GET['token'];
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$tituloPagina = "Confirmar cuenta";

$contenidoPrincipal = <<<EOS
    <p><strong>Pulsa el boton para confirmar</strong></p>
    <form method="POST" class="formulario" action="../Controlador/Usuario_controlador.php">
        <input type="hidden" name="token" value="$token">
        <button type="submit" name="Nuevaconfirmacion">Confirmar cuenta</button>
    </form>
EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}


require_once __DIR__."/plantillas/plantilla.php";

