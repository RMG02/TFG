<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Usuario {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->selectCollection('usuarios');
    }

    public function registro($DatosUsuario) {
        $UsuarioExiste = $this->collection->findOne(['email' => $DatosUsuario['email']]);
        if ($UsuarioExiste) {
            if($UsuarioExiste['confirmado'] == true){
                return "Email ya registrado";
            }
            else{
                $this->collection->deleteOne(['email' => $DatosUsuario['email']]);
            }
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

    public function obtenerUsuarioToken($token) {
        $usuario = $this->collection->findOne(['recuperacion_token' => $token]);
        if ($usuario['token_tiempo'] < time()) {
            return null;
        }
        return $usuario;
    }

    public function obtenerUsuarioTokenconfirmacion($token) {
        $usuario = $this->collection->findOne(['confirmacion_token' => $token]);
        if ($usuario['tokenconfirmacion_tiempo'] < time()) {
            return null;
        }
        return $usuario;
    }

    public function cambiarContraseña($email, $con1, $con2) {
        $filter = ['email' => $email];
        
        if ($con1 == $con2) {
            $contraseña = password_hash($con1, PASSWORD_DEFAULT);
            $update = [
                '$set' => [
                    'password' => $contraseña
                ]
            ];
        
            $this->collection->updateOne($filter, $update);
            return true;
        }else{
            return false;
        }
        
    }
    
    

    public function enviarEnlace($email) {
        $token = bin2hex(random_bytes(32)); // Genera un token de 64 caracteres hexadecimales
        $tiempo = time() + 1200; // Expira en 20 minutos

        $this->collection->updateOne(
            ['email' => $email],
            ['$set' => ['recuperacion_token' => $token, 'token_tiempo' => $tiempo]]
        );

        $enlace = "http://localhost:8000/Vista/nueva_contraseña.php?token=$token"; 

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gastrored2@gmail.com'; 
            $mail->Password = 'uwmg kdcj gmjc tjqe';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('no-reply@gastrored2.com', 'GastroRed');
            $mail->addAddress($email); 
    
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Recuperación de contraseña - GastroRed';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px; background-color: #f4f4f4;'>
                    <div style='max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);'>
                        <h2 style='color: #333;'>Recuperación de contraseña</h2>
                        <p style='color: #555; font-size: 16px;'>Has solicitado restablecer tu contraseña. Para restablecerla haz clic en el botón de abajo:</p>
                        <a href='$enlace' 
                                style='display: inline-block; padding: 12px 20px; margin-top: 10px; font-size: 16px; 
                                color: white; background-color: #007bff; text-decoration: none; 
                                border-radius: 5px; font-weight: bold;'>
                                Restablecer contraseña
                        </a>
                        <p style='margin-top: 20px; font-size: 14px; color: #999;'>Si no solicitaste este cambio, ignora este correo.</p>
                    </div>
                </div>
            ";
    
            $mail->send();
            return true;
        } catch (Exception $e) {
            $sos = $mail->ErrorInfo;
            error_log("Error al enviar correo a: $email. Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public function enviarEnlaceConfirmacion($email) {
        $token = bin2hex(random_bytes(32)); // Genera un token de 64 caracteres hexadecimales
        $tiempo = time() + 1200; // Expira en 20 minutos

        $this->collection->updateOne(
            ['email' => $email],
            ['$set' => ['confirmacion_token' => $token, 'tokenconfirmacion_tiempo' => $tiempo]]
        );

        $enlace = "http://localhost:8000/Vista/confirmacion.php?tokenconf=$token"; 

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gastrored2@gmail.com'; 
            $mail->Password = 'uwmg kdcj gmjc tjqe';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('no-reply@gastrored2.com', 'GastroRed');
            $mail->addAddress($email); 
    
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Confirmación de cuenta - GastroRed';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px; background-color: #f4f4f4;'>
                    <div style='max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);'>
                        <h2 style='color: #333;'>Nueva cuenta</h2>
                        <p style='color: #555; font-size: 16px;'>Has creado una cuenta en GastroRed. Para confirmarla haz clic en el botón de abajo:</p>
                        <a href='$enlace' 
                                style='display: inline-block; padding: 12px 20px; margin-top: 10px; font-size: 16px; 
                                color: white; background-color: #007bff; text-decoration: none; 
                                border-radius: 5px; font-weight: bold;'>
                                Confirmar cuenta
                        </a>
                        <p style='margin-top: 20px; font-size: 14px; color: #999;'>Si no has creado ninguna cuenta, ignora este correo.</p>
                    </div>
                </div>
            ";
    
            $mail->send();
            return true;
        } catch (Exception $e) {
            $sos = $mail->ErrorInfo;
            error_log("Error al enviar correo a: $email. Error: {$mail->ErrorInfo}");
            return false;
        }
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

    public function cambiarconfirmacion($email) {
        $filter = ['email' => $email];
        $update = [
            '$set' => [
                'confirmado' => true
            ]
        ];
    
        return $this->collection->updateOne($filter, $update);

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

