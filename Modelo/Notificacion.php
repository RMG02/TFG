<?php

use MongoDB\BSON\ObjectId;

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
        
        if ($tipo != "publicacion") {
            return $this->collection->deleteMany($filtro);
        } else {
            return $this->collection->deleteMany(['id_publi' => $id_publi]);
        }
    }

    public function borrarNotificacionUnica($id) {
        $Id = new ObjectId($id);

        return $this->collection->deleteOne(['_id' => $Id]);
    }
    
    public function obtenerTodasNotificaciones($usuario) {
        return $this->collection->find(['usuario_publi' => $usuario], ['sort' => ['fecha' => -1]]);

    }

    public function obtenerTodasNotificacionesId($id) {
        return $this->collection->find(['id_publi' => $id]);

    }
    public function borrarNotificacionComentario($id_publi, $id_comentario, $id_comentario_origen){
        if($id_comentario_origen){
            $filtro = [
                'id_publi' => $id_comentario_origen,
                'id_comentario' => $id_comentario
            ];
        }
        else{
            $filtro = [
                'id_publi' => $id_publi,
                'id_comentario' => $id_comentario
            ];
        }
        
        return $this->collection->deleteMany($filtro);
    }

   
}
?>
