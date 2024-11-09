<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

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
EOS;

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
