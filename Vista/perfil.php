
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
 //$verreceta = isset($_POST['verreceta']) ? filter_var($_POST['verreceta'], FILTER_VALIDATE_BOOLEAN) : false;
 $error = "";
 $mensaje = "";
 $recetaxx = false;

 if (isset($_SESSION['error'])) {
     $error = $_SESSION['error'];
     unset($_SESSION['error']);
 }
 
 if (isset($_SESSION['mensaje'])) {
     $mensaje = $_SESSION['mensaje'];
     unset($_SESSION['mensaje']);
 }

 if(isset($_SESSION['verRecetasPerfil'])){
    unset($_SESSION['verRecetasPerfil']);
    $verreceta = true;
 }
 else if(isset($_POST['verreceta'])){
    if($_POST['verreceta'] == "false"){
        $verreceta = false;
    }
    else{
        $verreceta = true;
    }
 }
 else{
    $verreceta = false;
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


$usuario = json_decode($_SESSION['usuariopropio'], true);
$siguiendo = $usuario['siguiendo'];
$seguidores = $usuario['seguidores'];




$numseguidores = is_array($seguidores) ? count($seguidores) : 0;
$numsiguiendo = is_array($siguiendo) ? count($siguiendo) : 0;

$contenidoPrincipal = <<<EOS
    <h3>Datos usuario:</h3>
    <p>Nick: {$_SESSION['nick']}</p>
    <p>Nombre: {$_SESSION['nombre']} </p> 
    <p>Email: {$_SESSION['email']} </p> 
    <p>Seguidores: {$numseguidores} </p> 
    <p>Siguiendo: {$numsiguiendo} </p>
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

    <div class="dropdown">
            <button class="dropbtn">⋮</button>
            <div class="dropdown-content">
                <form method="POST" action="../Vista/perfil.php">
                    <input type="hidden" name="verreceta" value='false'>
                    <button type="submit" class="boton_lista" name="publicaciones">Ver publicaciones</button>
                </form>
                <form method="POST" action="../Vista/perfil.php">
                    <input type="hidden" name="verreceta" value='true'>
                    <button type="submit" class="boton_lista" name="publicaciones">Ver recetas</button>
                </form>
            </div>         
    </div>
    
    

    <h3>Mis publicaciones</h3>
        <input type="text" id="buscador" onkeyup="filtrarPerfil()" placeholder="Buscar por texto...">
        <div id="publicaciones">
    
EOS;

if($verreceta){
    foreach ($recetas as $receta) {
        $nickuser = $_SESSION['nick'];
        $nick = $receta['nick'];
        $tipo = $receta['tipo'];
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
                <strong>$nick</strong><span class="tweet-time">$tipo</span><span class="tweet-time">$Hora</span>
            </div>
            <div class="tweet-content">
                    $multi
                    <p>$titulo</p>
                    <div class="comentarios-icon">
                        <i class="fas fa-comments"></i> $num_comentarios
                    </div>
            </div>
        EOS;
                
                $contenidoPrincipal .= <<<EOS
                    <form method="POST" action="../Controlador/Receta_controlador.php">
                        <button type="submit" name="darlike" class="btn-like">
                            <input type="hidden" name="id_publi" value="$id">
                            <input type="hidden" name="nick_user" value="$nick">
                            <input type="hidden" name="principal" value="$principal">
                            <input type="hidden" name="verreceta" value='true'>
                            <i class="fas fa-thumbs-up"></i> $numlikes
                        </button>
                        <button type="submit" name="dardislike" class="btn-dislike">
                            <input type="hidden" name="id_publi" value="$id">
                            <input type="hidden" name="nick_user" value="$nick">
                            <input type="hidden" name="principal" value="$principal">
                            <input type="hidden" name="verreceta" value='true'>
                            <i class="fas fa-thumbs-down"></i> $numdislikes
                        </button>
                    </form>

                    <form method="POST" action="../Controlador/Usuario_controlador.php">
                        <button type="submit" name="favoritos" class="btn-like">
                            <input type="hidden" name="publi" value="$id">
                            <input type="hidden" name="tipo" value="$recetaxx">
                            <input type="hidden" name="perfil" value="true">
                            <input type="hidden" name="verreceta" value='true'>
                            <input type="hidden" name="nick_perfil" value="$nick">
                            <input type="hidden" name="nick_user" value="$nickuser">
                EOS;
                $favoritos = isset($_SESSION['idsrecetas']) && is_array($_SESSION['idsrecetas']) 
                ? $_SESSION['idsrecetas'] 
                : [];
                
                if (in_array($id, $favoritos)) {
                    $contenidoPrincipal .= '<i class="fas fa-star"></i>';
                } else {
                    $contenidoPrincipal .= '<i class="far fa-star"></i>';
                }

                $contenidoPrincipal .= <<<EOS
                    </button> 
                </form>
                </div>
                </div>
                <div id="$modalId" class="modal_publi">
                    <form method="POST" action="../Controlador/Receta_controlador.php" class="formulario">
                        <input type="hidden" name="pruebareceta_id" value="true">
                        <input type="hidden" name="idpruebareceta" value="$id">
                        <button type="submit" class="botonPubli" name="Verpublicacion"></button>
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
        EOS;
                
                $contenidoPrincipal .= <<<EOS
                <form method="POST" action="../Controlador/Publicacion_controlador.php">
                    <button type="submit" name="darlike" class="btn-like">
                        <input type="hidden" name="id_publi" value="$id">
                        <input type="hidden" name="nick_user" value="$nickuser">
                         <input type="hidden" name="nick_perfil" value="$nick">
                        <input type="hidden" name="principal" value="$principal">
                        <i class="fas fa-thumbs-up"></i> $numlikes
                    </button>
                    <button type="submit" name="dardislike" class="btn-dislike">
                        <input type="hidden" name="id_publi" value="$id">
                        <input type="hidden" name="nick_user" value="$nickuser">
                         <input type="hidden" name="nick_perfil" value="$nick">
                        <input type="hidden" name="principal" value="$principal">
                        <i class="fas fa-thumbs-down"></i> $numdislikes
                    </button>
                </form>

                <form method="POST" action="../Controlador/Usuario_controlador.php">
                    <button type="submit" name="favoritos" class="btn-like">
                        <input type="hidden" name="publi" value="$id">
                        <input type="hidden" name="tipo" value="true">
                        <input type="hidden" name="nick_perfil" value="$nick">
                        <input type="hidden" name="perfil" value="true">
                        <input type="hidden" name="nick_user" value="$nickuser">
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

