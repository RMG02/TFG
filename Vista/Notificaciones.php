<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}


if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

if (isset($_SESSION['id_publi'])) {
    unset($_SESSION['id_publi']);
}

if (!isset($_SESSION['notificaciones_usuario'])) {
    header('Location: ../Controlador/Notificacion_controlador.php?listarNotificacionesUsuario=true');
}

$error = "";
$mensaje = "";
$notificaciones = json_decode($_SESSION['notificaciones_usuario'], true);
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

date_default_timezone_set('Europe/Madrid');

$tituloPagina = "Notificaciones";



if(!empty($notificaciones)){

    $contenidoPrincipal = '<div class="lista-notificaciones">';
    foreach ($notificaciones as $notificacion) {
        $fecha = date('d/m/Y H:i:s', strtotime($notificacion['fecha']));
        $id = $notificacion['_id']['$oid'];
        $contenidoPrincipal .= <<<EOS
            <div class="notificacion vista">
                <p><strong>{$notificacion['mensaje']}</strong></p>
        EOS;

        if($notificacion['tipo_publicacion'] == "publicacion"){
            $contenidoPrincipal .= <<<EOS
                <p><a href="{$notificacion['enlace']}">Ver publicación</a></p>
            EOS;
        }
        else if($notificacion['tipo_publicacion'] == "receta"){
            $contenidoPrincipal .= <<<EOS
                <p><a href="{$notificacion['enlace']}">Ver receta</a></p>
            EOS;
        }
        else if($notificacion['tipo_publicacion'] == "follows"){
            $contenidoPrincipal .= <<<EOS
                <p><a href="{$notificacion['enlace']}">Ver usuario</a></p>
            EOS;
        }

        $contenidoPrincipal .= <<<EOS
                <p><small>$fecha</small></p>
                 <form method="POST" action="../Controlador/Notificacion_controlador.php" class="formulario">
                    <input type="hidden" name="id_noti" value="$id">
                    <button type="submit" class="eliminar-notification" name="eliminarNotificacion"><i class="fas fa-times"></i></button>
                </form>
            </div>
        EOS;
    }
    $contenidoPrincipal .= '</div>';
}
else{
    $contenidoPrincipal = '<h3>No hay notificaciones</h3>';
}

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
EOS;
}

if ($mensaje != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="mensaje">$mensaje</p>
EOS;
}


require_once __DIR__ . "/plantillas/plantilla.php";
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    localStorage.setItem('notificationCounter', 0);

    var notificationCounter = document.getElementById('notification-counter');
    var contadorActual = parseInt(localStorage.getItem('notificationCounter')) || 0;
    notificationCounter.textContent = contadorActual;
    notificationCounter.style.display = contadorActual > 0 ? 'inline' : 'none';
});
</script>
