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

    public function confirmar($password,$email) {
        $usuario = $this->collection->findOne(['email' => $email]);
        if (!password_verify($password, $usuario['password'])) {
            return false;
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
    
}

