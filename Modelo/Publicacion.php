<?php

use MongoDB\BSON\ObjectId;
class Publicacion {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('publicaciones');
    }

    // Crear una nueva publicación
    public function crearPublicacion($DatosPublicacion) {
        $DatosPublicacion['created_at'] = date(DATE_ISO8601);
        $DatosPublicacion['comentarios'] = [];
        return $this->collection->insertOne($DatosPublicacion);
    }

    public function agregarComentario($id, $comentario) {
        $Id = new ObjectId($id);
        $update = [
            '$push' => [
                'comentarios' => $comentario
            ]
        ];
        return $this->collection->updateOne(['_id' => $Id], $update);
    }

    public function eliminarPublicacion($id) {
        $Id = new ObjectId($id);
        return $this->collection->deleteOne(['_id' => $Id]);
    }

    public function ListaPublicacion() {
        return $this->collection->find([], ['sort' => ['created_at' => -1]]);
    }

    public function ListaPublicacionUsuario($nick) {
        return $this->collection->find(['nick' => $nick], ['sort' => ['created_at' => -1]]);
    }

    public function EditarPublicacion($texto, $id, $media) {
        
        $Id = new ObjectId($id);
        $filter = ['_id' => $Id];
        $update = [
            '$set' => [
                'multimedia' => $media,
                'contenido' => $texto,
                'created_at' => date(DATE_ISO8601)
            ]
        ];
        return $this->collection->updateOne($filter, $update);
    }

}
?>
