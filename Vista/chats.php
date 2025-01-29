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

foreach ($conversaciones as $conv) {
    $conversacionId = $conv['_id']['$oid'];
    $usuarios = $conv['usuarios'];
    foreach ($usuarios as $usu){
        if($usu != $userNick){
            $otroUsuario = $usu;
        }
    }
    $no_leidos = 0; 
    
    $contenidoPrincipal .= <<<EOS
       <div class="conversacion">
           <a href="/Vista/chat.php?conversacionId=$conversacionId" class="enlace-conversacion">
               <p class="nombre-conversacion">$otroUsuario</p>
               <span class="no-leidos">$no_leidos</span>
           </a>
       </div>
EOS;
}

$contenidoPrincipal .= <<<EOS
   </div>
EOS;

require_once __DIR__ . "/plantillas/plantilla.php";
?>
