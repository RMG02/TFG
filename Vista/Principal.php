<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}


if (!isset($_SESSION['publicaciones'])) {
   header('Location: ../Controlador/Publicacion_controlador.php?listarPublicaciones=true');
   exit;
}
$modalId = 1;
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
            <input type="file" name="image"> 
            <button type="submit" name="crearPublicacion">Publicar</button> 
         </form> 
      </div>
   </div>

<input type="text" id="buscador" onkeyup="filtrarUsuarios()" placeholder="Buscar por nick...">
<div id="publicaciones">
EOS;

foreach ($publicaciones as $index => $publicacion) {
    $nick = $publicacion['nick'];
    $texto = $publicacion['contenido'];
    $id = $publicacion['_id']['$oid'];
    $Hora = date('d/m/Y H:i:s', strtotime($publicacion['created_at']));
    $modalId++;
   $hola = "";
   if($nick == $_SESSION['nick']){
      $hola .= <<<EOS
      <div id=$modalId class="modal_publi"> 
         <div class="modal_publi-content"> 
               <span class="close_publi">&times;</span> 
               <div class="tweet-header"> 
                  <strong>$nick</strong> 
                  <span class="tweet-time">$Hora</span> 
               </div> 
               <div class="tweet-content"> 
                  <p>$texto</p> 
                  <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario"> 
                     <input type="hidden" name="id_publi" value="$id"> 
                     <button type="submit" class="botonPubli" name="eliminarPublicacionp">Eliminar publicación</button> 
                  </form>
                  <button type="button" class="botonPubli" name="editar">Editar publicación</button>
                  <div id="edit-$modalId" class="modal">
                     <div class="modal-content">
                           <span class="close">&times;</span>
                           <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario"> 
                              <textarea name="contenido">$texto</textarea> 
                              <input type="hidden" name="id_publi" value="$id"> 
                              <button type="submit" class="botonPubli" name="editarPublicacionp">Guardar cambios</button> 
                           </form>
                     </div>
                  </div>
               </div> 
         </div> 
         </div>
   
      EOS;
   
   }
   $contenidoPrincipal .= <<<EOS
      <div class="tweet" id="publistas">
       <div class="tweet-header">
           <strong>$nick</strong> <span class="tweet-time">$Hora</span>
       </div>
       <div class="tweet-content">
           <p>$texto</p>
       </div>
   </div>
   $hola
   
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
<script src="../Recursos/js/Principal.js"></script>