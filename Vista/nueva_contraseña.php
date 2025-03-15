
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

$tituloPagina = "Nueva contraseña";

$contenidoPrincipal = <<<EOS
    <p><strong>Introduce la nueva contraseña</strong></p>
    <form method="POST" class="formulario" action="../Controlador/Usuario_controlador.php">
        <input type="hidden" name="token" value="$token">
        <input type="password" name="contraseña" placeholder="Nueva contraseña" required>
        <input type="password" name="contraseña2" placeholder="Vuelve a introducir la nueva contraseña" required>
        <button type="submit" name="NuevaCon">Cambiar contraseña</button>
    </form>
EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}


require_once __DIR__."/plantillas/plantilla.php";

