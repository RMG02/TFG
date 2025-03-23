<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}


$tituloPagina = "Lista de Foros";

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
    <h1>Foros</h1>
    <div class="crear-foro">
        <a href="crear_foro.php" class="btn-crear">Crear Foro</a>
    </div>
EOS;

if (empty($foros)) {
    $contenidoPrincipal .= "<p>No hay foros disponibles</p>";
} else {
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
        $contenidoPrincipal .= <<<EOS
            <div class="foro_div">
                <a href="foro.php?foroId=$id" class="foro-link">
                    <h3>$titulo</h3>
                </a>
            </div>
        EOS;
    }
}

require_once __DIR__ . "/plantillas/plantilla.php";

?>
