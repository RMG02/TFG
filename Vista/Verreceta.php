<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

require_once __DIR__ . "/plantillas/respuestas.php";

$modalId = 0;
$modalComId = 0;
$principal = true;
$tipo_publicacion = "receta";
$error = "";
$mensaje = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
}
$receta = "";

$idreceta = $_GET['id'] ?? null;

if (!isset($_SESSION['id_publi'])) {
    header('Location: ../Controlador/Receta_controlador.php?publi_id=true&id='.$idreceta);
    exit;
}

if($_SESSION['recedisponible'] == false){
    $tituloPagina = "Receta";
    
    $contenidoPrincipal = <<<EOS
        <div>
            <p>Receta ya no disponible</p>
        </div>
    EOS;
}else{

        $receta = json_decode($_SESSION['id_publi'], true);
        date_default_timezone_set('Europe/Madrid');


        $tituloPagina = "Receta";

            $nickuser = $_SESSION['nick'];
            $email = $receta['email'];
            $nick = $receta['nick'];
            $titulo = $receta['titulo'];
            $ingredientes = $receta['ingredientes'];
            $preparacion = $receta['preparacion'];
            $ingredientesx = nl2br($ingredientes);
            $preparacionx = nl2br($preparacion);
            $id = $receta['_id']['$oid'];
            $Hora = date('d/m/Y H:i:s', strtotime($receta['created_at']));
            $multimedia = $receta['multimedia'];
            $comentarios = $receta['comentarios'];
            $num_comentarios = count($comentarios);
            $likes = $receta['likes'];
            $dislikes = $receta['dislikes'];
            $numlikes = count($likes);
            $numdislikes = count($dislikes);
            $likes_cadena = implode(",", $likes);
            $dislikes_cadena = implode(",", $dislikes);
            $host = $_SERVER['HTTP_HOST']; 
            $urlTweet = "$host/Vista/Verreceta.php?id=$id";
            $dificultad = (int) $receta['dificultad'];
            $tiempo = $receta['tiempo'];
            $extension = "";
            $dificultadHTML = '';
            for ($j = 0; $j < $dificultad; $j++) {
                $dificultadHTML .= '<span class="iconify" data-icon="mdi:chef-hat" data-inline="false"></span>';
            }



        if ($multimedia) {
            $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación' class='imagen-vista'>";
                $multi_editar = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación' class='imagen-editar'>";
            } 
        } else {
            $multi = '';
            $multi_editar = '';
        }
            

        $contenidoPrincipal = <<<EOS
            <div class="tweet-header">
                <a href="../Vista/unsetPerfilPublico.php?email_user=$email" class="nick-link"><strong>$nick</strong></a>
                <span class="tweet-time">$Hora</span>
            </div>
            <div class="recet-content">
                <h2>$titulo</h2>
                $multi
                <h3>Tiempo<h3>
                <div class="tiempo-receta">
                    <i class="fa fa-clock"></i> <span>$tiempo min</span>
                </div></p>
                <div class="dificultad-receta">
                    <span>Dificultad: </span>
                    $dificultadHTML
                </div>
                <h3>Ingredientes:</h3>
                <p>$ingredientesx</p>
                <h3>Preparacion:</h3>
                <p>$preparacionx</p>
                
        EOS;
        if($nickuser != $nick){
            
            $contenidoPrincipal .= <<<EOS
                <button type="button" class="botonPubli" name="comen">Añadir Comentario</button>
                <div id="comen-$modalId" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <form method="POST" enctype="multipart/form-data" action="../Controlador/Receta_controlador.php" class="formulario" onsubmit="NuevoComentario(event, '$nickuser','$nick', '$id', '$tipo_publicacion', '')">
                            <input type="hidden" name="id_publi" value="$id">
                            <textarea name="texto" placeholder="Escribe un comentario..."></textarea>
                            <input type="file" name="archivo"> 
                            <input type="hidden" name="usuario_origen" value="$nick"> 
                            <button type="submit" class="botonPubli" name="agregarComentario">Añadir Comentario</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tweet-footer">
                <div class="reacciones-icon">
            EOS;
        }
        else{

            $contenidoPrincipal .= <<<EOS
                </div>
                <div class="tweet-footer">
                    <div class="reacciones-icon">
            EOS;
        }
        if($nickuser == $nick){
            $contenidoPrincipal .= <<<EOS
                <form method="POST" action="../Controlador/Receta_controlador.php">
            EOS;
        }
        else{
            $contenidoPrincipal .= <<<EOS
                <form method="POST" action="../Controlador/Receta_controlador.php" onsubmit="enviarDatos(event, '$nickuser','$nick', '$id', '$likes_cadena', '$dislikes_cadena', '$tipo_publicacion', '', '')">
            EOS;
        }
        $contenidoPrincipal .= <<<EOS
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
                <div class="dropdown">
                    <button class="dropbtn">⋮</button>
                    <div class="dropdown-content">
        EOS;

        if ($nick == $_SESSION['nick'] || $_SESSION['admin'] == true) {
            $contenidoPrincipal .= <<<EOS
                        <form method="POST" action="../Controlador/Receta_controlador.php" class="formulario" onsubmit="unset()">
                            <input type="hidden" name="id_publi" value="$id">
                            <input type="hidden" name="multi" value="../Recursos/multimedia/$multimedia"> 
                            <button type="submit" class="botonPubli" name="eliminarReceta">Eliminar receta</button>
                        </form>
                        <button type="button" class="botonPubli" name="editar">Editar receta</button>
            EOS;
        }

        $contenidoPrincipal .= <<<EOS
                        <input type="hidden" value="$urlTweet" readonly>
                        <button type="button" class="botonPubli" name="compartir" onclick="copiarEnlace(this.previousElementSibling)">Compartir receta</button> 
                        <button id="download-btn" 
                            data-title="$titulo" 
                            data-ingredients="$ingredientes" 
                            data-preparation="$preparacion" 
                            data-dificultad=$dificultad
                            data-tiempo=$tiempo
                            data-nick="$nick" 
                            data-multimedia="$multimedia" 
                            data-extension="$extension">Descargar PDF
                        </button>           
                    </div>
                </div>
                <div id="edit-$modalId" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <form method="POST" enctype="multipart/form-data" action="../Controlador/Receta_controlador.php" class="formulario">
                            $multi_editar
                            <textarea name="titulo">$titulo</textarea>
                            <textarea name="ingredientes">$ingredientes</textarea>
                            <textarea name="preparacion">$preparacion</textarea>
                            <input type="hidden" name="archivo_origen" value="$multimedia"> 
                            <input type="number" name="tiempo" value="$tiempo">
                            <p>Dificultad de la receta</p><select name="dificultad" value="$dificultad">
                            <option value=1>1</option>
                            <option value=2>2</option>
                            <option value=3>3</option>
                            <option value=4>4</option>
                            <option value=5>5</option>  
                            </select>
                            <input type="file" name="nuevo_archivo"> 
                            <input type="hidden" name="id_publi" value="$id">
                            <button type="submit" class="botonPubli" name="editarReceta">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            </div>
            <hr>
        EOS;
            




            if (!empty($comentarios)) {
                $contenidoPrincipal .= '<h3>Comentarios</h3>';
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
                        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                            $multi_com = "<img src='../Recursos/multimedia/$mult' alt='Imagen de la publicación'  class='imagen-respuesta'>";
                            $multi_editar = "<img src='../Recursos/multimedia/$mult' alt='Imagen de la publicación' class='imagen-editar'>";

                        } 
                    } else {
                        $multi_com = '';
                        $multi_editar = '';
                    }

                    $contenidoPrincipal .= <<<EOS
                                <div class="comentario" name="comentario">
                                    $multi_com
                                    <br>
                                    <a href="../Vista/unsetPerfilPublico.php?nick_user=$usuario" class="nick-link" onclick="event.stopPropagation();"><strong>$usuario:</strong></a>
                                    <span>$tex</span>
                                    <span class="comentario-time">$fecha</span>
                                    <div class="comentarios-icon">
                                        <i class="fa fa-comments"></i> $num_respuestas
                                    </div>
                                </div>
                                <div id="comentario-$modalComId" class="modal_publi">
                                    <div class="modal_publi-content">
                                        <span class="close_publi">&times;</span>
                                        <div class="tweet-header">
                                            <a href="../Vista/unsetPerfilPublico.php?nick_user=$usuario" class="nick-link"><strong>$usuario</strong></a>
                                            <span class="tweet-time">$fecha</span>
                                        </div>
                                        <div class="comentario_mod">
                                            $multi_com
                                            <span>$tex</span>
                                            <br>
                                            
                    EOS;
                    if($nickuser != $usuario){
                        $contenidoPrincipal .= <<<EOS
                            <button type="button" class="botonPubli" name="responder" id="responder-$modalComId">Responder</button>
                            <div id="respuesta-$modalComId" class="modal">
                                <div class="modal-content">
                                    <span class="close">&times;</span>
                                    <form method="POST" enctype="multipart/form-data" action="../Controlador/Receta_controlador.php" class="formulario" onsubmit="NuevoComentario(event, '$nickuser','$usuario', '$id_com', '$tipo_publicacion', 'true')">
                                        <input type="hidden" name="id_publi" value="$id">
                                        <input type="hidden" name="id_comen" value="$id_com"> 
                                        <input type="hidden" name="esRespuesta" value="true">
                                        <input type="hidden" name="usuario_origen" value="$usuario"> 
                                        <textarea name="texto" placeholder="Escribe un comentario..."></textarea>
                                        <input type="file" name="archivo"> 
                                        <button type="submit" class="botonPubli" name="agregarComentario">Añadir Respuesta</button>
                                    </form>
                                </div>
                            </div>
                        EOS;
                    }
                    $contenidoPrincipal .= <<<EOS
                                                        
                    <div class="tweet-footer">
                                                
                                                                        
                    EOS;

                    if($usuario == $_SESSION['nick'] || $_SESSION['admin'] == true){
                        $contenidoPrincipal .= <<<EOS
                            <div class="dropdown">
                                <button class="dropbtn">⋮</button>
                                <div class="dropdown-content">
                                    <form method="POST" action="../Controlador/Receta_controlador.php" class="formulario" onsubmit="ComentarioEliminado('$nick')">
                                        <input type="hidden" name="id_comen" value="$id_com"> 
                                        <input type="hidden" name="multi" value="../Recursos/multimedia/$mult"> 
                                        <input type="hidden" name="id_publi" value="$id">
                                        <button type="submit" class="botonPubli" name="eliminarComentario">Eliminar comentario</button>
                                    </form> 
                                    <button type="button" class="botonPubli" name="editar_com">Editar comentario</button>
                                </div>
                            </div> 
                            <div id="editCom-$modalComId" class="modal">
                                <div class="modal-content">
                                    <span class="close">&times;</span>
                                    $multi_editar
                                    <form method="POST" enctype="multipart/form-data" action="../Controlador/Receta_controlador.php" class="formulario">
                                    <textarea name="contenido">$tex</textarea>
                                    <input type="hidden" name="archivo_origen" value="$mult"> 
                                    <input type="file" name="nuevo_archivo"> 
                                    <input type="hidden" name="id_comen" value="$id_com"> 
                                    <input type="hidden" name="id_publi" value="$id">
                                    <button type="submit" class="botonPubli" name="editarComentario">Guardar cambios</button>
                                    </form>
                                </div>
                            </div>
                                        
                        EOS;
                    }

                    $contenidoPrincipal .= <<<EOS

                                    </div>
                                    <hr>
                    EOS;
                    if (!empty($comentario['respuestas'])) {
                        $id_comentario_anterior = $id_com;
                        $usuario_anterior = $usuario;
                        $contenidoPrincipal .= '<h3>Respuestas</h3>';
                        $contenidoPrincipal .= mostrarRespuestas($comentario['respuestas'], $modalComId, $modalResId, $id, $tipo_publicacion, $id_comentario_anterior, $usuario_anterior);
                    }
                    else{
                        $contenidoPrincipal .= '<h3>No hay respuestas</h3>';
                    }
                    $contenidoPrincipal.= <<<EOS
                                </div>
                            </div>
                        </div>
                    EOS;
                    
                    
                    $modalComId++;
                }
            }
            else{
                $contenidoPrincipal .= '<h3>No hay comentarios</h3>';
            }

            
            $contenidoPrincipal .= <<<EOS
                        
                        </div>
                    
            EOS;
            $modalId++;
}

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <div class="error_div">
            <p>$error</p>
        </div>
    EOS;
    unset($_SESSION['error']);
}

if ($mensaje != "") {
    $contenidoPrincipal .= <<<EOS
        <div class="mensaje_div">
            <p>$mensaje</p>
        </div>
    EOS;
    unset($_SESSION['mensaje']);
}

require_once __DIR__ . "/plantillas/plantilla.php";
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="../Recursos/js/Verpubli.js"></script>



