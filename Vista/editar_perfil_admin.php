
<?php


$tituloPagina = "Página de Edicion";

if(isset($_POST['modificar'])){
    $email = $_POST['email'];
    $nick = $_POST['nick'];
    $nombre = $_POST['nombre'];
    $admin = $_POST['admin'];
    $pass = $_POST['pass'];
    
}

$contenidoPrincipal = <<<EOS
    <div class="contenedor">
        <form method="POST" id="modificarUsuario" class="formulario" action="../Controlador/Admin_controlador.php">
                <div>
                <p> <label for="nombre"> Nombre </label>
                    <input id='nombre' type='text' name='nombre_nuevo'  placeholder=$nombre> </p>
                </div>
                <div>
                <p>
                    <label for="nick"> Nick </label>
                    <input id="nick" type="text" name="nick_nuevo" placeholder=$nick /> </p>
                </div> 

                <div>
                <p>
                    <label for="email"> Email </label>
                    <input id="email" type="text" name="email_nuevo" placeholder=$email /> </p>
                </div> 
                <div>
                <p>
                    <label for="password"> Contraseña </label>
                    <input id="password" type="password" name="pass_nuevo" placeholder="" /> </p>
                </div>
                <div>
                <p>
                    <label for="rol">Rol:</label>
                    <select name="admin" required>
                        <option value="usuario">Usuario</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>  
                <div> 
                    <input type="hidden" name="email" value=$email>
                    <input type="hidden" name="nombre" value=$nombre>
                    <input type="hidden" name="nick" value=$nick>
                    <input type="hidden" name="pass" value=$pass> 
                    <button type="submit" name="modificarUsuario">Editar</button>
                </div>
        </form>
    </div>
EOS;

require_once __DIR__."/plantillas/plantilla.php";