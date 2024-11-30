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

    public function obtenerPublicacion($id) {
        $Id = new ObjectId($id);
        return $this->collection->findOne(['_id' => $Id]);
    }


    private function añadirRespuesta(&$comentarios, $id_com, $comentario_nuevo) {

        foreach ($comentarios as &$comentario) {                
                if ($comentario['id_comentario'] == $id_com) {
                    if (!isset($comentario['respuestas'])) {
                        $comentario['respuestas'] = [];
                    }
                    $comentario['respuestas'][] = $comentario_nuevo;
                    return true;
                } elseif (!empty($comentario['respuestas'])) {
                    if ($this->añadirRespuesta($comentario['respuestas'], $id_com, $comentario_nuevo)) {
                        return true;
                    }
                }         
        }
        return false;
    }

    public function agregarComentario($id, $comentario, $id_comentario_origen) {
        $Id = new ObjectId($id);
        $comentario['id_comentario'] = new ObjectId();

        if($id_comentario_origen){
            $Id_origen = new ObjectId($id_comentario_origen);

            $publicacion = $this->collection->findOne(['_id' => $Id]);

            if ($publicacion) {
               
                if ($this->añadirRespuesta($publicacion['comentarios'], $Id_origen, $comentario)) {
                    return $this->collection->updateOne(['_id' => $Id], ['$set' => ['comentarios' => $publicacion['comentarios']]]);
                }
                
            }
            return false;
        }
        else{
            $update = [ 
                '$push' => [ 
                    'comentarios' => [ 
                        '$each' => [$comentario], 
                        '$sort' => ['fecha' => -1] 
                        ] 
                    ] 
                ];
            return $this->collection->updateOne(['_id' => $Id], $update);
        }
        
    }

    private function eliminarRespuesta(&$comentarios, $id_com) {

        foreach ($comentarios as &$comentario) {
                foreach($comentario['respuestas'] as $posicion => $respuesta){
                    if ($respuesta['id_comentario'] == $id_com) {
                        $respuestasArray = $comentario['respuestas']->getArrayCopy(); 
                        array_splice($respuestasArray, $posicion, 1); 
                        $comentario['respuestas'] = new \MongoDB\Model\BSONArray($respuestasArray);
                        return true;
                    } elseif (!empty($respuesta['respuestas'])) {
                            if ($this->eliminarRespuesta($comentario['respuestas'], $id_com)) {
                                return true;
                            }
                     }
                }        
        }
        return false;
    }

    public function eliminarComentario($id_publi, $id_com, $esRespuesta) {
        $id = new ObjectId($id_publi);
        $comentarioId = new ObjectId($id_com);
        if($esRespuesta){
            $publicacion = $this->collection->findOne(['_id' => $id]);

            if ($publicacion) {
               
                if($this->eliminarRespuesta($publicacion['comentarios'], $comentarioId)){
                    return $this->collection->updateOne( ['_id' => $id], ['$set' => ['comentarios' => $publicacion['comentarios']]] ); 
                }      
            }
            return false; 

        }
        $update = [
            '$pull' => [
                'comentarios' => ['id_comentario' => $comentarioId]
            ]
        ];
        return $this->collection->updateOne( ['_id' => $id], $update);
    }

    private function editarRespuesta(&$comentarios, $id_com, $texto, $media) {
        foreach ($comentarios as &$comentario) {
                if ($comentario['id_comentario'] == $id_com) {
                    $comentario['multimedia'] = $media;
                    $comentario['texto'] = $texto;
                    $comentario['fecha'] = date(DATE_ISO8601);
                    return true;
                } elseif (!empty($comentario['respuestas'])) {
                        if ($this->editarRespuesta($comentario['respuestas'], $id_com, $texto, $media)) {
                            return true;
                        } 
                }
            
        }
        return false;
    }
    public function editarComentario($id_publi, $id_com, $texto, $media, $esRespuesta) {
        $id = new ObjectId($id_publi);
        $comentarioId = new ObjectId($id_com);

        if ($esRespuesta) {

            $publicacion = $this->collection->findOne(['_id' => $id]);

            if ($publicacion) {
                   
                if($this->editarRespuesta($publicacion['comentarios'], $comentarioId, $texto, $media)){
                    return $this->collection->updateOne(['_id' => $id], ['$set' => ['comentarios' => $publicacion['comentarios']]]);
                }
                
            }
            return false;
        }
      
        $update = [
            '$set' => [
                'comentarios.$.multimedia' => $media,
                'comentarios.$.texto' => $texto,
                'comentarios.$.fecha' => date(DATE_ISO8601) 
            ]
        ];
        
        $resultado = $this->collection->updateOne(
            ['_id' => $id, 'comentarios.id_comentario' => $comentarioId],
            $update
        );
        
        if ($resultado->getModifiedCount() > 0) {
            $UpdateOrdenado = [
                '$push' => [
                    'comentarios' => [
                        '$each' => [],
                        '$sort' => ['fecha' => -1] 
                    ]
                ]
            ];
            $this->collection->updateOne(['_id' => $id], $UpdateOrdenado);
        }
        
        return $resultado;
        
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

        
    public function Likes($nick,$publi){
        $Id = new ObjectId($publi);
        $filter = ['_id' => $Id];
        $update =
                [
                    '$push' => ['likes' => $nick], // Agregar el usuario al array
                    '$pull' => ['dislikes' => $nick] // Asegurarse de quitar el dislike si existía
                ];
            
        return $this->collection->updateOne($filter, $update);
    }
    public function DisLikes($nick,$publi){
        $Id = new ObjectId($publi);
        $filter = ['_id' => $Id];
        $update = 
                [
                    '$push' => ['dislikes' => $nick], // Agregar el usuario al array
                    '$pull' => ['likes' => $nick] // Asegurarse de quitar el dislike si existía
                ];
        return $this->collection->updateOne($filter, $update);
                
    }
    public function Likesq($nick,$publi){
        $Id = new ObjectId($publi);
        $filter = ['_id' => $Id];
        $update =
                
                [
                    '$pull' => ['likes' => $nick]
                ];
            
        return $this->collection->updateOne($filter, $update);    
    }
    
    public function DisLikesq($nick,$publi){
        $Id = new ObjectId($publi);
        $filter = ['_id' => $Id];
        $update =
                [
                    '$pull' => ['dislikes' => $nick]
                ];
        return $this->collection->updateOne($filter, $update);
            
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

