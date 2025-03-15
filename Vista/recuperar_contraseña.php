
<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$tituloPagina = "Recuperar contraseña";

$contenidoPrincipal = <<<EOS
    <p><strong>Introduce tu correo electrónico</strong></p>
    <form method="POST" class="formulario" action="../Controlador/Usuario_controlador.php">
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <button type="submit" name="RecuperarCon">Enviar enlace de recuperación</button>
    </form>
EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}


require_once __DIR__."/plantillas/plantilla.php";

