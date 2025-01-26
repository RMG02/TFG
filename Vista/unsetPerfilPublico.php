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
    header('Location: ../Vista/PerfilPublico.php?email_user='.$emailUsuario);
    exit;
}
else{
    header('Location: ../Vista/PerfilPublico.php?nick_user='.$nickUsuario);
    exit;
}

