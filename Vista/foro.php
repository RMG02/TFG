<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

date_default_timezone_set('Europe/Madrid');

$tituloPagina = "Foro";

$foroId = $_GET['foroId'] ?? null;

if ($foroId == null) {
    $contenidoPrincipal = <<<EOS
        <div>
            <p>Foro eliminado</p>
        </div>
    EOS;
} else {
    
    if (!isset($_SESSION['foro'])) {
        header('Location: ../Controlador/Foros_controlador.php?ObtenerForoId=true&foroId=' . $foroId);
        exit;
    }

    $foro = $_SESSION['foro'];
    unset($_SESSION['foro']);

    $tituloForo = $foro['titulo'];
    $descripcionForo = $foro['descripcion'];
    $creadorForo = $foro['creador']; 
    $suscrito = in_array($_SESSION['nick'], $foro['suscriptores']);

    $contenidoPrincipal = <<<EOS
        <h1>$tituloForo</h1>
        <h3>$descripcionForo</h3>
        <div class="foro-acciones">
    EOS;

    if($_SESSION['nick'] === $creadorForo){
        $contenidoPrincipal .= <<<EOS
        <form method="POST" action="../Controlador/Foros_controlador.php">
            <input type="hidden" name="id" value="$foroId">
            <button type="submit" class="boton_lista" name="eliminarForo">Eliminar foro</button>
        </form>
        EOS;
    }

    if($suscrito){
        $contenidoPrincipal .= <<<EOS
            <form method="POST" action="../Controlador/Foros_controlador.php">
                <input type="hidden" name="id" value="$foroId">
                <button type="submit" class="btn-suscripcion" name="Desuscribirforo">Desuscribirse</button>
            </form>
            
        EOS;
    }
    else{
        $contenidoPrincipal .= <<<EOS
            <form method="POST" action="../Controlador/Foros_controlador.php">
                <input type="hidden" name="id" value="$foroId">
                <button type="submit" class="btn-suscripcion" name="Suscribirforo">Suscribirse</button>
            </form>
        EOS;
    }


    $contenidoPrincipal .= <<<EOS
        </div>
        <div id="foro-contenedor">
    EOS;
    if($suscrito){
        foreach ($foro['mensajes'] as $mensaje) {
            $contenido = strip_tags($mensaje['contenido'], '<a>');
            $emisor = htmlspecialchars($mensaje['usuario_emisor'], ENT_QUOTES);
            $hora = date('d/m/Y H:i:s', strtotime($mensaje['hora']));
            $id = $mensaje['mensaje_id']['$oid'];

            /*if($mensaje['usuario_emisor'] == $_SESSION['nick']){
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
            }*/
            
        }
    
        if (empty($foro['mensajes'])) {
            $contenidoPrincipal .= <<<EOS
                <h2 id="mensajeVacio">No hay mensajes</h2>
            EOS;    
        }
    
        $contenidoPrincipal .= <<<EOS
            </div>
            <form id="form-mensaje" class="form-foro" onsubmit="return false;">
                <input type="hidden" id="foroId" value="$foroId">
                <input type="hidden" id="usuario_emisor" value="{$_SESSION['nick']}">
                <input type="text" id="contenido" placeholder="Escribe tu mensaje..." class="input-mensaje">
                <button type="button" class="btn-enviar" onclick="enviarMensajeForo('{$_SESSION['nick']}', '$foroId', document.getElementById('contenido').value)">Enviar</button>
            </form>
        EOS;
    }else{
        $contenidoPrincipal .= <<<EOS
                <h2 id="mensajeVacio">Debes suscribirte para ver el contenido</h2>
        EOS;  
    }
    
}

require_once __DIR__ . "/plantillas/plantilla.php";

?>
