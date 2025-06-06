<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: enter.php");
    exit;
}
require_once __DIR__ . "/plantillas/respuestas.php";

$error = "";
$mensaje = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
}

$emailUsuario = $_SESSION['emailUserpublico'] ?? null;
$nickUsuario = $_SESSION['nickUserpublico'] ?? null;
$verreceta = $_SESSION['verreceta'] ?? false;
$recetaxx = false;

if(isset($_POST['nick_user'])){
    $nickUsuario = $_POST['nick_user'];
}

if (!isset($_SESSION['idspublis'])) {
    header('Location: ../Controlador/Usuario_controlador.php?arraypublisperfilpublico=true&user='.$nickUsuario.'&verreceta='.$verreceta);

}


if($emailUsuario){
    if (!isset($_SESSION['publicacionesUsuarioPublico'])) {
        header('Location: ../Controlador/Publicacion_controlador.php?PubliUsuarioPublico=true&email=' . $emailUsuario);
        exit;
    }

    if (!isset($_SESSION['RecetasUsuarioPublico'])) {
        header('Location: ../Controlador/Receta_controlador.php?ReceUsuarioPublico=true&email=' . $emailUsuario);
        exit;
    }

    if( $emailUsuario == $_SESSION['email']){
        header('Location: ../Vista/perfil.php');
        exit;
    }
    
    if (!isset($_SESSION['emailUser'])) {
        if($verreceta == "true"){
            header('Location: ../Controlador/Usuario_controlador.php?publicoemail=true&verreceta=true&email_Usur='.$emailUsuario);
        }
        else{
            header('Location: ../Controlador/Usuario_controlador.php?publicoemail=true&email_Usur='.$emailUsuario);
        }
        exit;
    }
}
else if($nickUsuario){
    if (!isset($_SESSION['publicacionesUsuarioPublico'])) {
        header('Location: ../Controlador/Publicacion_controlador.php?PubliUsuarioPublico=true&nick=' . $nickUsuario);
        exit;
     }

    if (!isset($_SESSION['RecetasUsuarioPublico'])) {
        header('Location: ../Controlador/Receta_controlador.php?ReceUsuarioPublico=true&nick=' . $nickUsuario);
        exit;
    }

    if( $nickUsuario == $_SESSION['nick']){
        header('Location: ../Vista/perfil.php');
        exit;
    }

    if (!isset($_SESSION['nickUser'])) {
        if($verreceta){
            header('Location: ../Controlador/Usuario_controlador.php?publiconick=true&verreceta=true&nick_Usur='.$nickUsuario);
        }
        else{
            header('Location: ../Controlador/Usuario_controlador.php?publiconick=true&nick_Usur='.$nickUsuario);
        }
        exit;
    }
}





if($_SESSION['usudisponible'] == false){
    $tituloPagina = "Perfil";
    $contenidoPrincipal = <<<EOS
        <div>
            <p>Usuario ya no disponible</p>
        </div>
    EOS;
}else{



        $tituloPagina = "Perfil";
        $modalId = 0;
        $modalComId = 0;

        if(isset($_SESSION['emailUser'])){
            $usuario = json_decode($_SESSION['emailUser'], true);
        }
        else if(isset($_SESSION['nickUser'])){
            $usuario = json_decode($_SESSION['nickUser'], true);
        }



        $nick = $usuario['nick'];
        $email = $usuario['email'];
        $nombre = $usuario['nombre'];
        $seguidores = $usuario['seguidores'];
        $siguiendo = $usuario['siguiendo'];

        $numseguidores = is_array($seguidores) ? count($seguidores) : 0;
        $numsiguiendo = is_array($siguiendo) ? count($siguiendo) : 0;


        if(isset($_SESSION['emailUser'])){
            unset($_SESSION['emailUser']);
        }
        else if(isset($_SESSION['nickUser'])){
            unset($_SESSION['nickUser']);
        }



        
        // Comprobar si el usuario de la sesión está en el array de seguidores
        $nickSesion = $_SESSION['nick'];
        $esSeguidor = is_array($seguidores) && in_array($nickSesion, $seguidores);

        // Determinar el texto del botón
        $textoBoton = $esSeguidor ? "Dejar de Seguir" : "Seguir";
        $textoVista = $esSeguidor ? '<i class="fas fa-user-minus"></i>'  : '<i class="fas fa-user-plus"></i>';

        // Cambiar la acción del formulario dependiendo del estado
        $accionFormulario = $esSeguidor ? "DejarSeguir" : "Seguir";
        $contenidoPrincipal = <<<EOS

            <div class="vista-perfil">

                <h2>{$nick}</h2>
                <div class="info-perfil">
                    <h3>Seguidores: </h3> <p>{$numseguidores}</p>
                </div>

                <div class="info-perfil">
                    <h3>Siguiendo: </h3> <p>{$numsiguiendo}</p>
                </div>
                
                <div class="botones-vista-perfil">
                    <form method="POST" action="../Controlador/Usuario_controlador.php" onsubmit="enviarSeguidor('$nickSesion', '$nick', '$textoBoton')">
                            <input type="hidden" name="nickPropio" value="{$nickSesion}">
                            <input type="hidden" name="nickSeguir" value="{$nick}">
                            <button type="submit" class="boton_lista" name="Seguir">{$textoVista}</button>
                    </form>

                    <form method="POST" action="../Controlador/Conversaciones_controlador.php">
                        <input type="hidden" name="usuario1" value="{$nickSesion}">
                        <input type="hidden" name="usuario2" value="{$nick}">
                        <button type="submit" class="boton_lista" name="abrirConversacion"><i class='fas fa-envelope'></i></button>
                    </form>
                </div>
            </div> 

            <hr>


        EOS;


        if($verreceta == "true"){
            $recetas = json_decode($_SESSION['RecetasUsuarioPublico'], true);
        }else{
            $publicaciones = json_decode($_SESSION['publicacionesUsuarioPublico'], true);

        }


        $contenidoPrincipal .= <<<EOS

            <div class="dropdown">
                    <button class="dropbtn">⋮</button>
                    <div class="dropdown-content">
                        <form method="POST" action="../Vista/unsetPerfilPublico.php?email_user=$email&verreceta=false">
                            <button type="submit" class="boton_lista" name="publicaciones">Ver publicaciones</button>
                        </form>
                        <form method="POST" action="../Vista/unsetPerfilPublico.php?email_user=$email&verreceta=true">
                            <button type="submit" class="boton_lista" name="publicaciones">Ver recetas</button>
                        </form>
                    </div>         
            </div>
            
            
        EOS;
        if($verreceta == "true"){
            $contenidoPrincipal .= <<<EOS
                <h3>Recetas</h3>
            EOS;
        }
        else{
            $contenidoPrincipal .= <<<EOS
                <h3>Publicaciones</h3>
            EOS;
        }
        $contenidoPrincipal .= <<<EOS
                <input type="text" id="buscador" onkeyup="filtrarPerfil()" placeholder="Buscar por texto...">
                <div id="publicaciones">
        EOS;


        if($verreceta == "true"){
            foreach ($recetas as $receta) {
                $nickuser = $_SESSION['nick'];
                $nick = $receta['nick'];
                $email = $receta['email'];
                $texto = $receta['titulo'];
                $id = $receta['_id']['$oid'];
                $tipo = $receta['tipo'];
                $Hora = date('d/m/Y H:i:s', strtotime($receta['created_at']));
                $multimedia = $receta['multimedia'] ?? '';
                $comentarios = $receta['comentarios'];
                $num_comentarios = count($comentarios);
                $likes = $receta['likes'];
                $dislikes = $receta['dislikes'];
                $likes_cadena = implode(",", $likes);
                $dislikes_cadena = implode(",", $dislikes);
                $numlikes = count($likes ?? []);
                $numdislikes = count($dislikes ?? []);
                $tipo_publicacion = "receta";

            
                if ($multimedia) {
                    $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación'>";
                    } elseif (in_array($extension, ['mp4', 'webm'])) {
                        $multi = "<video controls><source src='../Recursos/multimedia/$multimedia' type='video/$extension'></video>";
                    }
                }
                else{
                    $multi = '';
                }
            
                $contenidoPrincipal .= <<<EOS
                <div class="contenedor-publicacion">
                    <div class="tweetrecetas" id="publistas">
                        <div class="tweetrecetas-header">
                            <a href="../Vista/unsetPerfilPublico.php?email_user=$email" class="nick-link"><strong>$nick</strong></a><span class="tweet-tipo">$tipo</span><span class="tweetrecetas-time">$Hora</span>
                        </div>
                        <div class="tweetrecetas-content">
                            <p><strong>$texto</strong></p>
                            $multi
                            <div class="comentarios-icon-recetas">
                                <i class="fas fa-comments"></i> $num_comentarios
                            </div>
                        </div>
                        <div class="reacciones-icon-recetas">
                                
                EOS;
                if($nickuser == $nick){
                    $contenidoPrincipal .= <<<EOS
                        <form method="POST" action="../Controlador/Receta_controlador.php">
                    EOS;
                }
                else{
                    $contenidoPrincipal .= <<<EOS
                        <form method="POST" action="../Controlador/Receta_controlador.php" onsubmit="enviarDatos(event, '$nickuser','$nick', '$id', '$likes_cadena', '$dislikes_cadena', '$tipo_publicacion', '', '')">
                    EOS;
                }
                $contenidoPrincipal .= <<<EOS
                    <button type="submit" name="darlike" class="btn-like">
                        <input type="hidden" name="id_publi" value="$id">
                        <input type="hidden" name="nick_user" value="$nickuser">
                        <input type="hidden" name="nick_perfil" value="$nick">
                        <input type="hidden" name="perfilPublico" value=true>
                        <i class="fas fa-thumbs-up"></i> $numlikes
                    </button>
                    <button type="submit" name="dardislike" class="btn-dislike">
                        <input type="hidden" name="id_publi" value="$id">
                        <input type="hidden" name="nick_user" value="$nickuser">
                        <input type="hidden" name="nick_perfil" value="$nick">
                        <input type="hidden" name="perfilPublico" value=true>
                        <i class="fas fa-thumbs-down"></i> $numdislikes
                    </button>
                </form>

                <form method="POST" action="../Controlador/Usuario_controlador.php">
                    <button type="submit" name="favoritos" class="btn-like">
                        <input type="hidden" name="publi" value="$id">
                        <input type="hidden" name="tipo" value="$recetaxx">
                        <input type="hidden" name="perfilPublico" value=true>
                        <input type="hidden" name="nick_perfil" value="$nick">
                        <input type="hidden" name="nick_user" value="$nickuser">
                EOS;

                $favoritos = isset($_SESSION['idsrecetas']) && is_array($_SESSION['idsrecetas']) 
                ? $_SESSION['idsrecetas'] 
                : [];
                
                if (in_array($id, $favoritos)) {
                    $contenidoPrincipal .= '<i class="fas fa-star"></i>';
                } else {
                    $contenidoPrincipal .= '<i class="far fa-star"></i>';
                }

                $contenidoPrincipal .= <<<EOS
                    </button> 
                </form>
                </div>
                </div>
                <div id="$modalId" class="modal_publi">
                    <form method="POST" action="../Controlador/Receta_controlador.php" class="formulario">
                        <input type="hidden" name="pruebareceta_id" value="true">
                        <input type="hidden" name="idpruebareceta" value="$id">
                        <button type="submit" class="botonPubli" name="Verpublicacion"></button>
                    </form>
                </div>
                </div>
                EOS;

            $modalId++;
            }
        }else{
            foreach ($publicaciones as $publicacion) {
                $nickuser = $_SESSION['nick'];
                $nick = $publicacion['nick'];
                $texto = $publicacion['contenido'];
                $id = $publicacion['_id']['$oid'];
                $Hora = date('d/m/Y H:i:s', strtotime($publicacion['created_at']));
                $multimedia = $publicacion['multimedia'] ?? '';
                $comentarios = $publicacion['comentarios'];
                $num_comentarios = count($comentarios);
                $likes = $publicacion['likes'];
                $dislikes = $publicacion['dislikes'];
                $likes_cadena = implode(",", $likes);
                $dislikes_cadena = implode(",", $dislikes);
                $numlikes = count($likes ?? []);
                $numdislikes = count($dislikes ?? []);
                $tipo_publicacion = "publicacion";

            
                if ($multimedia) {
                    $extension = pathinfo($multimedia, PATHINFO_EXTENSION);
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $multi = "<img src='../Recursos/multimedia/$multimedia' alt='Imagen de la publicación'>";
                    } elseif (in_array($extension, ['mp4', 'webm'])) {
                        $multi = "<video controls><source src='../Recursos/multimedia/$multimedia' type='video/$extension'></video>";
                    }
                }
                else{
                    $multi = '';
                }

                $contenidoPrincipal .= <<<EOS
                <div class="contenedor-publicacion">
                    <div class="tweet" id="publistas">
                        <div class="tweet-header">
                            <a href="../Vista/unsetPerfilPublico.php?email_user=$email" class="nick-link"><strong>$nick</strong></a> <span class="tweet-time">$Hora</span>
                        </div>
                        <div class="tweet-content">
                            $multi
                            <p>$texto</p>
                            <div class="comentarios-icon">
                                <i class="fas fa-comments"></i> $num_comentarios
                            </div>
                        </div>
                        <div class="reacciones-icon">
                EOS;
                    if($nickuser == $nick){
                        $contenidoPrincipal .= <<<EOS
                            <form method="POST" action="../Controlador/Publicacion_controlador.php">
                        EOS;
                    }
                    else{
                        $contenidoPrincipal .= <<<EOS
                            <form method="POST" action="../Controlador/Publicacion_controlador.php" onsubmit="enviarDatos(event, '$nickuser','$nick', '$id', '$likes_cadena', '$dislikes_cadena', '$tipo_publicacion', '', '')">
                        EOS;
                    }
                
                $contenidoPrincipal .= <<<EOS
                    <button type="submit" name="darlike" class="btn-like">
                        <input type="hidden" name="id_publi" value="$id">
                        <input type="hidden" name="nick_user" value="$nickuser">
                         <input type="hidden" name="nick_perfil" value="$nick">
                        <input type="hidden" name="perfilPublico" value=true>
                        <i class="fas fa-thumbs-up"></i> $numlikes
                    </button>
                    <button type="submit" name="dardislike" class="btn-dislike">
                        <input type="hidden" name="id_publi" value="$id">
                        <input type="hidden" name="nick_user" value="$nickuser">
                         <input type="hidden" name="nick_perfil" value="$nick">
                        <input type="hidden" name="perfilPublico" value=true>
                        <i class="fas fa-thumbs-down"></i> $numdislikes
                    </button>
                </form>

                <form method="POST" action="../Controlador/Usuario_controlador.php">
                    <button type="submit" name="favoritos" class="btn-like">
                        <input type="hidden" name="publi" value="$id">
                        <input type="hidden" name="tipo" value="true">
                         <input type="hidden" name="nick_perfil" value="$nick">
                        <input type="hidden" name="perfilPublico" value=true>
                        <input type="hidden" name="nick_user" value="$nickuser">
                EOS;

            $favoritos = isset($_SESSION['idspublis']) && is_array($_SESSION['idspublis']) 
            ? $_SESSION['idspublis'] 
            : [];
            
            if (in_array($id, $favoritos)) {
                $contenidoPrincipal .= '<i class="fas fa-star"></i>';
            } else {
                $contenidoPrincipal .= '<i class="far fa-star"></i>';
            }

            $contenidoPrincipal .= <<<EOS
                </button> 
            </form>
            </div>
            </div>
            <div id="$modalId" class="modal_publi">
                <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario">
                    <input type="hidden" name="prueba_id" value="true">
                    <input type="hidden" name="idprueba" value="$id">
                    <button type="submit" class="botonPubli" name="Verpublicacion"></button>
                </form>
            </div>
            </div>
            EOS;

            $modalId++;
            }
        }
}

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
<script src="../Recursos/js/Principal.js"></script>
<script src="../Recursos/js/filtro_perfil.js"></script>
<script src="../Recursos/js/formularios_publicacion.js"></script>
