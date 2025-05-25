<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['listaUsuarios'])) {
    header('Location: ../Controlador/Admin_controlador.php?listarUsuarios=true');
    exit;
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

$usuarios = json_decode($_SESSION['listaUsuarios'], true);

$tituloPagina = "Lista de Usuarios";
$modalId = -1;

$contenidoPrincipal = <<<EOS
<input type="text" id="buscador" onkeyup="filtrarUsuarios()" placeholder="Buscar por nick...">
<table id="userList">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Nick</th>
            <th>Rol</th>
            <th>Cuenta confirmada</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
EOS;

foreach ($usuarios as $index => $usuario) {
    $email = $usuario['email'];
    if($email == $_SESSION['email']){
        continue;
    }
    $nombre = $usuario['nombre'];
    $nick = $usuario['nick'];
    $rol = $usuario['admin'] ? "Admin" : "Usuario";
    $pass = $usuario['password'];
    
    if($usuario['confirmado']){
        $confirmado = "SI";
    }
    else{
        $confirmado = "NO";
    }
    
    $modalId++;
    $contenidoPrincipal .= <<<EOS
    <tr>
        <td>$nombre</td>
        <td>$email</td>
        <td>$nick</td>
        <td>$rol</td>
        <td>$confirmado</td>
        <td>
            <form method="POST" class="boton_lista" action="editar_perfil_admin.php">
                <input type="hidden" name="email" value=$email>
                <input type="hidden" name="nombre" value=$nombre>
                <input type="hidden" name="nick" value=$nick>
                <input type="hidden" name="admin" value='{$usuario['admin']}'> 
                <input type="hidden" name="pass" value=$pass>                              
                <button type="submit" name="modificar">Editar cuenta</button>
            </form>
            <button type="button" class="botonInit">Eliminar cuenta</button>
            <div id="$modalId" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Introduce tu contraseña</h2>
                    <form method="POST" action="../Controlador/Admin_controlador.php" onsubmit="eliminarCuentaAdmin(event)">
                        <input type="hidden" name="email" value=$email>
                        <input type="hidden" id="nick_borrado" name="nick" value="$nick">
                        <input type="hidden" id="siguiendo" name="nick" value="$nick">
                        <input type="password" name="password" required placeholder="Contraseña"><br><br>
                        <button type="submit" class="boton_lista" name="eliminarUsuario">Confirmar</button>
                    </form>
                 </div>
            </div>
        </td>
    </tr>
    EOS;

    
}


$contenidoPrincipal .= <<<EOS
    </tbody>
</table>
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

require_once __DIR__ . "/plantillas/plantilla.php";
?>

<script src="../Recursos/js/filtro_usuario.js"></script>
<script src="../Recursos/js/cerrar_cuentas.js"></script>
