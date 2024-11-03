<?php

$tituloPagina = "Añadir usuario";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

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
    
    <button type="button" class="botonInit">Añadir Usuario</button>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>¿Estás seguro de añadir el usuario?</h2>
            <button type="submit" name="añadirUsuario">Confirmar</button>
            <button type="button" class="cancelAction">Cancelar</button>
        </div>
    </div>
    </form>

EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}


require_once __DIR__."/plantillas/plantilla.php";
?>

<script src="../Recursos/js/aviso.js"></script>



