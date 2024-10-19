
<?php

$tituloPagina = "Página de Login";

$contenidoPrincipal = <<<EOS
	<form method="POST" class="formulario" action="../Controlador/Usuario_controlador.php">
    <input type="email" name="email" placeholder="email" required>
    <input type="password" name="password" placeholder="contraseña" required>
    <button type="submit" name="login">Iniciar Sesión</button>
    </form>

EOS;

require_once __DIR__."/plantillas/plantilla.php";

