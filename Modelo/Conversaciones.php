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

    public function eliminarConversacionNick($id){
        $Id = new ObjectId($id);
        return $this->collection->deleteOne(['_id' => $Id]);
        
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

        if(in_array($emisor, (array) $resultado['eliminada'])){
            $this->collection->updateOne(
                ['_id' => $id],
                [
                    '$pull' => ['eliminada' => $emisor]
                ]
            );
        }
        
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

    public function actualizarNick($nick_pasado, $nick_nuevo, $id_conver, $estaEliminada, $mensajes) { 
        $id = new ObjectId($id_conver);

        $this->collection->updateOne(
            ['_id' => $id], 
            ['$pull' => ['usuarios' => $nick_pasado]]
        );
    
        $this->collection->updateOne(
            ['_id' => $id], 
            ['$push' => ['usuarios' => $nick_nuevo]]
        );
        
        if($estaEliminada){
            $this->collection->updateOne(
                ['_id' => $id], 
                ['$pull' => ['eliminada' => $nick_pasado]]

            );
        
            $this->collection->updateOne(
                ['_id' => $id], 
                ['$push' => ['eliminada' => $nick_nuevo]]

            );
        }
        
        $this->collection->updateMany(
            ['_id' => $id], 
            ['$set' => ['mensajes.$[msg].usuario_emisor' => $nick_nuevo]],
            ['arrayFilters' => [['msg.usuario_emisor' => $nick_pasado]]]
        );

        $this->collection->updateMany(
            ['_id' => $id], 
            ['$set' => ['mensajes.$[msg].usuario_receptor' => $nick_nuevo]],
            ['arrayFilters' => [['msg.usuario_receptor' => $nick_pasado]]]
        );
    
        return true;
    }
    
    
    

    // Obtener mensajes de una conversación
    public function obtenerConversacionId($conversacionId) {
        $id = new ObjectId($conversacionId);
        $resultado = $this->collection->findOne(['_id' => $id]);
        if ($resultado !== null) {
            $conver = json_encode(iterator_to_array($resultado));
        } else {
            $conver = null;  
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
