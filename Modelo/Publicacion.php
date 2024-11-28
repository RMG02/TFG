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

    public function agregarComentario($id, $comentario, $id_comentario_origen) {
        $Id = new ObjectId($id);
        $comentario['id_comentario'] = new ObjectId();

        if($id_comentario_origen){
            $Id_origen = new ObjectId($id_comentario_origen);

            $update = [
                '$push' => [
                    'comentarios.$.respuestas' => [
                        '$each' => [$comentario],
                        '$sort' => ['fecha' => -1] 
                    ]
                ]
            ];
            return $this->collection->updateOne(
                ['_id' => $Id, 'comentarios.id_comentario' => $Id_origen], 
                $update
            );
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

    public function eliminarComentario($id_publi, $id_com) {
        $id = new ObjectId($id_publi);
        $comentarioId = new ObjectId($id_com);
        $update = [
            '$pull' => [
                'comentarios' => ['id_comentario' => $comentarioId]
            ]
        ];
        return $this->collection->updateOne( ['_id' => $id], $update);
    }

    public function editarComentario($id_publi, $id_com, $texto, $media) {
        $id = new ObjectId($id_publi);
        $comentarioId = new ObjectId($id_com);
    
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

    public function Likes($id,$publi){
        $Id = new ObjectId($publi);
        $filter = ['_id' => $publi];
        $this->collection->updateOne(
                ['_id' => $publi],
                [
                    '$push' => ['likes' => $id], // Agregar el usuario al array
                    '$pull' => ['dislikes' => $id] // Asegurarse de quitar el dislike si existía
                ]
            );
    }
    public function DisLikes($id,$publi){
        $Id = new ObjectId($publi);
        $filter = ['_id' => $publi];
        $this->collection->updateOne(
                ['_id' => $publi],
                [
                    '$push' => ['dislikes' => $id], // Agregar el usuario al array
                    '$pull' => ['likes' => $id] // Asegurarse de quitar el dislike si existía
                ]
            );
    }
    public function Likesq($id,$publi){
        $Id = new ObjectId($publi);
        $filter = ['_id' => $publi];
        $this->collection->updateOne(
                ['_id' => $publi],
                [
                    ['$pull' => ['likes' => $id]]
                ]
            );
    }
    public function DisLikesq($id,$publi){
        $Id = new ObjectId($publi);
        $filter = ['_id' => $publi];
        $this->collection->updateOne(
                ['_id' => $publi],
                [
                    ['$pull' => ['dislikes' => $id]]
                ]
            );
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
