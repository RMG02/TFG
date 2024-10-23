
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$tituloPagina = "Página de Perfil";

$contenidoPrincipal = <<<EOS
    <h3>Datos usuario:</h3>
    <p>Nick: {$_SESSION['nick']}</p>
    <p>Nombre: {$_SESSION['nombre']} </p> 
    <p>Email: {$_SESSION['email']} </p> 
    <p><a href='/Vista/Editarperfil.php'>  Editar perfil</a></p>
    <form method="POST" id="cerrar" action="../Controlador/Usuario_controlador.php">
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <button type="submit" name="cerrarCuenta">Cerrar cuenta</button>
        </div>
    </form>


EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}

require_once __DIR__."/plantillas/plantilla.php";
