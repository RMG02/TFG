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
            'mensajes' => [],
            'eliminada' => []
        ];
        $resultado = $this->collection->insertOne($conversacion);
        //$conversacion = json_encode(iterator_to_array($resultado));
        return $resultado;

    }

    public function eliminarConversacion($id, $usuario){
        $Id = new ObjectId($id);
        $resultado = $this->collection->findOne(['_id' => $Id]);
        
        if(count($resultado['eliminada']) == 1){
            return $this->collection->deleteOne(['_id' => $Id]);
        }
        
        return $this->collection->updateOne(
            ['_id' => $Id], 
            ['$push' => ['eliminada' => $usuario]] 
        );
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
        $resultado = $this->collection->findOne(['_id' => $id]);
        
        if(in_array($receptor, (array) $resultado['eliminada'])){
            $this->collection->updateOne(
                ['_id' => $id],
                [
                    '$push' => ['mensajes' => $mensaje],
                    '$pull' => ['eliminada' => $receptor]
                ]
            );
        }
        else{
            $this->collection->updateOne(
                ['_id' => $id],
                ['$push' => ['mensajes' => $mensaje]]
            );
        }

        return $mensaje['mensaje_id'];

    }

    // Obtener mensajes de una conversación
    public function obtenerConversacionId($conversacionId) {
        $id = new ObjectId($conversacionId);
        $resultado = $this->collection->findOne(['_id' => $id]);
        if ($resultado !== null) {
            $conver = json_encode(iterator_to_array($resultado));
        } else {
            $conver = null;  // O puedes devolver '{}' si prefieres un objeto vacío
        }
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

    public function eliminarMensaje($mensaje_id) {
        $id = new ObjectId($mensaje_id);
        
        return $this->collection->updateOne(
            ['mensajes.mensaje_id' => $id],
            ['$pull' => ['mensajes' => ['mensaje_id' => $id]]] 
        );
    }

    public function editarMensaje($mensaje_id, $contenido) {
        $id = new ObjectId($mensaje_id);
        
        return $this->collection->updateOne(
            ['mensajes.mensaje_id' => $id],  
            ['$set' => ['mensajes.$.contenido' => $contenido]] 
        );
    }
}
