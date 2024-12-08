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
    $contenidoPrincipal = <<<EOS
    <div class="filtros-notificaciones">
        <button onclick="mostrarTodas()">Mostrar todas</button>
        <button onclick="mostrarNoVistas()">Mostrar no vistas</button>
    </div>
    <div class="lista-notificaciones">
    EOS;


    $contenidoPrincipal .= '<div class="lista-notificaciones">';
    foreach ($notificaciones as $notificacion) {
        $fecha = date('d/m/Y H:i:s', strtotime($notificacion['fecha']));
        $vista = $notificacion['vista'] ? 'vista' : 'no-vista';
        $id = $notificacion['_id']['$oid'];
        $contenidoPrincipal .= <<<EOS
            <div class="notificacion $vista">
                <p>{$notificacion['mensaje']}</p>
                <p><a href="{$notificacion['enlace']}">Ver publicación</a></p>
                <p><small>{$notificacion['fecha']}</small></p>
        EOS;
        if (!$vista) {
            $contenidoPrincipal .= '<button onclick="marcarComoVista('.$id.')">Marcar como vista</button>';
        }
        $contenidoPrincipal .= '</div>';
    }
    $contenidoPrincipal .= '</div>';
}
else{
    $contenidoPrincipal = '<h3>No hay notificaciones</h3>';
}


require_once __DIR__ . "/plantillas/plantilla.php";
?>

<script src="../Recursos/js/MostrarNoti.js"></script>
