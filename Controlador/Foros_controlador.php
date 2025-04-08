<?php

require_once '../Config/config.php';
require_once '../Modelo/Foro.php';
require_once '../Modelo/Usuario.php';
require_once '../Modelo/Conversaciones.php';



if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$forosModelo = new Foro($db);
$UsuarioModelo = new Usuario($db);
$conversacionesModelo = new Conversacion($db);
$dir_archivos = '../Recursos/multimedia';




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['CrearForo'])) {
        $DatosForo = [
            'titulo' => $_POST['titulo'],
            'descripcion' => $_POST['descripcion'],
            'creador' => $_SESSION['nick'],
            'suscriptores' =>[$_SESSION['nick']],
            'mensajes' => [],
            'notificaciones' => []
        ];
        
        if($_SESSION['forosCreados'] < 5){
            $resultado = $forosModelo->crearForo($DatosForo);
            if($resultado == "Título de foro ya registrado"){
                $_SESSION['error'] = "Título de foro ya registrado";
                header('Location: ../Vista/crear_foro.php'); 
                exit; 
            }
            else{
                $_SESSION['forosCreados'] += 1;
                $UsuarioModelo->sumaForo($_SESSION['nick']);
                $id = $resultado->getInsertedId()->__toString();

                header('Location: ../Vista/foro.php?foroId=' . $id);
                exit;  
            }
        }
        else{
            $_SESSION['error'] = "Has alcanzado el límite de foros creados (5)";
            header('Location: ../Vista/crear_foro.php'); 
            exit; 
        }

        
    }

    if(isset($_POST['EliminarNick'])){

        $resultado = $forosModelo->ObtenerForosNick($_POST['nick']);
        $foros_json = json_encode(iterator_to_array($resultado));
        $foros = json_decode($foros_json, true);
        
        $resultado = $forosModelo->obtenerForosSuscrito($_POST['nick']);
        $foros_suscrito_json = json_encode(iterator_to_array($resultado));
        $foros_suscrito = json_decode($foros_suscrito_json, true);

        $resultado = $forosModelo->obtenerForosNotis($_POST['nick']);
        $foros_notis_json = json_encode(iterator_to_array($resultado));
        $foros_notis = json_decode($foros_notis_json, true);

        if(!empty($foros)){
            foreach ($foros as $foro) {
                $forosModelo->eliminarPubliNick($foro['_id']['$oid'], $_POST['nick']);
            }
        }

        if(!empty($foros_suscrito)){
            foreach ($foros_suscrito as $foro) {
                $forosModelo->desuscribirForo($foro['_id']['$oid'], $_POST['nick']);
            }
        }
        
        if(!empty($foros_notis)){
            foreach ($foros_notis as $foro) {
                $forosModelo->desactivarNotis($foro['_id']['$oid'], $_POST['nick']);
            }
        }
        
        header('Location: ' . $_SESSION['url_anterior']);
        exit;  
    }

    if(isset($_POST['cambioNick'])){

        $resultado = $forosModelo->ObtenerForosNick($_POST['nick_pasado']);
        $foros_json = json_encode(iterator_to_array($resultado));
        $foros = json_decode($foros_json, true);
        
        $resultado = $forosModelo->obtenerForosSuscrito($_POST['nick_pasado']);
        $foros_suscrito_json = json_encode(iterator_to_array($resultado));
        $foros_suscrito = json_decode($foros_suscrito_json, true);

        $resultado = $forosModelo->obtenerForosCreador($_POST['nick_pasado']);
        $foros_creador_json = json_encode(iterator_to_array($resultado));
        $foros_creador = json_decode($foros_creador_json, true);

        $resultado = $forosModelo->obtenerForosNotis($_POST['nick_pasado']);
        $foros_notis_json = json_encode(iterator_to_array($resultado));
        $foros_notis = json_decode($foros_notis_json, true);

        if(!empty($foros)){
            foreach ($foros as $foro) {
                $forosModelo->actualizarNickPubli($_POST['nick_pasado'], $_POST['nuevoNick'], $foro['_id']['$oid']);
            }
        }

        if(!empty($foros_suscrito)){
            foreach ($foros_suscrito as $foro) {
                $forosModelo->actualizarNickSuscripcion($_POST['nick_pasado'], $_POST['nuevoNick'], $foro['_id']['$oid']);
            }
        }

        if(!empty($foros_creador)){
            foreach ($foros_creador as $foro) {
                $forosModelo->actualizarNickCreador( $_POST['nuevoNick'], $foro['_id']['$oid']);
            }
        }

        if(!empty($foros_notis)){
            foreach ($foros_notis as $foro) {
                $forosModelo->actualizarNickNotis($_POST['nick_pasado'], $_POST['nuevoNick'], $foro['_id']['$oid']);
            }
        }
        
        header('Location: ' . $_SESSION['url_anterior']);
        exit;  
    }

    if(isset($_POST['CrearMensaje'])){

        $archivo = $_FILES['archivo'];
        $archivo_subido = '';
    
        if ($archivo && $archivo['error'] == 0) {
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            // Verificar si la extensión es una imagen
            $permitidas = array('jpg', 'jpeg', 'png');
            if (!in_array($extension, $permitidas)) {
                $_SESSION['error'] = "Error al crear la publicación, solo se permiten imágenes.";
                header('Location: ../Vista/Principal.php?id_foro=' . $_POST['id_foro']);
                exit;
            }
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }

        $Mensaje = [
            'multimedia' => $archivo_subido,
            'email' => $_SESSION['email'],
            'contenido' => $_POST['contenido'],
            'nick' => $_SESSION['nick']
        ]; 
        $resultado = $forosModelo->crearMensaje($Mensaje, $_POST['id_foro']);
        if($resultado){
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id_foro']); 
        }
        else{
            $_SESSION['error'] = "Error al publicar el mensaje";
            header('Location: ../Vista/crear_mensaje_foro.php?id_foro=' . $_POST['id_foro'] . '&suscrito=' . $_POST['suscrito']); 
        }
        
        exit;
    }
    

    if(isset($_POST['EliminarPubli'])){
        $resultado = $forosModelo->eliminarPubli($_POST['Foro-id'], $_POST['Mensaje-id']);
        if($resultado == null){
            $_SESSION['error'] = "Error al eliminar el mensaje";
        }
        else{
            $_SESSION['mensaje'] = "Mensaje eliminado";
        }
        header('Location: ../Vista/foro.php?foroId=' . $_POST['Foro-id']); 
        exit;
    }
    
    if(isset($_POST['Desuscribirforo'])){
        $resultado = $forosModelo->desuscribirForo($_POST['id'], $_SESSION['nick']);
        if($resultado == null){
            $_SESSION['error'] = "Error al desuscribirse del foro";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        else{
            $_SESSION['mensaje'] = "Desuscripción correcta";
            $UsuarioModelo->foroDesuscrito($_POST['id'],$_SESSION['nick']);
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        
        exit;
    }

    if(isset($_POST['DesactivarNotis'])){
        $resultado = $forosModelo->desactivarNotis($_POST['id'], $_SESSION['nick']);
        if($resultado == null){
            $_SESSION['error'] = "Error al desactivar las notificaciones del foro";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        else{
            $_SESSION['mensaje'] = "Notificaciones desactivadas correctamente";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        
        exit;
    }
    

    if(isset($_POST['Suscribirforo'])){
        $resultado = $forosModelo->suscribirForo($_POST['id'], $_SESSION['nick']);
        if($resultado == null){
            $_SESSION['error'] = "Error al suscribirse del foro";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        else{
            $_SESSION['mensaje'] = "suscripción correcta";
            $UsuarioModelo->foroSuscrito($_POST['id'],$_SESSION['nick']);
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        
        exit;
    }

    if(isset($_POST['ActivarNotis'])){
        $resultado = $forosModelo->activarNotis($_POST['id'], $_SESSION['nick']);
        if($resultado == null){
            $_SESSION['error'] = "Error al activar las notificaciones del foro";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        else{
            $_SESSION['mensaje'] = "Notificaciones activadas correctamente";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        
        exit;
    }

    if (isset($_POST['eliminarForo'])) {
        $resultado = $forosModelo->eliminarForo($_POST['id']);
        if($resultado == null){
            $_SESSION['error'] = "Error al eliminar el foro";
            header('Location: ../Vista/foro.php?foroId=' . $_POST['id']); 
        }
        else{
            $_SESSION['mensaje'] = "Foro eliminado";
            $_SESSION['forosCreados'] -= 1;
            $UsuarioModelo->restaForo($_SESSION['nick']);
            header('Location: ../Vista/Foros.php'); 
        }
        
        exit; 
    }

    if(isset($_POST['EditarPubli'])){
        $archivo = $_FILES['nuevo_archivo'];
        $archivo_subido = $_POST['archivo_origen'];
        $id_foro = $_POST['id_foro'];
        $id_mensaje = $_POST['id_mensaje'];  
  

        if ($archivo && $archivo['error'] == 0) {
            $anterior = "../Recursos/multimedia/$archivo_subido";
            unlink($anterior);
            $tmp_name = $archivo['tmp_name'];
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            // Verificar si la extensión es una imagen
            $permitidas = array('jpg', 'jpeg', 'png');
            if (!in_array($extension, $permitidas)) {
                $_SESSION['error'] = "Error al modificar la publicación, solo se permiten imágenes."; 
                header('Location: ../Vista/foro.php?foroId=' . $id_foro); 
                exit;
            }
            $nombre = uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, "$dir_archivos/$nombre");
            $archivo_subido = $nombre;
        }

        $resultado = $forosModelo->editarPubli($_POST['contenido'], $id_foro, $id_mensaje, $archivo_subido);
        if ($resultado) {
            $_SESSION['mensaje'] = "Publicación editada";
            
        }
        else{
            $_SESSION['error'] = "Error al editar la publicación.";
        } 
        header('Location: ../Vista/foro.php?foroId=' . $id_foro); 
        exit;
        
    }



    
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    if (isset($_GET['ObtenerListaForos'])) {
        $resultado = $forosModelo->obtenerForos();
        $foros = json_decode($resultado, true);
        $_SESSION['foros'] = $foros;
        
        header('Location: ../Vista/Foros.php'); 
        exit; 
    }

    if (isset($_GET['ObtenerForoId'])) {
        $resultado = $forosModelo->obtenerForoId($_GET['foroId']);
        if($resultado != null){
            $foro = json_decode($resultado, true);
            usort($foro["mensajes"], function($a, $b) {
                return strtotime($b["hora"]) - strtotime($a["hora"]);
            });
            $_SESSION['foro'] = $foro;
        }
        else{
            $id = null;
            $_SESSION['foro'] = null;
            $resultado = $UsuarioModelo->obtenerUsuarioNick($_SESSION['nick']);
            $usuario_json = json_encode(iterator_to_array($resultado));
            $usuario = json_decode($usuario_json, true);

            if (in_array($_GET['foroId'], $usuario['forosSuscrito'])) {
                $UsuarioModelo->foroDesuscrito($_GET['foroId'],$_SESSION['nick']);
            }

            header('Location: ../Vista/foro.php'); 
            exit;
        }
        if(isset($_GET['suscrito'])){
            header('Location: ../Vista/crear_mensaje_foro.php?id_foro=' . $_GET['foroId'] . '&suscrito=' . $_GET['suscrito']); 
        }
        else{
            header('Location: ../Vista/foro.php?foroId=' . $_GET['foroId']); 
        }
        exit; 
    }

    if (isset($_GET['ObtenerInfoCompartir'])) {
        $resultadoForo = $forosModelo->obtenerForoNick($_GET['nick']);
        $_SESSION['forosSuscrito'] = json_encode(iterator_to_array($resultadoForo));

        $resultadoConver = $conversacionesModelo->obtenerConversaciones($_GET['nick']);
        $_SESSION['conversaciones_abiertas'] = json_encode(iterator_to_array($resultadoConver));

        header('Location: ' . $_SESSION['url_anterior']);
        exit;
    }


}
?>
<script src="../Recursos/js/socket.js"></script>
