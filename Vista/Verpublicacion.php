<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

require_once __DIR__ . "/plantillas/respuestas.php";


$host = $_SERVER['HTTP_HOST']; 
$modalId = 0;
$modalComId = 0;
$principal = true;
$tipo_publicacion = "publicacion";
$error = "";
$mensaje = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
}


$publicacion = "";

if(!isset($_SESSION['conversaciones_abiertas']) || !isset($_SESSION['forosSuscrito'])){
    header('Location: ../Controlador/Foros_controlador.php?ObtenerInfoCompartir=true&nick=' . $_SESSION['nick']);    
}


if($_SESSION['seguidores'] === null || $_SESSION['siguiendo'] === null){
    header('Location: ../Controlador/Usuario_controlador.php?seguidores=true');
}


$conversaciones = json_decode($_SESSION['conversaciones_abiertas'], true);
unset($_SESSION['conversaciones_abiertas']);

$foros = json_decode($_SESSION['forosSuscrito'], true);
unset($_SESSION['forosSuscrito']);


if($_SESSION['publidisponible'] == false){
    $tituloPagina = "Tweet";
    $contenidoPrincipal = <<<EOS
        <div>
            <p>Publicacion ya no disponible</p>
        </div>
    EOS;
}else{


    $publicacion = json_decode($_SESSION['id_publi'], true);
    date_default_timezone_set('Europe/Madrid');


    $tituloPagina = "Tweet";

        $nickuser = $_SESSION['nick'];
        $email = $publicacion['email'];
        $nick = $publicacion['nick'];
        $texto = $publicacion['contenido'];
        $id = $publicacion['_id']['$oid'];
        $Hora = date('d/m/Y H:i:s', strtotime($publicacion['created_at']));
        $multimedia = $publicacion['multimedia'];
        $comentarios = $publicacion['comentarios'];
        $num_comentarios = count($comentarios);
        $likes = $publicacion['likes'];
        $dislikes = $publicacion['dislikes'];
        $numlikes = count($likes);
        $numdislikes = count($dislikes);
        $likes_cadena = implode(",", $likes);
        $dislikes_cadena = implode(",", $dislikes);


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
    <div class="tweet-publicacion">
        <div class="tweet-header">
            <a href="../Vista/unsetPerfilPublico.php?email_user=$email" class="nick-link"><strong>$nick</strong></a>
            <span class="tweet-time">$Hora</span>
        </div>
        <div class="tweet-content">
            $multi
            <p>$texto</p>
        
    EOS;
    if($nickuser != $nick){
        
        $contenidoPrincipal .= <<<EOS
            <button type="button" class="botonPubli" name="comen">Añadir Comentario</button>
            <div id="comen-$modalId" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario" onsubmit="NuevoComentario(event, '$nickuser','$nick', '$id', '$tipo_publicacion', '')">
                        <input type="hidden" name="id_publi" value="$id">
                        <textarea name="texto" placeholder="Escribe un comentario..."></textarea>
                        <input type="hidden" name="usuario_origen" value="$nick"> 
                        <input type="file" name="archivo"> 
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
            <form method="POST" action="../Controlador/Publicacion_controlador.php">
        EOS;
    }
    else{
        $contenidoPrincipal .= <<<EOS
            <form method="POST" action="../Controlador/Publicacion_controlador.php" onsubmit="enviarDatos(event, '$nickuser','$nick', '$id', '$likes_cadena', '$dislikes_cadena', '$tipo_publicacion', '', '')">
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

                <form method="POST" action="../Controlador/Usuario_controlador.php">
                <button type="submit" name="favoritos" class="btn-like">
                    <input type="hidden" name="publi" value="$id">
                    <input type="hidden" name="tipo" value="true">
                    <input type="hidden" name="verpublicacion" value="true">
                    <input type="hidden" name="nick_user" value="$nickuser">
                </form>
            EOS;

            $favoritos = isset($_SESSION['idspublis']) && is_array($_SESSION['idspublis']) 
            ? $_SESSION['idspublis'] 
            : [];
            
            if (in_array($id, $favoritos)) {
                $contenidoPrincipal .= '<i class="fas fa-star"></i>';
            } else {
                $contenidoPrincipal .= '<i class="far fa-star"></i>';
            }
            
            $contenidoPrincipal .= <<<EOS
              </div>
                <div class="dropdown">
                    <button class="dropbtn">⋮</button>
                    <div class="dropdown-content">
            EOS;

    if ($nick == $_SESSION['nick'] || $_SESSION['admin'] == true) {
        $contenidoPrincipal .= <<<EOS
                    <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario" onsubmit="unset()">
                        <input type="hidden" name="id_publi" value="$id">
                        <input type="hidden" name="multi" value="../Recursos/multimedia/$multimedia"> 
                        <button type="submit" class="botonPubli" name="eliminarPublicacion">Eliminar publicación</button>
                    </form>
                    <button type="button" class="botonPubli" name="editar">Editar publicación</button>
        EOS;
    }

    $contenidoPrincipal .= <<<EOS
                    <button type="button" class="botonPubli" name="compartir" onclick="modal_compartir('$id')">Compartir publicación</button>   
                    <div id="compartir-$id" class="modal-compartir">
                        <div class="modal-compartir-content">
                            <span class="close_compartir" onclick="cerrar_modal_compartir('compartir-$id')">&times;</span>
                            <h2>Compartir publicación con</h2>

                            <div class="tabs-compartir">
                                <button id="btn-usuarios-$id" class="tab-button activo" onclick="mostrarUsuarios('$id')"><strong>Usuarios</strong></button>
                                <button id="btn-foros-$id" class="tab-button" onclick="mostrarForos('$id')"><strong>Foros</strong></button>
                            </div>

                            <input type="text" id="buscador-compartir" onkeyup="filtrarUsuariosCompartir('$id')" placeholder="Buscar por nick...">

                            <div id="seccion-usuarios-$id" class="seccion-compartir">
                            

    EOS;                    
                            if(empty($_SESSION['siguiendo']) && empty($conversaciones)){
                                $contenidoPrincipal .= <<<EOS
                                    <p style="color: white;"> No sigues a ningún usuario</p>
                                EOS;
                            }
                            else{
                                $contenidoPrincipal .= <<<EOS
                                    <ul>
                                       
                                EOS;
                                foreach ($conversaciones as $conv) {
                                    $usuarios = $conv['usuarios'];
                                    foreach ($usuarios as $usu){
                                        if($usu != $_SESSION['nick'] && !in_array($usu, $_SESSION['siguiendo']) ){
                                            $usuarios_a_mostrar[] = $usu;
                                        }
                                    }
                                }
                                foreach ($_SESSION['siguiendo'] as $usuario_seguido) {
                                    $usuarios_a_mostrar[] = $usuario_seguido;
                                }
                                foreach ($usuarios_a_mostrar as $usuario) {
                                    $contenidoPrincipal .= <<<EOS
                                        <li>
                                            <form method="POST" action="../Controlador/Conversaciones_controlador.php">
                                                <input type="hidden" name="usuario1" value="{$_SESSION['nick']}">
                                                <input type="hidden" name="usuario2" value="$usuario">
                                                <input type="hidden" name="compartir" value="publicacion">
                                                <input type="hidden" name="id_comp" value=$id>
                                                <button type="submit" class="boton_lista" name="abrirConversacion">$usuario</button>
                                            </form>
                                        </li>
                                        
                                    EOS;
                                }
                                $contenidoPrincipal .= <<<EOS
                                    </ul>
                                EOS;
                            }

                            $contenidoPrincipal .= <<<EOS
                                </div>
                                <div id="seccion-foros-$id" class="seccion-compartir" style="display: none;">

                            EOS;

                            if(empty($foros)){
                                $contenidoPrincipal .= <<<EOS
                                    <p style="color: white;"> No estás suscrito a ningún foro</p>
                                EOS;
                            }
                            else{
                                $contenidoPrincipal .= <<<EOS
                                    <ul>
                                       
                                EOS;
                                $publi_foro = "Publicación compartida: <a href=\"http://$host/Controlador/Publicacion_controlador.php?publi_id=true&id=$id\">Ver publicación</a>";
                                $suscrito = true;
                                foreach ($foros as $foro) {
                                    $notificaciones = json_encode($foro['notificaciones']); 
                                    $contenidoPrincipal .= <<<EOS
                                        <li>
                                            <form method="POST" action="../Controlador/Foros_controlador.php">
                                                <input type="hidden" name="contenido" value='$publi_foro'></textarea>
                                                <input type="hidden" name="id_foro" value="{$foro['_id']['$oid']}">
                                                <input type="hidden" name="suscrito" value="$suscrito">
                                                <button type="submit" class="boton_lista" name="CrearMensaje" onclick='enviarPublicacionForo("{$foro['_id']['$oid']}", $notificaciones, "{$_SESSION['nick']}", "{$foro['titulo']}")'>{$foro['titulo']}</button>
                                            </form>
                                        </li>
                                    EOS;
                                }
                            
                                $contenidoPrincipal .= <<<EOS
                                    </ul>
                                EOS;
                            }
    $contenidoPrincipal .= <<<EOS
                            </div>
                        </div>
                    </div>     
                </div>
            </div>
            <div id="edit-$modalId" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario">
                        $multi_editar
                        <textarea name="contenido">$texto</textarea>
                        <input type="hidden" name="archivo_origen" value="$multimedia"> 
                        <input type="file" name="nuevo_archivo"> 
                        <input type="hidden" name="id_publi" value="$id">
                        <button type="submit" class="botonPubli" name="editarPublicacion">Guardar cambios</button>
                    </form>
                </div>
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
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
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
                                        <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario" onsubmit="NuevoComentario(event, '$nickuser','$usuario', '$id_com', '$tipo_publicacion', 'true')">
                                            <input type="hidden" name="id_publi" value="$id">
                                            <input type="hidden" name="id_comen" value="$id_com"> 
                                            <input type="hidden" name="usuario_origen" value="$usuario"> 
                                            <input type="hidden" name="esRespuesta" value="true">
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
                                <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario" onsubmit="ComentarioEliminado('$nick')">
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
                                <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario">
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

<script src="../Recursos/js/Verpubli.js"></script>

