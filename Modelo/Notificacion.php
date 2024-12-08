<?php
class Notificacion {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('notificaciones');
    }

    public function crearNotificacion($notificacion) {
        return $this->collection->insertOne($notificacion);
    }

    public function borrarNotificacion($id_publi, $usuario_accion, $tipo) {
        $filtro = [
            'id_publi' => $id_publi,
            'usuario_accion' => $usuario_accion,
            'tipo' => $tipo
        ];
    
        return $this->collection->deleteMany($filtro);

    }
    
    public function obtenerTodasNotificaciones($usuario) {
        return $this->collection->find(['usuario_publi' => $usuario], ['sort' => ['fecha' => -1]]);

    }

    /*public function marcarComoVista($notificacionId) {
    }

    public function obtenerNotificacionesNoVistas($usuario) {
        
    }

       
    

    public function borrarNotificacion($notificacionId) {
        
    }*/
}
?>
