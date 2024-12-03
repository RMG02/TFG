<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
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


$publicaciones = json_decode($_SESSION['publicaciones'], true);
$tituloPagina = "Página Principal";



$contenidoPrincipal = <<<EOS
   <h1>Bienvenido {$_SESSION['nick']}</h1>
   <button id="publicaBtn">Publica</button> 
   <div id="formPublicacion" class="modal"> 
      <div class="modal-content">
         <span class="close">&times;</span>
         <form class="formulario" method="post" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php"> 
            <textarea name="contenido" placeholder="Escribe tu publicación aquí..."></textarea> 
            <input type="hidden" name="principal" value="$principal">
            <input type="file" name="archivo"> 
            <button type="submit" name="crearPublicacion">Publicar</button> 
         </form> 
      </div>
   </div>

<input type="text" id="buscador" onkeyup="filtrarUsuarios()" placeholder="Buscar por nick...">
<div id="publicaciones">
EOS;

foreach ($publicaciones as $publicacion) {
    $nickuser = $_SESSION['nick'];
    $nick = $publicacion['nick'];
    $texto = $publicacion['contenido'];
    $id = $publicacion['_id']['$oid'];
    $Hora = date('d/m/Y H:i:s', strtotime($publicacion['created_at']));
    $multimedia = $publicacion['multimedia'] ?? '';
    $comentarios = $publicacion['comentarios'];
    $num_comentarios = count($comentarios);
    $likes = $publicacion['likes'];
    $dislikes = $publicacion['dislikes'];
    $numlikes = count($likes ?? []);
    $numdislikes = count($dislikes ?? []);
    $host = $_SERVER['HTTP_HOST']; 
    $urlTweet = "$host/Vista/Verpublicacion.php?id=$id";
    $jsonComentarios = htmlspecialchars(json_encode($comentarios), ENT_QUOTES, 'UTF-8');
    $jsonLikes = htmlspecialchars(json_encode($likes), ENT_QUOTES, 'UTF-8');
    $jsonDislikes = htmlspecialchars(json_encode($dislikes), ENT_QUOTES, 'UTF-8');
    

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
                            <i class="fas fa-thumbs-up"></i> $numlikes
                        </button>
                        <button type="submit" name="dardislike" class="btn-dislike">
                            <input type="hidden" name="id_publi" value="$id">
                            <input type="hidden" name="nick_user" value="$nickuser">
                            <i class="fas fa-thumbs-down"></i> $numdislikes
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
        <div class="comp" id="publicomp">
            <form method="POST" action="../Vista/Verpublicacion.php?id=$id" class="formulario">
                <button type="submit" class="botonPubli" name="Verpublicacion">Ver Publicación</button>
            </form>
            <div class="share-icon">
                <input type="text" value="$urlTweet" readonly>
                <button onclick="copiarEnlace(this.previousElementSibling)">Copiar enlace</button>
            </div>
            
        </div>
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
                $contenidoPrincipal .= mostrarRespuestas($comentario['respuestas'], $modalComId, $modalResId, $principal, $id);
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

<script src="../Recursos/js/formularios_publicacion.js"></script>
<script src="../Recursos/js/filtro_publicacion.js"></script>
<script src="../Recursos/js/Principal.js"></script>

