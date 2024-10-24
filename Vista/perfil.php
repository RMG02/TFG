
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
    <button type="button" class="botonInit">Eliminar cuenta</button>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Introduce tu contraseña</h2>
            <form method="POST" action="../Controlador/Usuario_controlador.php">
                <input type="hidden" name="email" value={$_SESSION['email']}>
                <input type="password" name="password" required placeholder="Contraseña"><br><br>
                <button type="submit" class="boton_lista" name="cerrarCuenta">Confirmar</button>
            </form>
         </div>
    </div>
EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}

require_once __DIR__."/plantillas/plantilla.php";
?>

<script src="../Recursos/js/cerrar_cuenta.js"></script>
