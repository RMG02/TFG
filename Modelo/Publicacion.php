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
        return $this->collection->insertOne($DatosPublicacion);
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

    public function EditarPublicacion($texto, $id) {
        
        $Id = new ObjectId($id);
        $filter = ['_id' => $Id];
        $update = [
            '$set' => [
                'contenido' => $texto,
                'created_at' => date(DATE_ISO8601)
            ]
        ];
        return $this->collection->updateOne($filter, $update);
    }

}
?>
