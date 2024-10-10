<?php
require_once '../../TFG/Config/config.php';
require_once '../../TFG/Modelo/Usuario.php';
?>

<header>
    <h1>X</h1>

    <?php
    if(isset($_SESSION["login"]) && $_SESSION["login"]) {
        // Verifica que $_SESSION["login"] esté establecido y sea true
        echo "<p> Bienvenido {$_SESSION['email']}</p>";
        echo "<p>logout</p>";
    }
    else {
        echo "<p> Usuario Desconocido.  <a href='Login.php'>Login</a> </p>";
        echo "<p> <a href='Registro.php'>Registrate</a> </p>";
    }
    ?>
</header>
