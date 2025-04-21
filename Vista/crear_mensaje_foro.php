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

if (!isset($_SESSION['foro'])) {
    header('Location: ../Controlador/Foros_controlador.php?ObtenerForoId=true&foroId=' . $id . '&suscrito=' . $_GET['suscrito']);
    exit;
}

$foro = $_SESSION['foro'];
unset($_SESSION['foro']);

if($_GET['suscrito']){
    $notificaciones = json_encode($foro['notificaciones']); 

    $contenidoPrincipal = <<<EOS
        <form method="POST" class="formulario" enctype="multipart/form-data" action="../Controlador/Foros_controlador.php">
            <textarea name="contenido" placeholder="Escribe tu publicación aquí..." required></textarea>
            <input type="file" name="archivo" style="background-color: white;"> 
            <input type="hidden" name="id_foro" value="$id">
            <input type="hidden" name="suscrito" value="{$_GET['suscrito']}">
            <button type="submit" name="CrearMensaje" onclick='enviarPublicacionForo("$id", $notificaciones, "{$_SESSION['nick']}", "{$foro['titulo']}")'>Publicar</button>
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
<script src="../Recursos/js/socket.js"></script>



