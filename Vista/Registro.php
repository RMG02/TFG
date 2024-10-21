


<?php

$tituloPagina = "Página de Registro";

$contenidoPrincipal = <<<EOS
	<form method="POST" class="formulario" action="../Controlador/Usuario_controlador.php">
    
    <label for="nombre">Nombre de usuario:</label>
    <input type="nombre" name="nombre" required>

    <label for="nick">Nick:</label>
    <input type="nick" name="nick" required>

    <label for="email">Email:</label>
    <input type="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" name="password" required>
    
    <button type="submit" name="registro">Crear cuenta</button>
    </form>

EOS;

require_once __DIR__."/plantillas/plantilla.php";


