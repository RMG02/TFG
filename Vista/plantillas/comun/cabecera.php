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
            if (isset($_SESSION['admin']) && $_SESSION['admin']) {
                echo "<p><a href='/Vista/panel_admin.php' class='menu-icon' titulo='Panel Admin'><i class='fas fa-cogs'></i></a></p>";
            }
            echo "<p><a href='/Vista/Recetas.php' class='menu-icon' titulo='Recetas'><i class='fas fa-utensils'></i></a></p>";
            echo "<p><a href='/Vista/chats.php' class='menu-icon' titulo='chats'><i class='fas fa-envelope'></i></a></p>";
            echo "<span id='chats-counter' class='badge' style='display:none;'></span></a></p>";
            echo "<p><a href='/Vista/Notificaciones.php' class='menu-icon' titulo='Notificaciones'><i class='fas fa-bell'></i></a></p>";
            echo "<span id='notification-counter' class='badge' style='display:none;'></span></a></p>";
            echo "<p><a href='/Vista/perfil.php' class='menu-icon' titulo='Perfil'><i class='fas fa-user'></i></a></p>";
            echo "<p><a href='../Controlador/logout.php' class='menu-icon' titulo='Logout' onclick='LogOut()'><i class='fas fa-sign-out-alt'></i></a></p>";
            
        } else {
            echo "<p><a href='Login.php'>Iniciar sesión</a></p>";
            echo "<p><a href='Registro.php'>Registrarse</a></p>";
        }
        ?>
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var notificationCounter = document.getElementById('notification-counter');
    var contadorActual = parseInt(localStorage.getItem('notificationCounter')) || 0;
    if(contadorActual < 0){
        notificationCounter.textContent = 0;
    }
    else{
        notificationCounter.textContent = contadorActual;
    }
    
    notificationCounter.style.display = contadorActual > 0 ? 'inline' : 'none';
    
});
</script>
<script src="../Recursos/js/socket.js"></script>


