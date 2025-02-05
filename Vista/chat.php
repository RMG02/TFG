<?php

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

// Redirigir si no estás logueado
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

// Configuración de la zona horaria
date_default_timezone_set('Europe/Madrid');

// Título de la página
$tituloPagina = "Chat";


$conversacionId = $_GET['conversacionId'] ?? null;

if(!isset($_SESSION['conversacion'])){
    header('Location: ../Controlador/Conversaciones_controlador.php?ObtenerConversacion=true&conversacionId=' . $conversacionId);
}

$conversacion = $_SESSION['conversacion'];
unset($_SESSION['conversacion']);

foreach ($conversacion['usuarios'] as $usu){
    if($usu != $_SESSION['nick']){
        $otroUsuario = $usu;
    }
}

// Contenido principal
$contenidoPrincipal = <<<EOS
   <h1>$otroUsuario</h1>
   <div id="chat-cont" class="chat-container">
EOS;

foreach ($conversacion['mensajes'] as $mensaje) {
    $contenido = htmlspecialchars($mensaje['contenido'], ENT_QUOTES);
    $emisor = htmlspecialchars($mensaje['usuario_emisor'], ENT_QUOTES);
    $hora = date('d/m/Y H:i:s', strtotime($mensaje['hora']));

    if($mensaje['usuario_emisor'] == $_SESSION['nick']){
        $contenidoPrincipal .= <<<EOS
            <div class="mensaje_enviado">
                <p><strong>Tú:</strong> $contenido</p>
                <p><small>$hora</small></p>
            </div>
        EOS;
    }
    else{
        $contenidoPrincipal .= <<<EOS
            <div class="mensaje_recibido">
                <p><strong>$emisor:</strong> $contenido</p>
                <p><small>$hora</small></p>
            </div>
        EOS;
    }
    
}

$contenidoPrincipal .= <<<EOS
    </div>
    
    <form id="form-mensaje" class="form-chat" onsubmit="return false;"> 
        <input type="hidden" id="conversacionId" value="$conversacionId">
        <input type="hidden" id="usuario_receptor" value="$otroUsuario">
        <input type="hidden" id="usuario_emisor" value="{$_SESSION['nick']}">
        <input type="text" id="contenido" placeholder="Escribe tu mensaje..." class="input-mensaje">
        <button type="button" class="btn-enviar" onclick="enviarMensaje('{$_SESSION['nick']}', '$otroUsuario', '$conversacionId', document.getElementById('contenido').value)">Enviar</button>
    </form>

EOS;

require_once __DIR__ . "/plantillas/plantilla.php";

?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var chatContainer = document.querySelector(".chat-container");
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
});
</script>
