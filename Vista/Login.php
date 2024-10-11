
<?php

$tituloPagina = "Página de Login";

$contenidoPrincipal = <<<EOS
	<form method="POST" id="contenidoLogin" action="../Controlador/Usuario_controlador.php">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit" name="login">Iniciar Sesión</button>
    </form>

EOS;

require_once __DIR__."/plantillas/plantilla.php";

