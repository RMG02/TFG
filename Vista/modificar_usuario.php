<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
require_once '../Controlador/Admin_controlador.php';

// Obtener todos los documentos (usuarios)
$usuarios = $usuarioModelo->ListaUsuarios();

$tituloPagina = "Lista de Usuarios";

// Construcción del contenido principal de forma dinámica
$contenidoPrincipal = <<<EOS
<input type="text" id="buscador" onkeyup="filtrarUsuarios()" placeholder="Buscar por email...">
<table id="userList">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Nick</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
EOS;

foreach ($usuarios as $usuario) {
    $email = $usuario['email'];
    if($email == $_SESSION['email']){
        continue;
    }
    $nombre = $usuario['nombre'];
    $nick = $usuario['nick'];
    $rol = $usuario['admin'] ? "Admin" : "Usuario";
    $pass = $usuario['password'];
    $contenidoPrincipal .= <<<EOS
    <tr>
        <td>$nombre</td>
        <td>$email</td>
        <td>$nick</td>
        <td>$rol</td>
        <td>
            <form method="POST" class="boton_lista" action="editar_perfil_admin.php">
                <input type="hidden" name="email" value=$email>
                <input type="hidden" name="nombre" value=$nombre>
                <input type="hidden" name="nick" value=$nick>
                <input type="hidden" name="admin" value='{$usuario['admin']}'> 
                <input type="hidden" name="pass" value=$pass>                              
                <button type="submit" name="modificar">Editar cuenta</button>
            </form>
            <form method="POST" class="boton_lista" action="../Controlador/Admin_controlador.php">
                <input type="hidden" name="email" value=$email>
                <button type="submit" name="eliminarUsuario" id="eliminarUsuario">Eliminar cuenta</button>
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Introduce tu contraseña</h2>
                        <form method="POST" action="../Controlador/Admin_controlador.php">
                            <input type="password" name="password" required placeholder="Contraseña"><br><br>
                            <button type="submit" class="boton_lista" name="eliminarUsuario" id="eliminarUsuario">Confirmar</button>
                        </form>
                    </div>
                </div>

                <script>
                    // Obtener el modal
                    var modal = document.getElementById("myModal");

                    // Obtener el botón que abre el modal
                    var btn = document.getElementById("eliminarUsuario");

                    // Obtener el elemento <span> que cierra el modal
                    var span = document.getElementsByClassName("close")[0];

                    // Cuando el usuario haga clic en el botón, abre el modal
                    btn.onclick = function() {
                        modal.style.display = "block";
                    }

                    // Cuando el usuario haga clic en <span> (x), cierra el modal
                    span.onclick = function() {
                        modal.style.display = "none";
                    }

                    // Cuando el usuario haga clic fuera del modal, cierra el modal
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }

                </script>
            </form>
        </td>
    </tr>
    EOS;

    if ($error != "") {
        $contenidoPrincipal .= <<<EOS
            <p class="error">$error</p>
        EOS;
    }
}

$contenidoPrincipal .= <<<EOS
    </tbody>
</table>
EOS;

require_once __DIR__ . "/plantillas/plantilla.php";
?>

<script src="../Recursos/js/filtro_usuario.js"></script>
