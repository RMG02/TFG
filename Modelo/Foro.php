<?php

use MongoDB\BSON\ObjectId;

class Foro {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('foros');
    }

    public function crearForo($DatosForo) {
        $DatosForo['fecha'] = date(DATE_ISO8601);
        $UsuarioExiste = $this->collection->findOne(['titulo' => $DatosForo['titulo']]);
        if ($UsuarioExiste) {
            return "Título de foro ya registrado";
        }
        return $this->collection->insertOne($DatosForo);
    }

    public function obtenerForos() {
        
        $resultado = $this->collection->find();
        if ($resultado !== null) {
            $foro = json_encode(iterator_to_array($resultado));
        } else {
            $foro = null;  
        }
        return $foro;
    }

    public function crearMensaje($Mensaje, $id) {
        $Mensaje['hora'] = date(DATE_ISO8601);
        $Mensaje['mensaje_id'] = new ObjectId();
        $Id = new ObjectId($id);

        return $this->collection->updateOne(['_id' => $Id], ['$push' => ['mensajes' => $Mensaje]]);
    }

    public function obtenerForoId($foroId) {
        $id = new ObjectId($foroId);
        $resultado = $this->collection->findOne(['_id' => $id]);
        if ($resultado !== null) {
            $conver = json_encode(iterator_to_array($resultado));
        } else {
            $conver = null;  
        }
        return $conver;
    }

    public function obtenerForoNick($nick) {
        $foros = $this->collection->find(['suscriptores' => $nick]);
        
        return $foros;
    }

    public function eliminarforoscerrar($nick){
        return $this->collection->deleteMany(['creador' => $nick]); 
    }
    
    public function ObtenerForosNick($nick) {
        $foros = $this->collection->find(['mensajes.nick' => $nick]);
        return $foros;
    }

    public function eliminarPubliNick($id, $nick){
        $Id = new ObjectId($id);
        
        return $this->collection->updateOne(['_id' => $Id], ['$pull' => ['mensajes' => ['nick' => $nick]]]);
        
    }

    public function obtenerForosSuscrito($nick) {
        $foros = $this->collection->find(['suscriptores' => $nick]);
        return $foros;
    }

    public function obtenerForosCreador($nick) {
        $foros = $this->collection->find(['creador' => $nick]);
        return $foros;
    }

    public function obtenerForosNotis($nick) {
        $foros = $this->collection->find(['notificaciones' => $nick]);
        return $foros;
    }

    public function editarPubli($texto, $id_foro, $id_mensaje, $media) {
        $foro_id = new ObjectId($id_foro);
        $mensaje_id = new ObjectId($id_mensaje);
    
        $filter = [
            '_id' => $foro_id,
            'mensajes.mensaje_id' => $mensaje_id 
        ];
    
        $update = [
            '$set' => [
                'mensajes.$.multimedia' => $media, 
                'mensajes.$.contenido' => $texto, 
                'mensajes.$.hora' => date(DATE_ISO8601)
            ]
        ];
    
        return $this->collection->updateOne($filter, $update);
    }
    


    public function eliminarPubli($foroId, $mensajeId) {
        $foro_id = new ObjectId($foroId);
        $mensaje_id = new ObjectId($mensajeId);
       
        $resultado = $this->collection->updateOne(['_id' => $foro_id], ['$pull' => ['mensajes' => ['mensaje_id' => $mensaje_id]]]);
        return $resultado;
    }

    public function eliminarForo($foroId) {
        $id = new ObjectId($foroId);
        
        return $this->collection->deleteOne(['_id' => $id]);

    }

    public function desuscribirForo($foroId, $nick) {
        $id = new ObjectId($foroId);
        $resultado = $this->collection->updateOne(['_id' => $id], ['$pull' => ['suscriptores' => $nick]]);
        return $resultado;
    }

    public function desactivarNotis($foroId, $nick) {
        $id = new ObjectId($foroId);
        $resultado = $this->collection->updateOne(['_id' => $id], ['$pull' => ['notificaciones' => $nick]]);
        return $resultado;
    }

    public function suscribirForo($foroId, $nick) {
        $id = new ObjectId($foroId);
        $resultado = $this->collection->updateOne(['_id' => $id], ['$push' => ['suscriptores' => $nick]]);
        return $resultado;
    }

    public function activarNotis($foroId, $nick) {
        $id = new ObjectId($foroId);
        $resultado = $this->collection->updateOne(['_id' => $id], ['$push' => ['notificaciones' => $nick]]);
        return $resultado;
    }

    public function actualizarNickSuscripcion($nick_pasado, $nick_nuevo, $id_foro) { 
        $id = new ObjectId($id_foro);

        $this->collection->updateOne(
            ['_id' => $id], 
            ['$pull' => ['suscriptores' => $nick_pasado]]
        );
    
        $this->collection->updateOne(
            ['_id' => $id], 
            ['$push' => ['suscriptores' => $nick_nuevo]]
        );
        return true;
    }

    public function actualizarNickNotis($nick_pasado, $nick_nuevo, $id_foro) { 
        $id = new ObjectId($id_foro);

        $this->collection->updateOne(
            ['_id' => $id], 
            ['$pull' => ['notificaciones' => $nick_pasado]]
        );
    
        $this->collection->updateOne(
            ['_id' => $id], 
            ['$push' => ['notificaciones' => $nick_nuevo]]
        );
        return true;
    }

    public function actualizarNickCreador($nick_nuevo, $id_foro) { 
        $id = new ObjectId($id_foro);

        return $this->collection->updateOne(
            ['_id' => $id], 
            ['$set' => ['creador' => $nick_nuevo]]
        );
    
    }

    public function actualizarNickPubli($nick_pasado, $nick_nuevo, $id_foro) { 
        $id = new ObjectId($id_foro);
        
        return $this->collection->updateMany(
            ['_id' => $id], 
            ['$set' => ['mensajes.$[mensaje].nick' => $nick_nuevo]], 
            ['arrayFilters' => [['mensaje.nick' => $nick_pasado]]]
        );
    
    }

}
