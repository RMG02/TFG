<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

$verseguidores = $_SESSION['verseguidores'] ?? "false";

if (!isset($_SESSION['idspublis'])) {
    header('Location: ../Controlador/Usuario_controlador.php?arraypublis=true');
}

if (!isset($_SESSION['publicaciones'])) {
    header('Location: ../Controlador/Publicacion_controlador.php?listarPublicaciones=true&verseguidores='.$verseguidores);
}



require_once __DIR__ . "/plantillas/respuestas.php";

$modalId = 0;
$modalComId = 0;
$principal = true;
$recetaxx = false;
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
$usuario = json_decode($_SESSION['usuariopropio'], true);
$siguiendo = is_array($usuario['siguiendo']) ? $usuario['siguiendo'] : [];


if (isset($_SESSION['publicaciones'])) {
    unset($_SESSION['publicaciones']);
}



$contenidoPrincipal = <<<EOS
   <h1>Bienvenido {$_SESSION['nick']}</h1>
   <div class="dropdown">
                <button class="dropbtn">⋮</button>
                <div class="dropdown-content">
                    <form method="POST" action="../Controlador/Publicacion_controlador.php">
                            <input type="hidden" name="verseguidores" value="false">
                            <input type="hidden" name="seguidores" value="true">
                            <button type="submit" class="boton_lista" name="publicaciones">Explorar</button>
                    </form>
                    <form method="POST" action="../Controlador/Publicacion_controlador.php">
                            <input type="hidden" name="verseguidores" value="true">
                            <input type="hidden" name="seguidores" value="true">
                            <button type="submit" class="boton_lista" name="publicaciones">Ver contenido seguidores</button>
                    </form>
                </div>         
    </div>
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


<div class="buscador-contenedor">
    <input type="text" id="buscador" onkeyup="filtrarPublicaciones()" placeholder="Buscar...">
    
    <button id="filtroBtn"><i class="fas fa-filter"></i>Filtros</button>

    <div id="menuFiltro" class="menu-filtro">
        <div class="secciones">
            <button class="seccion activo" onclick="mostrarSeccion('buscar')">Buscar</button>
            <button class="seccion" onclick="mostrarSeccion('ordenar')">Ordenar</button>
        </div>

        <div id="buscar" class="contenido-seccion activo">
            <p>Buscar por:</p>
            <button onclick="setBuscarPor('nick')" id="btnBuscarNick"><i class="fas fa-search"></i> Nick</button>
            <button onclick="setBuscarPor('texto')" id="btnBuscarTexto" class="activo"><i class="fas fa-search"></i> Texto</button>
        </div>

        <div id="ordenar" class="contenido-seccion">
            <p>Ordenar por:</p>
            <button onclick="ordenarPublicaciones('btnOrdenarFechaDesc')" id="btnOrdenarFechaDesc" class="activo"><i class="fas fa-calendar-alt"></i> Más recientes</button>
            <button onclick="ordenarPublicaciones('btnOrdenarFechaAsc')" id="btnOrdenarFechaAsc"><i class="fas fa-calendar-alt"></i> Más antiguas</button>
            <button onclick="ordenarPublicaciones('btnOrdenarLikesDesc')" id="btnOrdenarLikesDesc"><i class="fas fa-thumbs-up"></i> Más likes</button>
            <button onclick="ordenarPublicaciones('btnOrdenarLikesAsc')" id="btnOrdenarLikesAsc"><i class="fas fa-thumbs-up"></i> Menos likes</button>
            <button onclick="ordenarPublicaciones('btnOrdenarDislikesDesc')" id="btnOrdenarDislikesDesc"><i class="fas fa-thumbs-down"></i> Más dislikes</button>
            <button onclick="ordenarPublicaciones('btnOrdenarDislikesAsc')" id="btnOrdenarDislikesAsc"><i class="fas fa-thumbs-down"></i> Menos dislikes</button>
        </div>
    </div>
</div>


<div id="publicaciones">
EOS;
if($verseguidores == "true"){
    $contenidoPrincipal .= <<<EOS
        <p>Publicaciones personas que sigues</p>
    EOS;
}else{
    $contenidoPrincipal .= <<<EOS
        <p>Explorar</p>
    EOS;
}

foreach ($publicaciones as $publicacion) {
   
    if (($verseguidores == "false") || ($verseguidores === "true" && in_array($publicacion['nick'],$siguiendo, true)) ||  $publicacion['email'] == $_SESSION['email'] ) {
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
        <div class="contenedor-publicacion">
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
                    <input type="hidden" name="tipo" value="true">
                    <input type="hidden" name="urlfav" value="false">
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
