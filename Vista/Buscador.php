<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}




$modalId = 0;
$modalComId = 0;
$principal = true;
$recetaxx = false;
$error = "";
$mensaje = "";




if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}


date_default_timezone_set('Europe/Madrid');

if (isset($_SESSION['usuarioslista'])) {
    $usuarios = json_decode($_SESSION['usuarioslista'], true);
    $ver = True;
}else{
    $ver = False;
}

$tituloPagina = "Buscador usuarios";

if (isset($_SESSION['usuarioslista'])) {
    unset($_SESSION['usuarioslista']);
}




$contenidoPrincipal = <<<EOS
   <h1>Bienvenido {$_SESSION['nick']}</h1>
    <form method="POST" action="../Controlador/Usuario_controlador.php">
           <input type="text" name="filtro" placeholder="Buscar usuario por nick">
            <input type="hidden" name="buscarusuario" value="true">
            <button type="submit" class="boton_lista" name="buscador">Buscar</button>
    </form>

EOS;
if(!$ver){
    $contenidoPrincipal .= <<<EOS
        <p>Busca el usuario que quieras encontrar</p>
    EOS;
}else{
    
        $contenidoPrincipal .= <<<EOS
        <p>Usuarios encontrados</p>
        EOS;
    
        foreach ($usuarios as $usuario) {
            $nick = $usuario['nick'];
            $nombre = $usuario['nombre'];
            $email = $usuario['email'];
            $seguidores = $usuario['seguidores'];
            $siguiendo = $usuario['siguiendo'];

            $numseguidores = is_array($seguidores) ? count($seguidores) : 0;
            $numsiguiendo = is_array($siguiendo) ? count($siguiendo) : 0;
            
            if($nick == $_SESSION['nick']){
                continue;
            }else{

            
                $contenidoPrincipal .= <<<EOS
                    <div class="contenedor-buscadorrr">
                        <div class="tweetbuscador" id="publistas">
                            <div class="tweetbuscador-header">
                                <a href="../Vista/unsetPerfilPublico.php?email_user=$email" class="nick-link">
                                    <strong>$nick</strong>
                                </a>
                                
                            </div>
                            <div class="tweetbuscador-content">   
                                    <p>Nombre: $nombre</p><br>
                                    <p>Seguidores: $numseguidores</p><br>
                                    <p>Siguiendo: $numsiguiendo</p><br>
                            </div> 
                            <div id="$modalId" class="modal_publi">
                                <form method="POST" action="../Controlador/Usuario_controlador.php" class="formulario">
                                    <input type="hidden" name="email" value="$email">
                                    <input type="hidden" name="verusuariooo" value="true">
                                    <button type="submit" class="botonPubli" name="VerUsuario"></button>
                                </form>
                            </div>
                        </div>
                    </div>
                EOS;
            
            $modalId++;
            
            }
        }
    
    
}





if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
EOS;
}

if ($mensaje != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="mensaje">$mensaje</p>
EOS;
}



require_once __DIR__ . "/plantillas/plantilla.php";
?>

<script src="../Recursos/js/formularios_publicacion.js"></script>
<script src="../Recursos/js/filtro_publicacion.js"></script>
<script src="../Recursos/js/Principal.js"></script>
