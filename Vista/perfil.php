
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['publicacionesUsuario'])) {
    header('Location: ../Controlador/Publicacion_controlador.php?PubliUsuario=true');
    exit;
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

date_default_timezone_set('Europe/Madrid');

$publicaciones = json_decode($_SESSION['publicacionesUsuario'], true);
$tituloPagina = "Página de Perfil";
$modalId = 1;

$contenidoPrincipal = <<<EOS
    <h3>Datos usuario:</h3>
    <p>Nick: {$_SESSION['nick']}</p>
    <p>Nombre: {$_SESSION['nombre']} </p> 
    <p>Email: {$_SESSION['email']} </p> 
    <p><a href='/Vista/Editarperfil.php'>Editar perfil</a></p>
    <button type="button" class="botonInit">Eliminar cuenta</button>
    <div id=$modalId class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Introduce tu contraseña</h2>
            <form method="POST" action="../Controlador/Usuario_controlador.php">
                <input type="hidden" name="email" value={$_SESSION['email']}>
                <input type="password" name="password" required placeholder="Contraseña"><br><br>
                <button type="submit" class="boton_lista" name="cerrarCuenta">Confirmar</button>
            </form>
         </div>
    </div>
    <hr>
    <h3>Mis publicaciones</h3>
    <input type="text" id="buscador" onkeyup="filtrarPerfil()" placeholder="Buscar por texto...">
    <div id="publicaciones">
EOS;

foreach ($publicaciones as $publicacion) {
    $nick = $publicacion['nick'];
    $texto = $publicacion['contenido'];
    $id = $publicacion['_id']['$oid'];
    $Hora = date('d/m/Y H:i:s', strtotime($publicacion['created_at']));
    $comentarios = $publicacion['comentarios'];
    $num_comentarios = count($comentarios);
    $modalId++;
    $multimedia = $publicacion['multimedia'] ?? '';

    if ($multimedia) {
        $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación'>";
        } elseif (in_array($ext, ['mp4', 'webm'])) {
            $multi = "<video controls><source src='../Recursos/multimedia/$multimedia' type='video/$extension'></video>";
        }
    }
    else{
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
        </div>
        <div id=$modalId class="modal_publi"> 
            <div class="modal_publi-content"> 
                <span class="close_publi">&times;</span> 
                <div class="tweet-header"> 
                    <strong>$nick</strong> 
                    <span class="tweet-time">$Hora</span> 
                </div> 
                <div class="tweet-content">
                    $multi 
                    <p>$texto</p> 
                    <hr>
    EOS;

    if (!empty($comentarios)) {
        foreach ($comentarios as $comentario) {
            $usuario = $comentario['usuario'];
            $tex = $comentario['texto'];
            $fecha = date('d/m/Y H:i:s', strtotime($comentario['fecha']));
            $contenidoPrincipal .= <<<EOS
                        <div class="comentario">
                            <strong>$usuario:</strong>
                            <span>$tex</span>
                            <span class="comentario-time">$fecha</span>
                        </div>
            EOS;
        }
    }

    $contenidoPrincipal .= <<<EOS
                    <button type="button" class="botonPubli" name="comen">Añadir Comentario</button>
                    <div id="comen-$modalId" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario">
                                <input type="hidden" name="id_publi" value="$id">
                                <textarea name="texto" placeholder="Escribe un comentario..."></textarea>
                                <button type="submit" class="botonPubli" name="agregarComentario">Añadir Comentario</button>
                            </form>
                        </div>
                    </div>
                    <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario"> 
                        <input type="hidden" name="id_publi" value="$id"> 
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
                                <input type="hidden" name="id_publi" value="$id"> 
                                <button type="submit" class="botonPubli" name="editarPublicacion">Guardar cambios</button> 
                            </form>
                        </div>
                    </div>
                </div> 
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

<script src="../Recursos/js/Principal.js"></script>
<script src="../Recursos/js/filtro_perfil.js"></script>
