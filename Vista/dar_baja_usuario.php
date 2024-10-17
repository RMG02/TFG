<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../Config/config.php';
require_once '../Modelo/Usuario.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->TFG;
$collection = $database->usuarios; 

// Obtener todos los documentos (usuarios)
$usuarios = $collection->find();

$tituloPagina = "Añadir usuario";

// Construcción del contenido principal de forma dinámica
$contenidoPrincipal = '<body>';
$contenidoPrincipal .= '<h2 style="text-align: center;">Lista de Usuarios</h2>';
$contenidoPrincipal .= '<table>';
$contenidoPrincipal .= '
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th> <!-- Puedes agregar más campos si es necesario -->
        </tr>
    </thead>
    <tbody>';

foreach ($usuarios as $usuario) {
    $contenidoPrincipal .= '<tr>';
    $contenidoPrincipal .= '<td>' . $usuario['nombre'] . '</td>';
    $contenidoPrincipal .= '<td>' . (isset($usuario['email']) ? $usuario['email'] : 'No definido') . '</td>';
    $contenidoPrincipal .= '<td>
        <form method="POST" id="darBajaUsuario" action="../Controlador/Admin_controlador.php">
            <input type="hidden" name="email" value="' . $usuario['email'] . '">
            <button type="submit" name="darBajaUsuario">Dar de baja usuario</button>
        </form>
    </td>';
    $contenidoPrincipal .= '</tr>';
}

$contenidoPrincipal .= '
    </tbody>
</table>
</body>';

require_once __DIR__."/plantillas/plantilla.php";
