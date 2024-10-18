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

$tituloPagina = "Lista de Usuarios";

// Construcción del contenido principal de forma dinámica
$contenidoPrincipal = '<body>';
$contenidoPrincipal .= '<h2 style="text-align: center;">Lista de Usuarios</h2>';
$contenidoPrincipal .= '<table>';
$contenidoPrincipal .= '
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Nick</th>
            <th>Admin</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>';

foreach ($usuarios as $usuario) {
    $contenidoPrincipal .= '<tr>';
    $contenidoPrincipal .= '<td style="padding: 10px;">' . (isset($usuario['nombre']) ? $usuario['nombre'] : 'No definido') . '</td>';
    $contenidoPrincipal .= '<td style="padding: 10px;">' . (isset($usuario['email']) ? $usuario['email'] : 'No definido') . '</td>';
    $contenidoPrincipal .= '<td style="padding: 10px;">' . (isset($usuario['nick']) ? $usuario['nick'] : 'No definido') . '</td>';
    $contenidoPrincipal .= '<td style="padding: 10px;">' . (isset($usuario['admin']) ? ($usuario['admin'] ? 'true' : 'false') : 'No definido') . '</td>';
    
    $contenidoPrincipal .= '<td><a href="editar_usuario.php?id=' . $usuario['email'] . '">Editar</a></td>';
    $contenidoPrincipal .= '</tr>';
}

$contenidoPrincipal .= '
    </tbody>
</table>
</body>';

require_once __DIR__."/plantillas/plantilla.php";
