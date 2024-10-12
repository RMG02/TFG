
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$tituloPagina = "Página de Edicion";

$contenidoPrincipal = <<<EOS
    <div class="contenedor">
        <form method="POST" action="../Controlador/Usuario_controlador.php">
                <div>
                <p> <label for="nombre"> Nombre </label>
                    <input id='nombre' type='text' name='nombre'  placeholder='{$_SESSION['nombre']}'> </p>
                </div>
                <div>
                <p>
                    <label for="nick"> Nick </label>
                    <input id="nick" type="text" name="nick" placeholder='{$_SESSION['nick']}' /> </p>
                </div> 

                <div>
                <p>
                    <label for="email"> Email </label>
                    <input id="email" type="text" name="email" placeholder='{$_SESSION['email']}' /> </p>
                </div> 
                <div>
                    <button type="submit" name="editar">Editar</button>
                </div>
        </form>
    </div>
EOS;

require_once __DIR__."/plantillas/plantilla.php";