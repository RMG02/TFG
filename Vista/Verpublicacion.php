<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

if (!isset($_SESSION['publicaciones'])) {
   header('Location: ../Controlador/Publicacion_controlador.php?listarPublicaciones=true');
   exit;
}

require_once __DIR__ . "/plantillas/respuestas.php";

$modalId = 0;
$modalComId = 0;
$principal = true;
$error = "";
$mensaje = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

date_default_timezone_set('Europe/Madrid');


$tituloPagina = "Página Principal";

$contenidoPrincipal = <<<EOS
   <h1>Tweet</h1>

EOS;
    $nickuser = $_SESSION['nick'];
    $nick = $_POST['nick'];
    $texto = $_POST['texto'];
    $id = $_POST['id'];
    $Hora = $_POST['hora'];
    $multimedia = $_POST['multimedia'];
    $comentarios = $_POST['comentarios'];
    $num_comentarios = $_POST['num_comentarios'];
    $likes = $_POST['likes'];
    $dislikes = $_POST['dislikes'];
    $numlikes = $_POST['numlikes'];
    $numdislikes = $_POST['numdislikes'];
    if ($multimedia) {
        $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación'>";
        } elseif (in_array($extension, ['mp4', 'webm'])) {
            $multi = "<video controls><source src='../Recursos/multimedia/$multimedia' type='video/$extension'></video>";
        }
    } else {
        $multi = '';
    }

    $contenidoPrincipal .= <<<EOS
        <div class="tweet" id="publistas">
            <div class="tweet-header">
                <strong>$nick</strong> <span class="tweet-time">$Hora</span>
            </div>
            <div class="tweet-content">
                $multi
                <p>$texto</p>
                <div class="comentarios-icon">
                    <i class="fa fa-comments"></i> $num_comentarios
                </div>
            </div>
            <div class="reacciones-icon">
                    <form method="POST" action="../Controlador/Publicacion_controlador.php">
                        
                        <button type="submit" name="darlike" class="btn-like">
                            <input type="hidden" name="id_publi" value="$id">
                            <input type="hidden" name="nick_user" value="$nickuser">
                            <i class="fa fa-thumbs-up"></i> $numlikes
                        </button>
                        <button type="submit" name="dardislike" class="btn-dislike">
                            <input type="hidden" name="id_publi" value="$id">
                            <input type="hidden" name="nick_user" value="$nickuser">
                            <i class="fa fa-thumbs-down"></i> $numdislikes
                        </button>
                    </form>
            </div>
        </div>
        <div id="$modalId" class="modal_publi">
            <div class="modal_publi-content">
                <span class="close_publi">&times;</span>
                <div class="tweet-header">
                    <strong>$nick</strong>
                    <span class="tweet-time">$Hora</span>
                </div>
                <div class="tweet-content">
                    $multi
                    <p>$texto</p>
                    <button type="button" class="botonPubli" name="comen">Añadir Comentario</button>
                    <div id="comen-$modalId" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario">
                                <input type="hidden" name="id_publi" value="$id">
                                <input type="hidden" name="principal" value="true">
                                <textarea name="texto" placeholder="Escribe un comentario..."></textarea>
                                <input type="file" name="archivo"> 
                                <button type="submit" class="botonPubli" name="agregarComentario">Añadir Comentario</button>
                            </form>
                        </div>
                    </div>
    EOS;

    if ($nick == $_SESSION['nick']) {
        $contenidoPrincipal .= <<<EOS
                        <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario">
                            <input type="hidden" name="id_publi" value="$id">
                            <input type="hidden" name="principal" value="true">
                            <input type="hidden" name="multi" value="../Recursos/multimedia/$multimedia"> 
                            <button type="submit" class="botonPubli" name="eliminarPublicacion">Eliminar publicación</button>
                        </form>
                        <button type="button" class="botonPubli" name="editar">Editar publicación</button>
                        <div id="edit-$modalId" class="modal">
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario">
                                    $multi
                                    <textarea name="contenido">$texto</textarea>
                                    <input type="hidden" name="archivo_origen" value="$multimedia"> 
                                    <input type="file" name="nuevo_archivo"> 
                                    <input type="hidden" name="principal" value="true">
                                    <input type="hidden" name="id_publi" value="$id">
                                    <button type="submit" class="botonPubli" name="editarPublicacion">Guardar cambios</button>
                                </form>
                            </div>
                        </div>
        EOS;
    }
    $contenidoPrincipal .= <<<EOS
        <hr>
        <h3>Comentarios</h3>       
    EOS;

    if (!empty($comentarios)) {
        foreach ($comentarios as $comentario) {
            $usuario = $comentario['usuario'];
            $id_com = $comentario['id_comentario']['$oid'];
            $tex = $comentario['texto'];
            $mult = $comentario['multimedia'] ?? '';
            $fecha = date('d/m/Y H:i:s', strtotime($comentario['fecha']));
            $num_respuestas = count($comentario['respuestas'] ?? []);
            $modalResId = 0;

            if ($mult) {
                $extension = pathinfo($mult, PATHINFO_EXTENSION);
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $multi_com = "<img src='../Recursos/multimedia/$mult' alt='Imagen de la publicación'>";
                } elseif (in_array($extension, ['mp4', 'webm'])) {
                    $multi_com = "<video controls><source src='../Recursos/multimedia/$mult' type='video/$extension'></video>";
                }
            } else {
                $multi_com = '';
            }

            $contenidoPrincipal .= <<<EOS
                        <div class="comentario" name="comentario">
                            $multi_com
                            <strong>$usuario:</strong>
                            <span>$tex</span>
                            <span class="comentario-time">$fecha</span>
                            <div class="comentarios-icon">
                                <i class="fa fa-comments"></i> $num_respuestas
                            </div>
                        </div>
                        <div id="comentario-$modalComId" class="modal_publi">
                            <div class="modal_publi-content">
                                <span class="close_publi">&times;</span>
                                <div class="comentario_mod">
                                    $multi_com
                                    <strong>$usuario:</strong>
                                    <br>
                                    <span>$tex</span>
                                    <span class="comentario-time">$fecha</span>
                                    
            EOS;

            if($usuario == $_SESSION['nick']){
                $contenidoPrincipal .= <<<EOS
                    <button type="button" class="botonPubli" name="editar_com">Editar comentario</button>
                    <div id="editCom-$modalComId" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario">
                                <textarea name="contenido">$tex</textarea>
                                <input type="hidden" name="archivo_origen" value="$mult"> 
                                <input type="file" name="nuevo_archivo"> 
                                <input type="hidden" name="id_comen" value="$id_com"> 
                                <input type="hidden" name="principal" value="true">
                                <input type="hidden" name="id_publi" value="$id">
                                <button type="submit" class="botonPubli" name="editarComentario">Guardar cambios</button>
                            </form>
                        </div>
                    </div>
                    <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario">
                        <input type="hidden" name="id_comen" value="$id_com"> 
                        <input type="hidden" name="principal" value="true">
                        <input type="hidden" name="multi" value="../Recursos/multimedia/$mult"> 
                        <input type="hidden" name="id_publi" value="$id">
                        <button type="submit" class="botonPubli" name="eliminarComentario">Eliminar comentario</button>
                    </form>
                    
                EOS;
            }

            $contenidoPrincipal .= <<<EOS
                            <button type="button" class="botonPubli" name="responder" id="responder-$modalComId">Responder</button>
                            <div id="respuesta-$modalComId" class="modal">
                                <div class="modal-content">
                                    <span class="close">&times;</span>
                                    <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario">
                                        <input type="hidden" name="id_publi" value="$id">
                                        <input type="hidden" name="id_comen" value="$id_com"> 
                                        <input type="hidden" name="principal" value="true">
                                        <input type="hidden" name="esRespuesta" value="true">
                                        <textarea name="texto" placeholder="Escribe un comentario..."></textarea>
                                        <input type="file" name="archivo"> 
                                        <button type="submit" class="botonPubli" name="agregarComentario">Añadir Respuesta</button>
                                    </form>
                                </div>
                            </div>
                            <hr>
                            <h3>Respuestas</h3>
            EOS;
            if (!empty($comentario['respuestas'])) {
                $contenidoPrincipal .= mostrarRespuestas($comentario['respuestas'], $modalComId, $modalResId, $principal, $id_com, $id);
            }
            $contenidoPrincipal.= <<<EOS
                        </div>
                    </div>
                </div>
            EOS;
            
            
            $modalComId++;
        }
    }

    
    $contenidoPrincipal .= <<<EOS
                    </div>
                </div>
            </div>
    EOS;
    $modalId++;


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

<script src="../Recursos/js/formularios_publicacion.js"></script>
<script src="../Recursos/js/filtro_publicacion.js"></script>
<script src="../Recursos/js/Principal.js"></script>

