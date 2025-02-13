<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$emailUsuario = $_GET['email_user'] ?? null;
$nickUsuario = $_GET['nick_user'] ?? null;

if(isset($_SESSION['publicacionesUsuarioPublico'])){
    unset($_SESSION['publicacionesUsuarioPublico']);
}

if(isset($_SESSION['RecetasUsuarioPublico'])){
    unset($_SESSION['RecetasUsuarioPublico']);
}

if($emailUsuario){
    header('Location: ../Controlador/Usuario_controlador.php?publicoemail=true&email_Usur='.$emailUsuario);
    exit;
}
else{
    header('Location: ../Controlador/Usuario_controlador.php?publiconick=true&nick_Usur='.$nickUsuario);
    exit;
}

