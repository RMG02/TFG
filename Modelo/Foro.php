<?php

use MongoDB\BSON\ObjectId;

class Foro {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('foros');
    }

    public function crearForo($DatosForo) {
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

    public function suscribirForo($foroId, $nick) {
        $id = new ObjectId($foroId);
        $resultado = $this->collection->updateOne(['_id' => $id], ['$push' => ['suscriptores' => $nick]]);
        return $resultado;
    }

}
