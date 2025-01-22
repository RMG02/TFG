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
   
}



require_once __DIR__ . "/plantillas/respuestas.php";

$modalId = 0;
$modalComId = 0;
$principal = true;
$tipo_publicacion = "publicacion";
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
    $email = $publicacion['email'];
    $texto = $publicacion['contenido'];
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
                <a href="../Vista/PerfilPublico.php?email_user=$email"><strong>$nick</strong></a> <span class="tweet-time">$Hora</span>
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
            </div>
        </div>
        <div id="$modalId" class="modal_publi">
            <form method="POST" action="../Vista/Verpublicacion.php?id=$id" class="formulario">
                <button type="submit" class="botonPubli" name="Verpublicacion"></button>
            </form>
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

