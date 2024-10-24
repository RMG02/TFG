
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$tituloPagina = "Página de Perfil";

$contenidoPrincipal = <<<EOS
    <h3>Datos usuario:</h3>
    <p>Nick: {$_SESSION['nick']}</p>
    <p>Nombre: {$_SESSION['nombre']} </p> 
    <p>Email: {$_SESSION['email']} </p> 
    <p><a href='/Vista/Editarperfil.php'>  Editar perfil</a></p>
    <form method="POST" class="boton_lista" action="../Controlador/Usuario_controlador.php">
                <button type="submit" name="cerrarCuenta" id="cerrarCuenta">Cerrar cuenta</button>
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Introduce tu contraseña</h2>
                        <form method="POST" action="../Controlador/Usuario_controlador.php">
                            <input type="password" name="password" required placeholder="Contraseña"><br><br>
                            <button type="submit" class="boton_lista" name="cerrarCuenta" id="cerrarCuenta">Confirmar</button>
                        </form>
                    </div>
                </div>

                <script>
                    // Obtener el modal
                    var modal = document.getElementById("myModal");

                    // Obtener el botón que abre el modal
                    var btn = document.getElementById("cerrarCuenta");

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


EOS;

if ($error != "") {
    $contenidoPrincipal .= <<<EOS
        <p class="error">$error</p>
    EOS;
}

require_once __DIR__."/plantillas/plantilla.php";
