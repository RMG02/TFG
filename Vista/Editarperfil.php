
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$tituloPagina = "Página de Edicion";

$contenidoPrincipal = <<<EOS
    <div class="contenedor">
        <form method="POST" class="formulario" action="../Controlador/Usuario_controlador.php">
                
                <label for="nombre"> Nombre </label>
                <input id='nombre' type='text' name='nombre'  placeholder='{$_SESSION['nombre']}'> 
                
                <label for="nick"> Nick </label>
                <input id="nick" type="text" name="nick" placeholder='{$_SESSION['nick']}' /> 

                
                <label for="email"> Email </label>
                <input id="email" type="text" name="email" placeholder='{$_SESSION['email']}' /> 
                
                <label for="password"> Contraseña </label>
                <input id="password" type="password" name="password" placeholder="Contraseña" />
                
                <button type="button" class="botonInit">Editar</button>
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>¿Estás seguro de realizar los cambios?</h2>
                        <button type="submit" name="editar">Confirmar</button>
                        <button type="button" class="cancelAction">Cancelar</button>
                    </div>
                </div>
        </form>
    </div>
EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}


require_once __DIR__."/plantillas/plantilla.php";
?>

<script src="../Recursos/js/aviso.js"></script>
