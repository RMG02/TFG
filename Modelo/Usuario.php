<?php
class Usuario {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('usuarios');
    }

    public function registro($DatosUsuario) {
        $UsuarioExiste = $this->collection->findOne(['email' => $DatosUsuario['email']]);
        if ($UsuarioExiste) {
            return "Email ya registrado.";
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

    public function modificarUsuario($email, $nuevosDatos) {
        if (isset($nuevosDatos['password'])) {
            $nuevosDatos['password'] = password_hash($nuevosDatos['password'], PASSWORD_DEFAULT);
        }
        return $this->collection->updateOne(['email' => $email],['$set' => $nuevosDatos]);
    }

    public function editarUsuario($email,$admin, $datos) {
        $filter = ['email' => $email];
        if (isset($datos['password'])) {
            $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }
        $update = [
            '$set' => [
                'nombre' => $datos['nombre'],
                'nick' => $datos['nick'],
                'email' => $datos['email'],
                'password' => $datos['password'],
                'admin' => $admin
            ]
        ];

        $result = $this->collection->updateOne($filter, $update);

        return $result->getModifiedCount() > 0;
    }
    

}

