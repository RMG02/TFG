<?php

use MongoDB\BSON\ObjectId;
class Receta {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('recetas');
    }

    // Crear una nueva publicación
    public function crearReceta($Datosreceta) {
        $Datosreceta['created_at'] = date(DATE_ISO8601);
        $Datosreceta['comentarios'] = [];
        return $this->collection->insertOne($Datosreceta);
    }

    function obtenerReceta($id) {
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

            $receta = $this->collection->findOne(['_id' => $Id]);

            if ($receta) {
               
                if ($this->añadirRespuesta($receta['comentarios'], $Id_origen, $comentario)) {
                    $resultado = $this->collection->updateOne(['_id' => $Id], ['$set' => ['comentarios' => $receta['comentarios']]]);
                }
                
            }
            else{
                $resultado = false;
            }
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
            $resultado = $this->collection->updateOne(['_id' => $Id], $update);
        }

        if($resultado){
            return [
                'resultado' => $resultado,
                'id_comentario' => $comentario['id_comentario']
            ];
        }
        else{
            return [
                'resultado' => $resultado,
            ];
        }
        
    }

    private function eliminarComentariosMultimedia(&$comentarios) {
        foreach ($comentarios as &$comentario) {
            // Eliminar archivo multimedia del comentario
            if (!empty($comentario['multimedia'])) {
                $archivo = "../Recursos/multimedia/{$comentario['multimedia']}";
                if (file_exists($archivo)) {
                    unlink($archivo);
                }
            }
    
            // Si el comentario tiene respuestas, eliminarlas recursivamente
            if (!empty($comentario['respuestas'])) {
                $this->eliminarComentariosMultimedia($comentario['respuestas']);
            }
        }
    }
    

    private function eliminarRespuesta(&$comentarios, $id_com) {

        foreach ($comentarios as &$comentario) {
                foreach($comentario['respuestas'] as $posicion => $respuesta){
                    if ($respuesta['id_comentario'] == $id_com) {
                        if(!empty($respuesta['respuestas'])){
                            $this->eliminarComentariosMultimedia($respuesta['respuestas']);
                        }
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
        $receta = $this->collection->findOne(['_id' => $id]);

        if($esRespuesta){
            if ($receta) {
               
                if($this->eliminarRespuesta($receta['comentarios'], $comentarioId)){
                    return $this->collection->updateOne( ['_id' => $id], ['$set' => ['comentarios' => $receta['comentarios']]] ); 
                }      
            }
            return false; 

        }
        if($receta){
            foreach ($receta['comentarios'] as &$comentario) {
                if ($comentario['id_comentario'] == $id_com) {
                    if(!empty($comentario['respuestas'])){
                        $this->eliminarComentariosMultimedia($comentario['respuestas']);
                    }
                    
                    $update = [
                        '$pull' => [
                            'comentarios' => ['id_comentario' => $comentarioId]
                        ]
                    ];
                    return $this->collection->updateOne( ['_id' => $id], $update);
                }
            }
            
        }
        return false;
        
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

    public function cambiarnickpublicacion($antiguonick, $nuevonick) {
        // Obtener todas las publicaciones donde pueda estar el nick antiguo
        $recetas = $this->collection->find([
            '$or' => [
                ['nick' => $antiguonick],
                ['comentarios.usuario' => $antiguonick],
                ['comentarios.respuestas.usuario' => $antiguonick]
            ]
        ]);
    
        foreach ($recetas as $receta) {
            $actualizado = false;
    
            if (isset($receta['nick']) && $receta['nick'] === $antiguonick) {
                $receta['nick'] = $nuevonick;
                $actualizado = true;
            }
    
            if (isset($receta['comentarios'])) {
                $comentarios = $receta['comentarios'];
                $this->actualizarNickEnComentarios($comentarios, $antiguonick, $nuevonick, $actualizado);
                $receta['comentarios'] = $comentarios; // Actualizar en la publicación
            }
    
            if ($actualizado) {
                $this->collection->replaceOne(
                    ['_id' => $receta['_id']],
                    $receta
                );
            }
        }
    }
    
    private function actualizarNickEnComentarios(&$comentarios, $antiguonick, $nuevonick, &$actualizado) {
        foreach ($comentarios as &$comentario) {
            // Cambiar el nick en el comentario actual
            if (isset($comentario['usuario']) && $comentario['usuario'] === $antiguonick) {
                $comentario['usuario'] = $nuevonick;
                $actualizado = true;
            }
    
            // Cambiar el nick en las respuestas si existen
            if (isset($comentario['respuestas'])) {
                $this->actualizarNickEnComentarios($comentario['respuestas'], $antiguonick, $nuevonick, $actualizado);
            }
        }
    }
    
    public function actualizarNickLikes($nick_pasado, $nick_nuevo, $id_receta) { 
        $id = new ObjectId($id_receta);

        $this->collection->updateOne(
            ['_id' => $id], 
            ['$pull' => ['likes' => $nick_pasado]]
        );
    
        $this->collection->updateOne(
            ['_id' => $id], 
            ['$push' => ['likes' => $nick_nuevo]]
        );
        
        return true;
    }

    public function eliminarNickLikes($nick, $id_receta) { 
        $id = new ObjectId($id_receta);

        $this->collection->updateOne(
            ['_id' => $id], 
            ['$pull' => ['likes' => $nick]]
        );
        
        return true;
    }

    public function actualizarNickDislikes($nick_pasado, $nick_nuevo, $id_receta) { 
        $id = new ObjectId($id_receta);

        $this->collection->updateOne(
            ['_id' => $id], 
            ['$pull' => ['dislikes' => $nick_pasado]]
        );
    
        $this->collection->updateOne(
            ['_id' => $id], 
            ['$push' => ['dislikes' => $nick_nuevo]]
        );
        
        return true;
    }

    public function eliminarNickDislikes($nick, $id_receta) { 
        $id = new ObjectId($id_receta);

        $this->collection->updateOne(
            ['_id' => $id], 
            ['$pull' => ['dislikes' => $nick]]
        );
        
        return true;
    }
    
    public function obtenerRecetasLikes($usuario) {
        $publicaciones = $this->collection->find(['likes' => $usuario]);
        return $publicaciones;
    }

    public function obtenerRecetasDislikes($usuario) {
        $publicaciones = $this->collection->find(['dislikes' => $usuario]);
        return $publicaciones;
    }

    public function editarComentario($id_publi, $id_com, $texto, $media, $esRespuesta) {
        $id = new ObjectId($id_publi);
        $comentarioId = new ObjectId($id_com);

        if ($esRespuesta) {

            $receta = $this->collection->findOne(['_id' => $id]);

            if ($receta) {
                   
                if($this->editarRespuesta($receta['comentarios'], $comentarioId, $texto, $media)){
                    return $this->collection->updateOne(['_id' => $id], ['$set' => ['comentarios' => $receta['comentarios']]]);
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
    
    

    public function eliminarReceta($id) {
        $Id = new ObjectId($id);

        return $this->collection->deleteOne(['_id' => $Id]);
    }

    public function ListaReceta() {
        return $this->collection->find([], ['sort' => ['created_at' => -1]]);
    }

    public function ListaRecetaUsuario($nick) {
        return $this->collection->find(['nick' => $nick], ['sort' => ['created_at' => -1]]);
    }

    public function ListaRecetaUsuarioEmail($email) {
        return $this->collection->find(['email' => $email], ['sort' => ['created_at' => -1]]);
    }

    function eliminarrecetascerrar($email){
        return $this->collection->deleteMany(['email' => $email]); 
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

    public function EditarReceta($titulo,$ingredientes,$preparacion,$dificultad,$tiempo, $id, $media) {
        
        $Id = new ObjectId($id);
        $filter = ['_id' => $Id];
        $update = [
            '$set' => [
                'multimedia' => $media,
                'titulo' => $titulo,
                'ingredientes' => $ingredientes,
                'preparacion' => $preparacion,
                'dificultad' => $dificultad,
                'tiempo' => $tiempo,
                'created_at' => date(DATE_ISO8601)
            ]
        ];
        return $this->collection->updateOne($filter, $update);
    }

    public function ListaRecetafavoritos($lista){
        
        $lista = iterator_to_array($lista);
    
        // Verificar que $lista no esté vacío
        if (empty($lista)) {
            return [];
        }
    
        try {
            // Convertir los IDs en ObjectId de MongoDB
            $listaObjectId = array_map(function ($id) {
                return new MongoDB\BSON\ObjectId($id);
            }, $lista);
    
            // Buscar publicaciones con _id en la lista
            $publicaciones = $this->collection->find([
                '_id' => ['$in' => $listaObjectId]
            ]);
    
            // Convertir el resultado a un array de PHP
            return iterator_to_array($publicaciones);
        } catch (Exception $e) {
            // Manejo de errores si ocurre una excepción
            error_log("Error en ListaPublicacionfavoritos: " . $e->getMessage());
            return [];
        }
        
    }
}
?>

