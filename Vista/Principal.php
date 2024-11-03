<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

$tituloPagina = "Página Principal";

$contenidoPrincipal = <<<EOS
   <h1>Bienvenido {$_SESSION['nick']}</h1>
EOS;

require_once __DIR__."/plantillas/plantilla.php";


