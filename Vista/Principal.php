<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
require_once '../Controlador/Publicacion_controlador.php';

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

$publicaciones = $publicacionModelo->ListaPublicacion();
$tituloPagina = "Página Principal";

$contenidoPrincipal = <<<EOS
   <h1>Bienvenido {$_SESSION['nick']}</h1>
   <button id="publicaBtn">Publica</button> 
   <div id="opcionesPublicacion" class="modal"> 
      <div class="modal-content">
         <span class="close">&times;</span>
         <button id="recetaBtn">Receta</button> 
         <button id="publicacionBtn">Publicación</button> 
      </div>
   </div> 

   <div id="formPublicacion" class="modal"> 
      <div class="modal-content">
         <span class="close">&times;</span>
         <form class="formulario" method="post" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php"> 
            <textarea name="contenido" placeholder="Escribe tu publicación aquí..."></textarea> 
            <input type="file" name="image"> 
            <button type="submit" name="crearPublicacion">Publicar</button> 
         </form> 
      </div>
   </div>

<input type="text" id="buscador" onkeyup="filtrarUsuarios()" placeholder="Buscar por email...">
<div id="publicaciones">
EOS;

foreach ($publicaciones as $index => $publicacion) {
   $email = $publicacion['user_email'];
   $texto = $publicacion['contenido'];
   $Hora = $publicacion['created_at'];
   $contenidoPrincipal .= <<<EOS
   <div class="tweet" id="publistas">
       <div class="tweet-header">
           <strong>$email</strong> <span class="tweet-time">$Hora</span>
       </div>
       <div class="tweet-content">
           <p>$texto</p>
       </div>
   </div>
   EOS;
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
require_once __DIR__."/plantillas/plantilla.php";
?>

<script src="../Recursos/js/formularios_publicacion.js"></script>
<script src="../Recursos/js/filtro_publicacion.js"></script>
