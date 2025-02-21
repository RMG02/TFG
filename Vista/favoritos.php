<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

$verpublicaciones = $_SESSION['verpublicaciones'] ?? "true";

if (!isset($_SESSION['publicacionesfavoritos'])) {
    header('Location: ../Controlador/Publicacion_controlador.php?listarPublicacionesfavoritos=true&verpublicaciones='.$verpublicaciones);
}

require_once __DIR__ . "/plantillas/respuestas.php";

$modalId = 0;
$modalComId = 0;
$principal = "true";
$tipo_publicacion = "publicacion";
$error = "";
$mensaje = "";
$recetaxx = "false";




if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}


date_default_timezone_set('Europe/Madrid');



$publicaciones = json_decode($_SESSION['publicacionesfavoritos'], true);
$tituloPagina = "Página Principal";
$usuario = json_decode($_SESSION['usuariopropio'], true);
$siguiendo = is_array($usuario['siguiendo']) ? $usuario['siguiendo'] : [];


if (isset($_SESSION['publicacionesfavoritos'])) {
    unset($_SESSION['publicacionesfavoritos']);
}




$contenidoPrincipal = <<<EOS
   <h1>Bienvenido {$_SESSION['nick']}</h1>
   <div class="dropdown">
                <button class="dropbtn">⋮</button>
                <div class="dropdown-content">
                    <form method="POST" action="../Controlador/Publicacion_controlador.php">
                            <input type="hidden" name="verpublicacionesphp" value="true">
                            <input type="hidden" name="favoritosphp" value="true">
                            <button type="submit" class="boton_lista" name="favoritosphppubli">Publicaciones</button>
                    </form>
                    <form method="POST" action="../Controlador/Publicacion_controlador.php">
                            <input type="hidden" name="verpublicacionesphp" value="false">
                            <input type="hidden" name="favoritosphp" value="true">
                            <button type="submit" class="boton_lista" name="favoritosphppubli">Recetas</button>
                    </form>
                </div>         
    </div>

<input type="text" id="buscador" onkeyup="filtrarUsuarios()" placeholder="Buscar por nick...">
<div id="publicaciones">
EOS;
if($verpublicaciones == "true"){
    $contenidoPrincipal .= <<<EOS
        <p>Publicaciones en favoritos</p>
    EOS;

    foreach ($publicaciones as $publicacion) {
        $nickuser = $_SESSION['nick'];
        $nick = $publicacion['nick'];
        $email = $publicacion['email'];
        $texto = nl2br(htmlspecialchars($publicacion['contenido']));
        $id = $publicacion['_id']['$oid'];
        $Hora = date('d/m/Y H:i:s', strtotime($publicacion['created_at']));
        $multimedia = $publicacion['multimedia'] ?? '';
        $comentarios = $publicacion['comentarios'];
        $num_comentarios = count($comentarios);
        $likes = $publicacion['likes'];
        $dislikes = $publicacion['dislikes'];
        $likes_cadena = implode(",", $likes);
        $dislikes_cadena = implode(",", $dislikes);
        $numlikes = count($likes ?? []);
        $numdislikes = count($dislikes ?? []);
        

        $multi = '';
        if ($multimedia) {
            $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación'>";
            } elseif (in_array($extension, ['mp4', 'webm'])) {
                $multi = "<video controls><source src='../Recursos/multimedia/$multimedia' type='video/$extension'></video>";
            }
        }

        $contenidoPrincipal .= <<<EOS
            <div class="tweet" id="publistas">
                <div class="tweet-header">
                    <a href="../Vista/unsetPerfilPublico.php?email_user=$email" class="nick-link"><strong>$nick</strong></a> <span class="tweet-time">$Hora</span>
                </div>
                <div class="tweet-content">
                    $multi
                    <p>$texto</p>
                    <div class="comentarios-icon">
                        <i class="fas fa-comments"></i> $num_comentarios
                    </div>
                </div>
                <div class="reacciones-icon">
        EOS;

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
                                    <input type="hidden" name="principal" value="$principal">
                                    <i class="fas fa-thumbs-up"></i> $numlikes
                                </button>
                                <button type="submit" name="dardislike" class="btn-dislike">
                                    <input type="hidden" name="id_publi" value="$id">
                                    <input type="hidden" name="nick_user" value="$nickuser">
                                    <input type="hidden" name="principal" value="$principal">
                                    <i class="fas fa-thumbs-down"></i> $numdislikes
                                </button>
                            </form>

                            <form method="POST" action="../Controlador/Usuario_controlador.php">
                                <button type="submit" name="favoritos" class="btn-like">
                                    <input type="hidden" name="publi" value="$id">
                                    <input type="hidden" name="tipo" value="$principal">
                                    <input type="hidden" name="urlfav" value="$principal">
                                    <input type="hidden" name="nick_user" value="$nickuser">
                                    <i class="fas fa-star"></i>
                                </button> 
                            </form>
                        </div>
                    </div>
                <div id="$modalId" class="modal_publi">
                    <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario">
                        <input type="hidden" name="prueba_id" value="true">
                        <input type="hidden" name="idprueba" value="$id">
                        <button type="submit" class="botonPubli" name="Verpublicacion"></button>
                    </form>
                </div>
                EOS;
        }    $modalId++;
        
}else{
    $contenidoPrincipal .= <<<EOS
        <p>Recetas en favoritos</p>
    EOS;

    foreach ($publicaciones as $receta) {

                $nickuser = $_SESSION['nick'];
                $email = $receta['email'];
                $nick = $receta['nick'];
                $texto = $receta['titulo'];
                $id = $receta['_id']['$oid'];
                $Hora = date('d/m/Y H:i:s', strtotime($receta['created_at']));
                $multimedia = $receta['multimedia'] ?? '';
                $comentarios = $receta['comentarios'];
                $num_comentarios = count($comentarios);
                $likes = $receta['likes'];
                $dislikes = $receta['dislikes'];
                $likes_cadena = implode(",", $likes);
                $dislikes_cadena = implode(",", $dislikes);
                $numlikes = count($likes ?? []);
                $numdislikes = count($dislikes ?? []);
    
                if ($multimedia) {
                    $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
                    if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                        $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación'>";
                    } 
                } else {
                    $multi = '';
                }
    
                $contenidoPrincipal .= <<<EOS
                    <div class="tweet" id="publistas">
                        <div class="tweet-header">
                            <a href="../Vista/unsetPerfilPublico.php?email_user=$email" class="nick-link"><strong>$nick</strong></a> <span class="tweet-time">$Hora</span>
                        </div>
                        <div class="tweet-content">
                            <strong>$texto</strong>
                            $multi
                            <div class="comentarios-icon">
                                <i class="fas fa-comments"></i> $num_comentarios
                            </div>
                        </div>
                        <div class="reacciones-icon">
                                
                EOS;
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
                                        <input type="hidden" name="principal" value="$principal">
                                        <i class="fas fa-thumbs-up"></i> $numlikes
                                    </button>
                                    <button type="submit" name="dardislike" class="btn-dislike">
                                        <input type="hidden" name="id_publi" value="$id">
                                        <input type="hidden" name="nick_user" value="$nickuser">
                                        <input type="hidden" name="principal" value="$principal">
                                        <i class="fas fa-thumbs-down"></i> $numdislikes
                                    </button>
                                </form>
                                <form method="POST" action="../Controlador/Usuario_controlador.php">
                                    <button type="submit" name="favoritos" class="btn-like">
                                        <input type="hidden" name="publi" value="$id">
                                        <input type="hidden" name="tipo" value="$recetaxx">
                                        <input type="hidden" name="urlfav" value="$principal">
                                        <input type="hidden" name="nick_user" value="$nickuser">
                                        <i class="fas fa-star"></i>
                                    </button> 
                                </form>
                        </div>
                    </div>
                    <div id="$modalId" class="modal_publi">
                        <form method="POST" action="../Controlador/Receta_controlador.php" class="formulario">
                            <input type="hidden" name="pruebareceta_id" value="true">
                            <input type="hidden" name="idpruebareceta" value="$id">
                            <button type="submit" class="botonPubli" name="Verreceta"></button>
                        </form>
                    </div>
                EOS;
                $modalId++;
    }
    
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
