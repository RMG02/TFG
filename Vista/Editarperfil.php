
<?php
$tituloPagina = "Página de Edicion";

$contenidoPrincipal = <<<EOS
	<fieldset>
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
    </fieldset>

EOS;