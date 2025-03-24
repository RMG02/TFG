<?php

$tituloPagina = "Crear foro";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

$error = "";

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$contenidoPrincipal = <<<EOS
	<form method="POST" class="formulario" action="../Controlador/Foros_controlador.php">
        <label for="titulo">Título del foro:</label>
        <input type="text" name="titulo" required>

        <label for="titulo">Descripción del foro:</label>
        <textarea name="descripcion"required></textarea>

        <button type="submit" class="botonInit" name="CrearForo">Crear foro</button>
    </form>

EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}


require_once __DIR__."/plantillas/plantilla.php";
?>

<script src="../Recursos/js/aviso.js"></script>



