
<?php
$tituloPagina = "Página de Perfil";

$contenidoPrincipal = <<<EOS
    <h3>Datos usuario:</h3>
    <p>Nick: {$_SESSION['nick']}</p>
    <p>Nombre: {$_SESSION['nombre']} </p> 
    <p>Email: {$_SESSION['email']} </p> 
    <p><a href='/Vista/Editarperfil.php'>  Editar perfil</a></p>
    

EOS;

require_once __DIR__."/plantillas/plantilla.php";
