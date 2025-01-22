<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

$error = "";
$mensaje = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
}

$emailUsuario = $_GET['email_user'] ?? null;

if (!isset($_SESSION['emailUser'])) {
    header('Location: ../Controlador/Usuario_controlador.php?Usuarion=true&email_Usur='.$emailUsuario);
    exit;
}

$tituloPagina = "Perfil";

// Decodificar los datos del usuario
$usuario = json_decode($_SESSION['emailUser'], true);

$nick = $usuario['nick'];
$email = $usuario['email'];
$nombre = $usuario['nombre'];
$seguidores = $usuario['seguidores'];
$siguiendo = $usuario['siguiendo'];
unset($_SESSION['emailUser']);

$numseguidores = is_array($seguidores) ? count($seguidores) : 0;
$numsiguiendo = is_array($siguiendo) ? count($siguiendo) : 0;
// Obtener el email del usuario en sesión
$emailSesion = $_SESSION['email'];

// Verificar si es el propio perfil
$esMiPerfil = $emailSesion === $email;

// Si no es el propio perfil, mostramos el botón de "Seguir"
$mostrarBotonSeguir = !$esMiPerfil;

if (!$esMiPerfil) {
    // Comprobar si el usuario de la sesión está en el array de seguidores
    $esSeguidor = is_array($seguidores) && in_array($emailSesion, $seguidores);

    // Determinar el texto del botón
    $textoBoton = $esSeguidor ? "Dejar de Seguir" : "Seguir";

    // Cambiar la acción del formulario dependiendo del estado
    $accionFormulario = $esSeguidor ? "DejarSeguir" : "Seguir";

    // Mostrar el formulario con el botón de seguir
    $botonSeguir = <<<EOS
    <form method="POST" action="../Controlador/Usuario_controlador.php">
        <input type="hidden" name="emailpropio" value="{$emailSesion}">
        <input type="hidden" name="emailseguir" value="{$email}">
        <button type="submit" class="boton_lista" name="Seguir">{$textoBoton}</button>
    </form>
    EOS;
} else {
    $botonSeguir = ''; // No mostramos el botón si es el propio perfil
}

$contenidoPrincipal = <<<EOS
    <div class="tweet-header">
        <strong>Nick: {$nick}</strong>
    </div>
    <div class="tweet-content">
        <p>Nombre: {$nombre}</p>
        <p>Seguidores: {$numseguidores}</p>
        <p>Siguiendo: {$numsiguiendo}</p>
        {$botonSeguir}  <!-- Muestra el botón si no es el propio perfil -->
    </div>  
EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <div class="error_div">
            <p>$error</p>
        </div>
    EOS;
    unset($_SESSION['error']);
}

if ($mensaje != "") {
    $contenidoPrincipal .= <<<EOS
        <div class="mensaje_div">
            <p>$mensaje</p>
        </div>
    EOS;
    unset($_SESSION['mensaje']);
}

require_once __DIR__ . "/plantillas/plantilla.php";
?>
