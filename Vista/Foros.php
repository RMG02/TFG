<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}


$tituloPagina = "Lista de Foros";
$verseguidores = $_SESSION['verseguidoresforo'] ?? "false";
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

if (!isset($_SESSION['foros'])) {
    header('Location: ../Controlador/Foros_controlador.php?ObtenerListaForos=true');
    exit;
}

date_default_timezone_set('Europe/Madrid');

$foros = $_SESSION['foros'];
unset($_SESSION['foros']);

if($verseguidores == "false"){
    $contenidoPrincipal = <<<EOS
    <div class="crear-foro">
        <h1>Explorar foros</h1>
        <a href="crear_foro.php" class="btn-crear" title="Crear foro"><i class="fas fa-plus-circle"></i></a>
    </div>
    EOS;
}
else{
    $contenidoPrincipal = <<<EOS
    <div class="crear-foro">
        <h1>Foros que sigues</h1>
        <a href="crear_foro.php" class="btn-crear" title="Crear foro"><i class="fas fa-plus-circle"></i></a>
    </div>
    EOS;

}

if (empty($foros)) {
    $contenidoPrincipal .= <<<EOS
        <p>No hay foros disponibles</p>
        <div class="dropdown">
                        <button class="dropbtn">⋮</button>
                        <div class="dropdown-content">
                            <form method="POST" action="../Controlador/Foros_controlador.php">
                                    <input type="hidden" name="verseguidoresforo" value="false">
                                    <input type="hidden" name="seguidoresforo" value="true">
                                    <button type="submit" class="boton_lista" name="foros">Explorar</button>
                            </form>
                            <form method="POST" action="../Controlador/Foros_controlador.php">
                                <input type="hidden" name="verseguidoresforo" value="true">
                                    <input type="hidden" name="seguidoresforo" value="true">
                                    <button type="submit" class="boton_lista" name="foros">Ver foros que sigues</button>
                            </form>
                        </div>         
        </div>
    EOS;
} else {
    $contenidoPrincipal .= <<<EOS
        <div class="buscador-contenedor">
            <div class="dropdown">
                    <button class="dropbtn">⋮</button>
                    <div class="dropdown-content">
                        <form method="POST" action="../Controlador/Foros_controlador.php">
                                <input type="hidden" name="verseguidoresforo" value="false">
                                <input type="hidden" name="seguidoresforo" value="true">
                                <button type="submit" class="boton_lista" name="foros">Explorar</button>
                        </form>
                    <form method="POST" action="../Controlador/Foros_controlador.php">
                            <input type="hidden" name="verseguidoresforo" value="true">
                                <input type="hidden" name="seguidoresforo" value="true">
                                <button type="submit" class="boton_lista" name="foros">Ver foros que sigues</button>
                        </form>
                    </div>         
            </div>
            <input type="text" id="buscador" onkeyup="filtrarForos()" placeholder="Buscar por título...">
            
            <button id="filtroBtn"><i class="fas fa-filter"></i>Filtros</button>

            <div id="menuFiltro" class="menu-filtro">
                <p>Ordenar por:</p>

                <button onclick="mostrarOrdenFechas()" id="btnFiltrarTipo"><i class="fas fa-calendar-alt"></i> Fecha</button>
                <div id="opcionesOrdenFechas" class="opciones-filtro opciones_orden">
                    <button onclick="ordenarForos('btnOrdenarFechaDesc')" id="btnOrdenarFechaDesc" class="activo">Más recientes</button>
                    <button onclick="ordenarForos('btnOrdenarFechaAsc')" id="btnOrdenarFechaAsc">Más antiguas</button>
                </div>

                <button onclick="mostrarOrdenSus()" id="btnFiltrarTipo"><i class="fas fa-users"></i> Suscriptores</button>
                <div id="opcionesOrdenSus" class="opciones-filtro opciones_orden">
                    <button onclick="ordenarForos('btnOrdenarSusDesc')" id="btnOrdenarSusDesc">Más suscriptores</button>
                    <button onclick="ordenarForos('btnOrdenarSusAsc')" id="btnOrdenarSusAsc">Menos suscriptores</button>
                </div>
            </div>
        </div>
        
        <div id="foros">
    EOS;
        
    foreach ($foros as $foro) {
        $titulo = $foro['titulo'];
        $id = $foro['_id']['$oid'];
        $suscriptores = $foro['suscriptores'];
        $fecha = date('d/m/Y H:i:s', strtotime($foro['fecha']));
        $num_suscriptores = count($suscriptores);
       
        if($verseguidores == "false"){
            $contenidoPrincipal .= <<<EOS
                <div class="contenedor-foros">
                    <a href="foro.php?foroId=$id" class="foro-link">  
                        <div class="foro_div">
                            <span class="tweet-time">$fecha</span>
                            <h3>$titulo</h3>
                            <div class="comentarios-icon">
                                <i class="fas fa-users"></i> $num_suscriptores
                            </div>
                        </div>
                    </a>
                </div>
            EOS;
        }
        else {
            if(in_array($_SESSION['nick'], $foro['suscriptores'])){
                $contenidoPrincipal .= <<<EOS
                <div class="contenedor-foros">
                    <a href="foro.php?foroId=$id" class="foro-link">
                        <div class="foro_div">
                            <span class="tweet-time">$fecha</span>
                            <h3>$titulo</h3>
                            <div class="comentarios-icon">
                                <i class="fas fa-users"></i> $num_suscriptores
                            </div>
                        </div>
                    </a>
                </div>
                EOS;
            }
        }
    }
    $contenidoPrincipal .= <<<EOS
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

require_once __DIR__ . "/plantillas/plantilla.php";

?>

<script src="../Recursos/js/filtro_foros.js"></script>
