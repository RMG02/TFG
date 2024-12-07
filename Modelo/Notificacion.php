<?php
class Notificacion {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('notificaciones');
    }

    public function crearNotificacion($notificacion) {
        return $this->collection->insertOne($notificacion);
    }

    /*public function marcarComoVista($notificacionId) {
    }

    public function obtenerNotificacionesNoVistas($usuario) {
        
    }

    public function obtenerTodasNotificaciones($usuario) {
       
    

    public function borrarNotificacion($notificacionId) {
        
    }*/
}
?>
