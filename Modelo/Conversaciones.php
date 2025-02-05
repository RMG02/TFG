<?php

use MongoDB\BSON\ObjectId;

class Conversacion {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('conversaciones');
    }

    // Crear una nueva conversación con dos usuarios
    public function crearConversacion($usuario1, $usuario2) {
        $conversacion = [
            'usuarios' => [$usuario1, $usuario2],
            'mensajes' => []
        ];
        $resultado = $this->collection->insertOne($conversacion);
        //$conversacion = json_encode(iterator_to_array($resultado));
        return $resultado;

    }

    // Agregar un mensaje a una conversación
    public function agregarMensaje($conversacionId, $emisor, $contenido, $receptor, $hora) {
        $mensaje = [
            'mensaje_id' => new ObjectId(),
            'usuario_emisor' => $emisor,
            'usuario_receptor' => $receptor,
            'contenido' => $contenido,
            'hora' => $hora,
        ];
        $id = new ObjectId($conversacionId);
        return $this->collection->updateOne(
            ['_id' => $id],
            ['$push' => ['mensajes' => $mensaje]]
        );
    }

    // Obtener mensajes de una conversación
    public function obtenerConversacionId($conversacionId) {
        $id = new ObjectId($conversacionId);
        $resultado = $this->collection->findOne(['_id' => $id]);
        $conver = json_encode(iterator_to_array($resultado)); 
        return $conver;
    }

    // Obtener listas de conversaciones para un usuario
    public function obtenerConversaciones($usuario) {
        $conversaciones = $this->collection->find(['usuarios' => $usuario]);
        return $conversaciones;
    }

    //Obtener conversacion 
    public function obtenerConversacion($usuario1, $usuario2) {
        $resultado = $this->collection->find(['usuarios' => [ '$all' => [$usuario1, $usuario2]]]);
        $conversacion = json_encode(iterator_to_array($resultado));
        return $conversacion;
    }
}
