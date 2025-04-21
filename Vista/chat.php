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

$host = $_SERVER['HTTP_HOST']; 
$conversacionId = $_GET['conversacionId'] ?? null;
$compartir = $_GET['compartir'] ?? null;
$id_comp = $_GET['id'] ?? null;

if($conversacionId == null){
    $contenidoPrincipal = <<<EOS
        <div>
            <p>Conversación eliminada</p>
        </div>
    EOS;

}
else{
    if(!$_SESSION['conversacion']){
        header('Location: ../Controlador/Conversaciones_controlador.php?ObtenerConversacion=true&conversacionId=' . $conversacionId . '&compartir=' . $compartir . '&id=' . $id_comp);
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
        <a href="../Vista/unsetPerfilPublico.php?nick_user=$otroUsuario" class="nick-link"><h1>$otroUsuario <span id="estado">🔴</span> </h1></a>
        <div id="chat-cont" class="chat-container">
    EOS;
    
    
    foreach ($conversacion['mensajes'] as $mensaje) {
            $contenido = strip_tags($mensaje['contenido'], '<a>');
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
    
    
    if($compartir == "publicacion"){
    
        $texto_men = "Publicación compartida: <a onclick=\"event.stopPropagation();\" href=\"http://$host/Controlador/Publicacion_controlador.php?publi_id=true&id=$id_comp\">Ver publicación</a>";
        $contenidoPrincipal .= <<<EOS
            <script type="text/javascript">
                enviarMensaje('{$_SESSION['nick']}', '$otroUsuario', '$conversacionId', '$texto_men', true);
            </script>
        EOS;
    }
    else if($compartir == "receta"){
        $texto_men = "Receta compartida: <a onclick=\"event.stopPropagation();\" href=\"http://$host/Controlador/Receta_controlador.php?publi_id=true&id=$id_comp\">Ver receta</a>";
        $contenidoPrincipal .= <<<EOS
            <script type="text/javascript">
                enviarMensaje('{$_SESSION['nick']}', '$otroUsuario', '$conversacionId', '$texto_men', true);
            </script>
        EOS;
    }
    else{
        if(count($conversacion['mensajes']) == 0){
            $contenidoPrincipal .= <<<EOS
                <h2 id="mensajeVacio" style="color: white;">No hay mensajes</h2>
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
            <button type="button" class="btn-enviar" onclick="enviarMensaje('{$_SESSION['nick']}', '$otroUsuario', '$conversacionId', document.getElementById('contenido').value, false)">Enviar</button>
        </form>
    
    EOS;
}


require_once __DIR__ . "/plantillas/plantilla.php";

?>

<script src="../Recursos/js/chat.js"></script>

<script type="text/javascript">
    <?php if ($conversacionId !== null): ?>
        window.onload = function() {
            var conversacionId = "<?php echo $conversacionId; ?>";
            var usuarioActual = "<?php echo $_SESSION['nick']; ?>";
            
            socket.emit('entrar_chat', { usuario: usuarioActual, chatId: conversacionId });
        };

        window.onbeforeunload = function() {
            var usuarioActual = "<?php echo $_SESSION['nick']; ?>";
            
            socket.emit('salir_chat', usuarioActual);
        };

        socket.on("actualizar-estado-usuarios", (usuariosConectados) => {
            var estadoSpan = document.getElementById("estado");
            var usuario = "<?php echo $otroUsuario; ?>";
            var usuarioActual = "<?php echo $_SESSION['nick']; ?>";
            var conversacionId = "<?php echo $conversacionId; ?>";

            if(usuariosConectados.hasOwnProperty(usuario) && usuariosConectados.hasOwnProperty(usuarioActual) && usuariosConectados[usuario] == usuariosConectados[usuarioActual]){
                estadoSpan.textContent = '🟢'; 
            } else {
                estadoSpan.textContent = '🔴'; 
            }
        });
    <?php endif; ?>

</script>

