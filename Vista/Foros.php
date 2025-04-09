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

$foros = $_SESSION['foros'];
unset($_SESSION['foros']);
//$filtro = $_GET['filtro'] ?? 'todos';

/*if ($filtro === 'suscritos') {
    $foros = array_filter($foros, function ($foro) use ($usuarioActual) {
        return in_array($usuarioActual, $foro['suscriptores'] ?? []);
    });
}*/

/* Ordenar los foros por número de suscriptores (de mayor a menor)
usort($foros, function ($a, $b) {
    return count($b['suscriptores']) - count($a['suscriptores']);
});*/

/*$contenidoPrincipal = <<<EOS
    <h1>Foros disponibles</h1>
    <div class="filtro-foros">
        <a href="foros.php?filtro=todos" class="btn-filtro">Todos los foros</a>
        <a href="foros.php?filtro=suscritos" class="btn-filtro">Mis foros suscritos</a>
    </div>
    <div class="crear-foro">
        <a href="crear_foro.php" class="btn-crear">Crear Foro</a>
    </div>
EOS;*/

$contenidoPrincipal = <<<EOS
    <div class="crear-foro">
        <h1>Foros</h1>
        <a href="crear_foro.php" class="btn-crear" title="Crear foro"><i class="fas fa-plus-circle"></i></a>
    </div>
EOS;

if (empty($foros)) {
    $contenidoPrincipal .= "<p>No hay foros disponibles</p>";
} else {
    $contenidoPrincipal = <<<EOS
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
        
    foreach ($foros as $foro) {
        $titulo = $foro['titulo'];
        $id = $foro['_id']['$oid'];
        //$numSuscriptores = count($foro['suscriptores']);

        /*$contenidoPrincipal .= <<<EOS
            <div class="foro_div">
                <a href="foro.php?foroId=$id" class="foro-link">
                    <h3>$titulo</h3>
                    <p>Suscriptores: $numSuscriptores</p>
                </a>
            </div>
        EOS;*/
        if($verseguidores == "false" || ($verseguidores == "true") && (in_array($_SESSION['nick'], $foro['suscriptores']))){
            if($verseguidores == "false"){
                $contenidoPrincipal .= <<<EOS
                            <h3>Explorar foros</h3>
                        EOS;
            }else{
                $contenidoPrincipal .= <<<EOS
                            <h3>Foros que sigues</h3>
                        EOS;
            }
            $contenidoPrincipal .= <<<EOS
                <a href="foro.php?foroId=$id" class="foro-link">
                    <div class="foro_div">
                        <h3>$titulo</h3>
                    </div>
                </a>
            EOS;
        }else{
            $contenidoPrincipal .= "<p>No hay foros disponibles</p>";
        }
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
