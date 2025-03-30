<?php

$tituloPagina = "Añadir mensaje foro";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

$error = "";
$id = $_GET['id_foro'];
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if($_GET['suscrito']){
    $contenidoPrincipal = <<<EOS
        <form method="POST" class="formulario" action="../Controlador/Foros_controlador.php">
            <textarea name="contenido" placeholder="Escribe tu publicación aquí..." required></textarea>
            <input type="file" name="archivo" style="background-color: white;"> 
            <input type="hidden" name="id_foro" value="$id">
            <input type="hidden" name="suscrito" value="{$_GET['suscrito']}">
            <button type="submit" class="botonInit" name="CrearMensaje">Publicar</button>
        </form>
    EOS;
}
else{
    $contenidoPrincipal = <<<EOS
        <h2>Tienes que estar suscrito al foro para poder publicar contenido</h2>
    EOS;
}


if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}


require_once __DIR__."/plantillas/plantilla.php";
?>

<script src="../Recursos/js/aviso.js"></script>



