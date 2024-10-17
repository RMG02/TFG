
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
            <button type="submit" name="cerrarCuenta">Cerrar Cuenta</button>
        </div>
    </form>


EOS;

require_once __DIR__."/plantillas/plantilla.php";
