<?php

$tituloPagina = "Añadir usuario";

$contenidoPrincipal = <<<EOS
	<form method="POST" class="formulario" action="../Controlador/Admin_controlador.php">
    
    <label for="nombre">Nombre de usuario:</label>
    <input type="text" name="nombre" required>

    <label for="nick">Nick:</label>
    <input type="text" name="nick" required>

    <label for="email">Email:</label>
    <input type="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" name="password" required>

    <label for="rol">Rol:</label>
    <select name="rol" required>
        <option value="usuario">Usuario</option>
        <option value="admin">Admin</option>
    </select>
    
    <button type="submit" name="añadirUsuario">Añadir Usuario</button>
    </form>

EOS;

require_once __DIR__."/plantillas/plantilla.php";


