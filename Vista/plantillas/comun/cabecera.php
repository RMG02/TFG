<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


?>
<header>

    <?php
    if (isset($_SESSION['login'])) {
        if (!isset($_SESSION['notificaciones_usuario'])) {
            header('Location: ../Controlador/Notificacion_controlador.php?listarNotificacionesUsuario=true');
        }

        if(!empty($_SESSION['notificaciones_usuario'])){
            $notificaciones = json_decode($_SESSION['notificaciones_usuario'], true);
        }
        else{
            $notificaciones = [];
        }    
        $num_noti = count($notificaciones);

    }
    else{
        $num_noti = null;
    }
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
            if (isset($_SESSION['admin']) && $_SESSION['admin']) {
                echo "<p><a href='/Vista/panel_admin.php' class='menu-icon' titulo='Panel Admin'><i class='fas fa-cogs'></i></a></p>";
            }
            echo "<p><a href='/Vista/Notificaciones.php' class='menu-icon' titulo='Notificaciones'><i class='fas fa-bell'></i></a></p>";
            if($num_noti > 0){
                echo "<span id='notification-counter' class='badge' style='display:inline;'>$num_noti</span></a></p>";
            }
            else{
                echo "<span id='notification-counter' class='badge' style='display:none;'></span></a></p>";
            }
            echo "<p><a href='/Vista/perfil.php' class='menu-icon' titulo='Perfil'><i class='fas fa-user'></i></a></p>";
            echo "<p><a href='../Controlador/logout.php' class='menu-icon' titulo='Logout'><i class='fas fa-sign-out-alt'></i></a></p>";
            
        } else {
            echo "<p><a href='Login.php'>Iniciar sesión</a></p>";
            echo "<p><a href='Registro.php'>Registrarse</a></p>";
        }
        ?>
    </div>
</header>
<script src="../../../Recursos/js/avisoNoti.js"></script>

