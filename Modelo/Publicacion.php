<?php
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

    public function ListaPublicacion() {
        return $this->collection->find();
    }

}
?>
