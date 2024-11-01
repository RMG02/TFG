<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<header>

    <?php

    $logo = '../../../Recursos/imagenes/Logo.png';

    if (isset($_SESSION['login'])) {
        echo "<a href='../../Vista/Principal.php'><img src='$logo' class='logo'></a>";
    } else {
        echo "<img src='$logo' class='logo'>";
    }
    ?>
    <div class="menu">
        <?php
        if (isset($_SESSION['login']) && $_SESSION['login']) {
            echo "<p><a href='/Vista/perfil.php'>Bienvenido {$_SESSION['nick']}</a></p>";
            echo "<p><a href='../Controlador/logout.php'>Logout</a></p>";
            if (isset($_SESSION['admin']) && $_SESSION['admin']) {
                echo "<p><a href='/Vista/panel_admin.php'>Panel Admin</a></p>";
            }
        } else {
            echo "<p><a href='Login.php'>Iniciar sesión</a></p>";
            echo "<p><a href='Registro.php'>Registrarse</a></p>";
        }
        ?>
    </div>
</header>
