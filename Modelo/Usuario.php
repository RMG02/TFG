<?php
class Usuario {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('usuarios');
    }

    public function registro($DatosUsuario) {
        $UsuarioExiste = $this->collection->findOne(['email' => $DatosUsuario['email']]);
        if ($UsuarioExiste) {
            return "Email ya registrado";
        }
        $UsuarioExiste = $this->collection->findOne(['nick' => $DatosUsuario['nick']]);
        if ($UsuarioExiste) {
            return "Nick ya registrado";
        }
        $DatosUsuario['password'] = password_hash($DatosUsuario['password'], PASSWORD_DEFAULT);
        return $this->collection->insertOne($DatosUsuario);
    }

    public function login($email, $password) {
        $usuario = $this->collection->findOne(['email' => $email]);
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return null;
    }

    public function darBajaUsuario($email) {
        return $this->collection->deleteOne(['email' => $email]);
    }

    public function obtenerUsuario($email) {
        $usuario = $this->collection->findOne(['email' => $email]);
        if ($usuario) {
            return $usuario;
        }
        return null;
    }

    public function favoritospublicacion($publicacion,$nick){
            // Buscar al usuario en la base de datos
            $usuario = $this->collection->findOne(['nick' => $nick]);
            if ($usuario) {
                // Verificar si la publicación ya está en favoritos
                $favoritos = isset($usuario['favoritospubli']) ? iterator_to_array($usuario['favoritospubli']) : [];
                if (in_array($publicacion, $favoritos)) {
                    // Si ya está en favoritos, eliminarla con $pull
                    $this->collection->updateOne(
                        ['nick' => $nick],
                        ['$pull' => ['favoritospubli' => $publicacion]]
                    );
                    return true;
                } else {
                    $this->collection->updateOne(
                        ['nick' => $nick],
                        ['$addToSet' => ['favoritospubli' => $publicacion]]
                    );
                    return true;
                }
            } else {
                return false;
            }
    }

    public function favoritosreceta($publicacion,$nick){
        // Buscar al usuario en la base de datos
        $usuario = $this->collection->findOne(['nick' => $nick]);
            if ($usuario) {
                // Verificar si la publicación ya está en favoritos
                $favoritos = isset($usuario['favoritosreceta']) ? iterator_to_array($usuario['favoritosreceta']) : [];
                if (in_array($publicacion, $favoritos)) {
                    // Si ya está en favoritos, eliminarla con $pull
                    $this->collection->updateOne(
                        ['nick' => $nick],
                        ['$pull' => ['favoritosreceta' => $publicacion]]
                    );
                    return true;
                } else {
                    $this->collection->updateOne(
                        ['nick' => $nick],
                        ['$addToSet' => ['favoritosreceta' => $publicacion]]
                    );
                    return true;
                }
            } else {
                return false;
            }
    }

    public function buscarusuario($nick) {
        $filter = ['nombre' => ['$regex' => $nick, '$options' => 'i']];
        $result = $this->collection->find($filter);
        return $result;
    }

    public function obtenerUsuarioNick($nick) {
        $usuario = $this->collection->findOne(['nick' => $nick]);
        if ($usuario) {
            return $usuario;
        }
        return null;
    }

    public function confirmar($password,$email) {
        $usuario = $this->collection->findOne(['email' => $email]);
        if (!password_verify($password, $usuario['password'])) {
            return false;
        }
        return true;
    }

    public function actualizarNick($nick_pasado, $nick_nuevo, $usuario_email, $cambiarSeguidores, $cambiarSiguiendo) {
        
        if ($cambiarSeguidores) {
            $this->collection->updateOne(
                ['email' => $usuario_email], 
                ['$pull' => ['seguidores' => $nick_pasado]]
            );
    
            $this->collection->updateOne(
                ['email' => $usuario_email], 
                ['$push' => ['seguidores' => $nick_nuevo]]
            );
        }
    
        if ($cambiarSiguiendo) {
            $this->collection->updateOne(
                ['email' => $usuario_email], 
                ['$pull' => ['siguiendo' => $nick_pasado]]
            );
    
            $this->collection->updateOne(
                ['email' => $usuario_email], 
                ['$push' => ['siguiendo' => $nick_nuevo]]
            );
        }
        
    
        return true;
    }
    
    

    public function editarUsuario($email, $datos, $nick) {
        $filter = ['email' => $email];
        $nuevoEmail = $datos['email'];
        $nuevoNick = $datos['nick'];

        $usuarioExistente = $this->collection->findOne(['email' => $nuevoEmail]);
        if ($usuarioExistente && $nuevoEmail != $email) {
            return "Email ya registrado";
        }

        $usuarioExistente = $this->collection->findOne(['nick' => $nuevoNick]);
        if($usuarioExistente && $nuevoNick != $nick){
            return "Nick ya registrado";
        }
        
        $passs = $this->collection->findOne(['email' => $email]);
        if ($datos['password'] != $passs['password']) {
            $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }else{
            $datos['password'] = $passs['password'];
        }
    
        $update = [
            '$set' => [
                'nombre' => $datos['nombre'],
                'nick' => $datos['nick'],
                'email' => $datos['email'],
                'password' => $datos['password'],
                'admin' => $datos['admin']
            ]
        ];
    
        return $this->collection->updateOne($filter, $update);
    }
    
    public function ListaUsuarios() {
        return $this->collection->find();
    }

    public function listapublicacionfavoritos($nick){
        $usuario = $this->collection->findOne(['nick' => $nick]);
        return $usuario['favoritospubli'];
    }

    public function listarecetafavoritos($nick){
        $usuario = $this->collection->findOne(['nick' => $nick]);
        return $usuario['favoritosreceta'];
    }

    public function noSeguir($nickPropio,$nickSeguir){
        $filter = ['nick' => $nickSeguir];
        $update =
                [
                    '$pull' => ['seguidores' => $nickPropio] // Asegurarse de quitar el seguidor si existía
                ];
         
        $filter2 = ['nick' => $nickPropio];
        $update2 =
                [
                    '$pull' => ['siguiendo' => $nickSeguir] // Asegurarse de quitar el seguidor si existía
                ];
        
        return $this->collection->updateOne($filter, $update) && $this->collection->updateOne($filter2, $update2);
    }

    public function preferencias($type, $status, $email) {
        
        $filter = ['email' => $email];
    
        
        $update = [
            '$set' => [$type => $status] 
        ];
    
        return $this->collection->updateOne($filter, $update);
    }


    public function Seguir($nickPropio,$nickSeguir){
        $filter = ['nick' => $nickSeguir];
        $update =
                [
                    '$push' => ['seguidores' => $nickPropio] 
                ];
         
        $filter2 = ['nick' => $nickPropio];
        $update2 =
                [
                    '$push' => ['siguiendo' => $nickSeguir] 
                ];
        
        return $this->collection->updateOne($filter, $update) && $this->collection->updateOne($filter2, $update2);
    }
    
}

