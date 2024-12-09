
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

if (!isset($_SESSION['publicacionesUsuario'])) {
    header('Location: ../Controlador/Publicacion_controlador.php?PubliUsuario=true');
    exit;
 }

 if (!isset($_SESSION['RecetasUsuario'])) {
    header('Location: ../Controlador/Receta_controlador.php?ReceUsuario=true');
    exit;
 }


 
 require_once __DIR__ . "/plantillas/respuestas.php";
 $principal = false;
 $verreceta = isset($_POST['verreceta']) ? filter_var($_POST['verreceta'], FILTER_VALIDATE_BOOLEAN) : false;
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

if($verreceta){
    $recetas = json_decode($_SESSION['RecetasUsuario'], true);
}else{
    $publicaciones = json_decode($_SESSION['publicacionesUsuario'], true);
}

$tituloPagina = "Página de Perfil";
$modalId = 0;
$modalComId = 0;

$contenidoPrincipal = <<<EOS
    <h3>Datos usuario:</h3>
    <p>Nick: {$_SESSION['nick']}</p>
    <p>Nombre: {$_SESSION['nombre']} </p> 
    <p>Email: {$_SESSION['email']} </p> 
    <p><a href='/Vista/Editarperfil.php'>Editar perfil</a></p>
    <button type="button" class="botonInit" id="botonInit">Eliminar cuenta</button>
    <div id="eliminar"class="modal">
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
    <form method="POST" action="../Vista/perfil.php">
                <input type="hidden" name="verreceta" value='false'>
                <button type="submit" class="boton_lista" name="publicaciones">Verpublicaciones</button>
    </form>
    <form method="POST" action="../Vista/perfil.php">
                <input type="hidden" name="verreceta" value='true'>
                <button type="submit" class="boton_lista" name="publicaciones">Ver recetas</button>
    </form>

    <h3>Mis tweets</h3>
        <input type="text" id="buscador" onkeyup="filtrarPerfil()" placeholder="Buscar por texto...">
        <div id="publicaciones">
    
EOS;

if($verreceta){
    foreach ($recetas as $receta) {
        $nickuser = $_SESSION['nick'];
        $nick = $receta['nick'];
        $titulo = $receta['titulo'];
        $id = $receta['_id']['$oid'];
        $Hora = date('d/m/Y H:i:s', strtotime($receta['created_at']));
        $multimedia = $receta['multimedia'] ?? '';
        $comentarios = $receta['comentarios'];
        $num_comentarios = count($comentarios);
        $likes = $receta['likes'];
        $dislikes = $receta['dislikes'];
        $numlikes = count($likes ?? []);
        $numdislikes = count($dislikes ?? []);
       
        if ($multimedia) {
            $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación'>";
            } elseif (in_array($extension, ['mp4', 'webm'])) {
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
                    <p>$titulo</p>
                    <div class="comentarios-icon">
                        <i class="fas fa-comments"></i> $num_comentarios
                    </div>
                </div>
                <div class="reacciones-icon">
                        <form method="POST" action="../Controlador/Receta_controlador.php">
                            
                            <button type="submit" name="darlike" class="btn-like">
                                <input type="hidden" name="id_publi" value="$id">
                                <input type="hidden" name="nick_user" value="$nick">
                                <input type="hidden" name="principal" value="$principal">
                                <i class="fas fa-thumbs-up"></i> $numlikes
                            </button>
                            <button type="submit" name="dardislike" class="btn-dislike">
                                <input type="hidden" name="id_publi" value="$id">
                                <input type="hidden" name="nick_user" value="$nick">
                                <input type="hidden" name="principal" value="$principal">
                                <i class="fas fa-thumbs-down"></i> $numdislikes
                            </button>
                        </form>
                </div>
            </div>
            <div id="$modalId" class="modal_publi"> 
                <form method="POST" action="../Vista/Verreceta.php?id=$id" class="formulario">
                    <input type="hidden" name="id" value="$id">
                    <button type="submit" class="botonPubli" name="Verpublicacion">Ver Publicación</button>
                </form>
            </div>
        EOS;
        $modalId++;
     }
}else{
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
    
        if ($multimedia) {
            $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación'>";
            } elseif (in_array($extension, ['mp4', 'webm'])) {
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
                        <i class="fas fa-comments"></i> $num_comentarios
                    </div>
                </div>
                <div class="reacciones-icon">
                        <form method="POST" action="../Controlador/Publicacion_controlador.php">
                            
                            <button type="submit" name="darlike" class="btn-like">
                                <input type="hidden" name="id_publi" value="$id">
                                <input type="hidden" name="nick_user" value="$nick">
                                <input type="hidden" name="principal" value="$principal">
                                <i class="fas fa-thumbs-up"></i> $numlikes
                            </button>
                            <button type="submit" name="dardislike" class="btn-dislike">
                                <input type="hidden" name="id_publi" value="$id">
                                <input type="hidden" name="nick_user" value="$nick">
                                <input type="hidden" name="principal" value="$principal">
                                <i class="fas fa-thumbs-down"></i> $numdislikes
                            </button>
                        </form>
                </div>
            </div>
            <div id="$modalId" class="modal_publi"> 
                <form method="POST" action="../Vista/Verpublicacion.php?id=$id" class="formulario">
                    <input type="hidden" name="id" value="$id">
                    <button type="submit" class="botonPubli" name="Verpublicacion">Ver Publicación</button>
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

require_once __DIR__."/plantillas/plantilla.php";
?>

<script src="../Recursos/js/Principal.js"></script>
<script src="../Recursos/js/filtro_perfil.js"></script>
<script src="../Recursos/js/formularios_publicacion.js"></script>

