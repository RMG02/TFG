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

$id_div = 0;

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
        <div class="crear-foro">
            <h1>$tituloForo</h1>
            <a href="crear_mensaje_foro.php?id_foro=$foroId&suscrito=$suscrito" class="btn-crear" title="Publicar mensaje"><i class="fas fa-plus-circle"></i></a>
        </div>
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
    EOS;
    foreach ($foro['mensajes'] as $mensaje) {
        $multimedia = $mensaje['multimedia'] ?? '';
        $hora = date('d/m/Y H:i:s', strtotime($mensaje['hora']));
        $email = $mensaje['email'];
        $nick = $mensaje['nick'];
        $contenido = strip_tags($mensaje['contenido'], '<a>');
        $id = $mensaje['mensaje_id']['$oid'];

        $multi = '';

        if ($multimedia) {
            $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación'>";
            } elseif (in_array($extension, ['mp4', 'webm'])) {
                $multi = "<video controls><source src='../Recursos/multimedia/$multimedia' type='video/$extension'></video>";
            }
        }

        if($nick == $_SESSION['nick']){
            $nick = "Tú";
        }
        $contenidoPrincipal .= <<<EOS
        <div class="contenedor-publicacion" onclick="abrirModal('mensaje-$id_div', '{$mensaje['nick']}', '{$_SESSION['nick']}')">

            <div class="tweet" id="publistas">
                <div class="tweet-header">
                    <a href="../Vista/unsetPerfilPublico.php?email_user=$email" class="nick-link"><strong>$nick</strong></a> <span class="tweet-time">$hora</span>
                </div>
                <div class="tweet-content">
                    $multi
                    <p>$contenido</p>
                </div>
            </div>
        </div>
        
        <div id="mensaje-$id_div" class="modal_men">
            <div class="modal_men-content">
                <span class="close_men" onclick="cerrarModal('mensaje-$id_div')">&times;</span>  
                <form method="POST" action="../Controlador/Foros_controlador.php">
                    <input type="hidden" name="Mensaje-id" value='$id'>
                    <input type="hidden" name="Foro-id" value='$foroId'>
                    <button type="submit" class="mod-men" name="EliminarPubli">Eliminar publicación</button>
                </form>
                <button type="button" class="mod-men" onclick="mostrarEditar('$id_div')">Editar publicacion</button>
                <div id="edit-$id_div" class="modal_men">
                    <div class="modal_men-content">
                        <span class="close_men" onclick="cerrarModal('edit-$id_div')">&times;</span>
                        <p>Modifica tu publicación</p>
                        <form method="POST" enctype="multipart/form-data" action="../Controlador/Foros_controlador.php">
                            <textarea name="contenido">$contenido</textarea>
                            <input type="hidden" name="archivo_origen" value="$multimedia"> 
                            <input type="file" name="nuevo_archivo"> 
                            <input type="hidden" name="id_mensaje" value="$id">
                            <input type="hidden" name="id_foro" value="$foroId">
                            <button type="submit" class="mod-men" name="EditarPubli">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        EOS;
        $id_div += 1;

    }
    
    if (empty($foro['mensajes'])) {
        $contenidoPrincipal .= <<<EOS
            <h2 id="mensajeVacio">No hay publicaciones</h2>
        EOS;    
    }
    
}

require_once __DIR__ . "/plantillas/plantilla.php";

?>

<script src="../Recursos/js/foro.js"></script>
