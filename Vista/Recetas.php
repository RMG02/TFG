<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}


if (!isset($_SESSION['recetas'])) {
   header('Location: ../Controlador/Receta_controlador.php?listarRecetas=true');
   exit;
}



require_once __DIR__ . "/plantillas/respuestas.php";

$modalId = 0;
$modalComId = 0;
$error = "";
$recetat = true;
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


$recetas = json_decode($_SESSION['recetas'], true);
$tituloPagina = "Página recetas";



$contenidoPrincipal = <<<EOS
   <h1>Bienvenido {$_SESSION['nick']}</h1>
   <h2>Página de recetas</h2>
   <button id="publicaBtn">Publica</button> 
   <div id="formPublicacion" class="modal"> 
      <div class="modal-content">
         <span class="close">&times;</span>
         <form class="formulario" method="post" enctype="multipart/form-data" action="../Controlador/Receta_controlador.php"> 
            <textarea name="titulo" placeholder="Escribe un Titulo" required></textarea>
            <textarea name="ingredientes" placeholder="Escribe ingredientes y la cantidad" required></textarea> 
            <textarea name="preparacion" placeholder="Escribe la preparación" required></textarea>
            <input type="hidden" name="recetat" value="$recetat">
            <input type="file" name="archivo"> 
            <button type="submit" name="crearReceta">Publicar</button> 
         </form> 
      </div>
   </div>

<input type="text" id="buscador" onkeyup="filtrarUsuarios()" placeholder="Buscar por nick...">
<div id="publicaciones">
EOS;

foreach ($recetas as $receta) {
    $nickuser = $_SESSION['nick'];
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
                <strong>$texto</strong>
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
            <form method="POST" action="../Controlador/Receta_controlador.php" onsubmit="enviarDatos(event, '$nickuser','$nick', '$id', '$likes_cadena', '$dislikes_cadena')">
        EOS;
    }
    $contenidoPrincipal .= <<<EOS
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
            <form method="POST" action="../Vista/Verreceta.php?id=$id" class="formulario">
                <button type="submit" class="botonPubli" name="Verreceta"></button>
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

