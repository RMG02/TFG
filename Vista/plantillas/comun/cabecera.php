<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<header>
    <h1>X</h1>

    <?php
    if(isset($_SESSION["login"]) && $_SESSION["login"]) {
        echo "<p><a href='/Vista/perfil.php'>Bienvenido {$_SESSION['nick']}</a> </p>";
        echo "<p><a href='../Controlador/logout.php'>  Logout</a></p>";
        if (isset($_SESSION['admin']) && $_SESSION['admin']) {
            echo "<p><a href='/Vista/panel_admin.php'>Panel Admin</a></p>";
         }
    }
    else {
        echo "<p> Usuario Desconocido.  <a href='Login.php'>Login</a> </p>";
        echo "<p> <a href='Registro.php'>Registrate</a> </p>";
    }
    ?>
</header>
