<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}

$mensaje = "";

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

$notiseguidores = $_SESSION['notiseguidores'];
$notilikes = $_SESSION['notilikes'];
$noticomentarios = $_SESSION['noticomentarios'];
$tituloPagina = "Panel preferencias";

$contenidoPrincipal = "<h1>Panel de preferencias</h1>";

$contenidoPrincipal .= '<div class="preference-container">
        <label class="notification-text" for="switch-followers">Notificaciones de seguidores</label>
        <div class="switch-button">
            <input type="checkbox" name="switch-followers" id="switch-followers" class="switch-button__checkbox" onchange="updatePreference(\'notiseguidores\', this.checked)" ' . ($notiseguidores ? 'checked' : '') . '>
            <label for="switch-followers" class="switch-button__label"></label>
        </div>
    </div>';

$contenidoPrincipal .= '<br><div class="preference-container">
        <label class="notification-text" for="switch-likes">Notificaciones de likes</label>
        <div class="switch-button">
            <input type="checkbox" name="switch-likes" id="switch-likes" class="switch-button__checkbox" onchange="updatePreference(\'notilikes\', this.checked)" ' . ($notilikes ? 'checked' : '') . '>
            <label for="switch-likes" class="switch-button__label"></label>
        </div>
    </div>';

$contenidoPrincipal .= '<br><div class="preference-container">
        <label class="notification-text" for="switch-comments">Notificaciones de comentarios</label>
        <div class="switch-button">
            <input type="checkbox" name="switch-comments" id="switch-comments" class="switch-button__checkbox" onchange="updatePreference(\'noticomentarios\', this.checked)" ' . ($noticomentarios ? 'checked' : '') . '>
            <label for="switch-comments" class="switch-button__label"></label>
        </div>
    </div>';




if ($mensaje != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="mensaje">$mensaje</p>
    EOS;
}

require_once __DIR__."/plantillas/plantilla.php";
?>

<script>
function updatePreference(type, isChecked) {
    fetch('../Controlador/Usuario_controlador.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `type=${type}&status=${isChecked ? 1 : 0}&ischanged=1`
    })
    .then(response => response.text())
    .catch(error => console.error('Error:', error));
}
</script>
