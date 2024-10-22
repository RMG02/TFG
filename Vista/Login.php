
<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$tituloPagina = "Página de Login";

$contenidoPrincipal = <<<EOS
	<form method="POST" class="formulario" action="../Controlador/Usuario_controlador.php">
        <input type="email" name="email" placeholder="email" required>
        <input type="password" name="password" placeholder="contraseña" required>
        <button type="submit" name="login">Iniciar Sesión</button>
    </form>
EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}


require_once __DIR__."/plantillas/plantilla.php";

