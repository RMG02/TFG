<?php

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

// Redirigir si no estás logueado
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}


$tituloPagina = "Tus Conversaciones";
$userNick = $_SESSION['nick'];

if(!$_SESSION['conversaciones']){
    header('Location: ../Controlador/Conversaciones_controlador.php?listarConversaciones=true&nick_Usur=' . $userNick);
}


$conversaciones = json_decode($_SESSION['conversaciones'], true);
unset($_SESSION['conversaciones']);


$contenidoPrincipal = <<<EOS
   <h1>Tus Conversaciones</h1>
   <div id="lista-conversaciones">
EOS;

if(empty($conversaciones)){
    $contenidoPrincipal = <<<EOS
        <h2>No tienes conversaciones</h2>
    EOS;
}
foreach ($conversaciones as $conv) {
    $conversacionId = $conv['_id']['$oid'];
    $usuarios = $conv['usuarios'];
    foreach ($usuarios as $usu){
        if($usu != $userNick){
            $otroUsuario = $usu;
        }
    }
    
    $contenidoPrincipal .= <<<EOS
       <div class="conversacion">
            <form method="POST" action="../Controlador/Conversaciones_controlador.php" onsubmit="unset()">
                <input type="hidden" name="id_conver" value="$conversacionId">
                <input type="hidden" name="otroUsuario" value="$otroUsuario">
                <button type="submit" class="eliminar-conver" name="eliminarConversacion" title="Eliminar conversación"><i class="fas fa-trash-alt"></i></button>
            </form>
            <a href="/Vista/chat.php?conversacionId=$conversacionId" class="enlace-conversacion">
                <p class="nombre-conversacion">$otroUsuario</p>
            </a>
       </div>
       
EOS;
}

$contenidoPrincipal .= <<<EOS
   </div>
EOS;

require_once __DIR__ . "/plantillas/plantilla.php";
?>
