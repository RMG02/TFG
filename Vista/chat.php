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
if (count($conversacion['mensajes']) > 0){
    foreach ($conversacion['mensajes'] as $mensaje) {
        $contenido = htmlspecialchars($mensaje['contenido'], ENT_QUOTES);
        $emisor = htmlspecialchars($mensaje['usuario_emisor'], ENT_QUOTES);
        $hora = date('d/m/Y H:i:s', strtotime($mensaje['hora']));
        $id = $mensaje['mensaje_id']['$oid'];

        if($mensaje['usuario_emisor'] == $_SESSION['nick']){
            $contenidoPrincipal .= <<<EOS
                <div class="mensaje_enviado" id="mensajeEnviado-$id" onclick="mostrarOpciones('$id')">
                    <p class="nombre-usuario"><strong>Tú</strong></p>
                    <p id="contenido-$id">$contenido</p>
                    <p><small>$hora</small></p>
                </div>
                <div id="mensaje-$id" class="modal_men">
                    <div class="modal_men-content">
                        <span class="close_men" onclick="cerrarModal('mensaje-$id')">&times;</span>  
                        <button type="button" class="mod-men" onclick="eliminarMensaje('$id', '$otroUsuario')">Eliminar mensaje</button>
                        <button type="button" class="mod-men" onclick="mostrarEditar('$id')">Editar mensaje</button>
                        <div id="edit-$id" class="modal_men">
                            <div class="modal_men-content">
                                <span class="close_men" onclick="cerrarModal('edit-$id')">&times;</span>
                                <p>Modifica tu mensaje</p>
                                <input type="text" id="nuevoContenido-$id" value="$contenido" class="input-mensaje"> 
                                <button type="button" class="mod-men" onclick="editarMensaje('$id', '$otroUsuario', document.getElementById('nuevoContenido-$id').value); cerrarModal('edit-$id'); cerrarModal('mensaje-$id')">Editar</button>
                            </div>
                        </div>
                    </div>
                </div>
            EOS;
        }
        else{
            $contenidoPrincipal .= <<<EOS
                <div class="mensaje_recibido" id="mensajeRecibido-$id">
                    <p class="nombre-usuario"><strong>$emisor</strong></p>
                    <p id="contenido-$id">$contenido</p>
                    <p><small>$hora</small></p>
                </div>
            EOS;
        }
        
    }
}
else{
    $contenidoPrincipal .= <<<EOS
        <h2>No hay mensajes</h2>
    EOS;
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

<script src="../Recursos/js/chat.js"></script>
