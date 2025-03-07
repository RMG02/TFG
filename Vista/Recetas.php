<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Si hay una página guardada en la sesión, usarla
$paginaActual = isset($_SESSION['paginarecetas']) ? (int)$_SESSION['paginarecetas'] : $paginaActual;

// Guardar la página actual en la sesión
$_SESSION['paginarecetas'] = $paginaActual;

$verseguidores = $_SESSION['verseguidoresreceta'] ?? "false";

if (!isset($_SESSION['idsrecetas'])) {
    header('Location: ../Controlador/Usuario_controlador.php?arrayrecetas=true&paginarecetas=' . $paginaActual);
    exit;
}

if (!isset($_SESSION['recetas'])) {
    header('Location: ../Controlador/Receta_controlador.php?listarRecetas=true&paginarecetas=' . $paginaActual . '&verseguidores=' . $verseguidores);
    exit;
}

require_once __DIR__ . "/plantillas/respuestas.php";

$modalId = 0;
$modalComId = 0;
$error = "";
$tipo_publicacion = "receta";
$principal = true;
$recetaxx = false;
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
$usuario = json_decode($_SESSION['usuariopropio'], true);
$siguiendo = $usuario['siguiendo'];

// Configurar paginación
$recetasPorPagina = 2; // Número de recetas por página
$totalRecetas = count($recetas);
$totalPaginas = ceil($totalRecetas / $recetasPorPagina);

$paginaActual = max($paginaActual, 1);
$paginaActual = min($paginaActual, $totalPaginas);
$offset = ($paginaActual - 1) * $recetasPorPagina;
$recetasPagina = array_slice($recetas, $offset, $recetasPorPagina);

$contenidoPrincipal = <<<EOS
   <h1>Bienvenido {$_SESSION['nick']}</h1>
   <h2>Página de recetas</h2>
   <div class="dropdown">
        <button class="dropbtn">⋮</button>
        <div class="dropdown-content">
            <form method="POST" action="../Controlador/Receta_controlador.php">
                <input type="hidden" name="verseguidoresreceta" value="false">
                <input type="hidden" name="seguidoresreceta" value="true">
                <button type="submit" class="boton_lista" name="publicaciones">Explorar</button>
            </form>
            <form method="POST" action="../Controlador/Receta_controlador.php">
                <input type="hidden" name="verseguidoresreceta" value="true">
                <input type="hidden" name="seguidoresreceta" value="true">
                <button type="submit" class="boton_lista" name="publicaciones">Ver contenido seguidores</button>
            </form>
        </div>         
    </div>
   <button id="publicaBtn">Publica</button> 
   <div id="formPublicacion" class="modal"> 
      <div class="modal-content">
         <span class="close">&times;</span>
         <form class="formulario" method="post" enctype="multipart/form-data" action="../Controlador/Receta_controlador.php"> 
            <textarea name="titulo" placeholder="Escribe un Titulo" required></textarea>
            <textarea name="ingredientes" placeholder="Escribe ingredientes y la cantidad" required></textarea> 
            <textarea name="preparacion" placeholder="Escribe la preparación" required></textarea>
            <input type="number" min="0" name="tiempo" placeholder="Tiempo tarda en minutos" required>
            <input type="hidden" name="recetat" value="$recetat">
            <p>Dificultad de la receta</p><select name="dificultad" required>
            <option value=1>1</option>
            <option value=2>2</option>
            <option value=3>3</option>
            <option value=4>4</option>
            <option value=5>5</option>  
            </select>
            <p>Tipo</p><select name="tiporeceta" required>
            <option value="Entrante">Entrante</option>
            <option value="Primer Plato">Primer Plato</option>
            <option value="Segundo Plato">Segundo Plato</option>
            <option value="Postre">Postre</option> 
            </select>
            <input type="file" name="archivo" required> 
            <button type="submit" name="crearReceta">Publicar</button> 
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
            <button class="seccion" onclick="mostrarSeccion('filtrar')">Filtrar</button>
        </div>

        <div id="buscar" class="contenido-seccion activo">
            <p>Buscar por:</p>
            <button onclick="setBuscarPor('nick')" id="btnBuscarNick"><i class="fas fa-search"></i> Nick</button>
            <button onclick="setBuscarPor('texto')" id="btnBuscarTexto" class="activo"><i class="fas fa-search"></i> Texto</button>
        </div>

        <div id="ordenar" class="contenido-seccion">
            <p>Ordenar por:</p>
            <button onclick="mostrarOrdenFechas()" id="btnFiltrarTipo"><i class="fas fa-calendar-alt"></i> Fecha</button>
        </div>
    </div>
</div>

<div id="publicaciones">
EOS;

if ($verseguidores == "true") {
    $contenidoPrincipal .= <<<EOS
        <p>Recetas personas que sigues</p>
    EOS;
} else {
    $contenidoPrincipal .= <<<EOS
        <p>Explorar</p>
    EOS;
}

foreach ($recetasPagina as $receta) {
    if (($verseguidores == "false") || ($verseguidores == "true" && in_array($receta['nick'], $siguiendo, false)) || $receta['email'] == $_SESSION['email']) {
            $nickuser = $_SESSION['nick'];
            $email = $receta['email'];
            $tipo = $receta['tipo'];
            $nick = $receta['nick'];
            $texto = $receta['titulo'];
            $id = $receta['_id']['$oid'];
            $Hora = date('d/m/Y H:i:s', strtotime($receta['created_at']));
            $multimedia = $receta['multimedia'] ?? '';
            $comentarios = $receta['comentarios'];
            $num_comentarios = count($comentarios);
            $likes = $receta['likes'];
            $dislikes = $receta['dislikes'];
            $tiempoReceta = $receta['tiempo'];
            $dificultad = (int)$receta['dificultad'];
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
            <div class="contenedor-publicacion">
                <div class="tweet" id="publistas">
                    <div style="display: none;">
                        <span class="tiempo_receta">$tiempoReceta</span>
                        <span class="dificultad_receta">$dificultad</span>
                    </div>
                    <div class="tweet-header">
                        <a href="../Vista/unsetPerfilPublico.php?email_user=$email" class="nick-link"><strong>$nick</strong></a><span class="tweet-tipo">$tipo</span><span class="tweet-time">$Hora</span>
                    </div>
                    <div class="tweet-content">
                        <p><strong>$texto</strong></p>
                        $multi
                        <div class="comentarios-icon">
                            <i class="fas fa-comments"></i> $num_comentarios
                        </div>
                    </div>
                    <div class="reacciones-icon">
    EOS;
            if ($nickuser == $nick) {
                $contenidoPrincipal .= <<<EOS
                    <form method="POST" action="../Controlador/Receta_controlador.php">
                EOS;
            } else {
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
        </div>
    EOS;

        $modalId++;
    }
}

// Paginación: mostrar enlaces para ir a otras páginas
$contenidoPrincipal .= "<div class='paginacion'>";
if ($paginaActual > 1) {
    $contenidoPrincipal .= "<a href='Recetas.php?pagina=" . ($paginaActual - 1) . "'>&laquo; Anterior</a>";
}

if ($paginaActual < $totalPaginas) {
    $contenidoPrincipal .= "<a href='Recetas.php?pagina=" . ($paginaActual + 1) . "'>Siguiente &raquo;</a>";
}

$contenidoPrincipal .= "</div>";

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
<script src="../Recursos/js/filtro_receta.js"></script>
<script src="../Recursos/js/Principal.js"></script>