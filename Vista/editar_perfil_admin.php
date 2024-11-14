
<?php


$tituloPagina = "Página de Edicion";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";
$mensaje = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}


if(isset($_POST['modificar'])){
    $_SESSION['usuario_email'] = $_POST['email'];
    $_SESSION['usuario_nick'] = $_POST['nick'];
    $_SESSION['usuario_nombre'] = $_POST['nombre'];
    $_SESSION['usuario_admin'] = $_POST['admin'];
    $_SESSION['usuario_pass'] = $_POST['pass'];
    
}

$rolUsuario = ($_SESSION['usuario_admin'] == false) ? 'selected' : ''; 
$rolAdmin = ($_SESSION['usuario_admin'] == true) ? 'selected' : '';

$contenidoPrincipal = <<<EOS
    <div class="contenedor">
        <form method="POST" id="modificarUsuario" class="formulario" action="../Controlador/Admin_controlador.php">
                <label for="nombre"> Nombre </label>
                <input id='nombre_nuevo' type='text' name='nombre_nuevo'  placeholder={$_SESSION['usuario_nombre']}> 

                <label for="nick"> Nick </label>
                <input id="nick_nuevo" type="text" name="nick_nuevo" placeholder={$_SESSION['usuario_nick']}> 
                
                <label for="email"> Email </label>
                <input id="email_nuevo" type="text" name="email_nuevo" placeholder={$_SESSION['usuario_email']}> 
                
                <label for="password"> Contraseña </label>
                <input id="pass_nuevo" type="password" name="pass_nuevo" placeholder=""> 
                
                <label for="rol">Rol:</label>
                <select name="admin_nuevo" required>
                    <option value="usuario" $rolUsuario>Usuario</option>
                    <option value="admin" $rolAdmin>Admin</option>
                </select>
 
                <input type="hidden" name="email" value={$_SESSION['usuario_email']}>
                <input type="hidden" name="nombre" value={$_SESSION['usuario_nombre']}>
                <input type="hidden" name="nick" value={$_SESSION['usuario_nick']}>
                <input type="hidden" name="pass" value={$_SESSION['usuario_pass']}> 
                <button type="button" class="botonInit">Editar</button>
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>¿Estás seguro de realizar los cambios?</h2>
                        <button type="submit" name="modificarUsuario">Confirmar</button>
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

if ($mensaje != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="mensaje">$mensaje</p>
    EOS;
}

require_once __DIR__."/plantillas/plantilla.php";
?>

<script src="../Recursos/js/aviso.js"></script>
