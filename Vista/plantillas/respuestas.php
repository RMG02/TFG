<?php
function mostrarRespuestas($comentarios, $modalComId, &$modalResId, $id_publi, $tipo_publicacion, $id_comentario_anterior, $usuario_anterior) {
    $contenido = "";
    foreach ($comentarios as $comentario) {
        $usuario = $comentario['usuario'];
        $id_com = $comentario['id_comentario']['$oid'];
        $tex = $comentario['texto'];
        $mult = $comentario['multimedia'] ?? '';
        $fecha = date('d/m/Y H:i:s', strtotime($comentario['fecha']));
        $num_respuestas = count($comentario['respuestas'] ?? []);

        if ($mult) {
            $extension = pathinfo($mult, PATHINFO_EXTENSION);
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $multi_com = "<img src='../Recursos/multimedia/$mult' alt='Imagen de la publicación' class='imagen-respuesta_rec'>";
                $multi_editar = "<img src='../Recursos/multimedia/$mult' alt='Imagen de la publicación' class='imagen-editar'>";
            } 
        } else {
            $multi_com = '';
            $multi_editar = '';
        }

        $contenido .= <<<EOS

                        <div class="comentario" name="comentario_rec">
                            $multi_com
                            <a href="../Vista/unsetPerfilPublico.php?nick_user=$usuario" class="nick-link" onclick="event.stopPropagation();"><strong>$usuario:</strong></a>
                            <span>$tex</span>
                            <span class="comentario-time">$fecha</span>
                            <div class="comentarios-icon">
                                <i class="fa fa-comments"></i> $num_respuestas
                            </div>
                        </div>
                        <div id="comentario-$modalComId-$modalResId" class="modal_publi">
                            <div class="modal_publi-content">
                                <span class="close_publi">&times;</span>
                                <div class="tweet-header">
                                    <a href="../Vista/unsetPerfilPublico.php?nick_user=$usuario" class="nick-link"><strong>$usuario</strong></a>
                                    <span class="tweet-time">$fecha</span>
                                </div>
                                <div class="comentario_mod">
                                    $multi_com
                                    <span>$tex</span>
                                    <br>
                                    
        EOS;
        if($tipo_publicacion == "publicacion"){
            if($usuario != $_SESSION['nick']){
                $contenido .= <<<EOS
                    <button type="button" class="botonPubli" name="responder_rec">Responder</button>
                    <div id="respuesta-$modalComId-$modalResId" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario" onsubmit="NuevoComentario(event, '{$_SESSION['nick']}','$usuario', '$id_publi', '$tipo_publicacion', 'true')">
                                <input type="hidden" name="id_publi" value="$id_publi">
                                <input type="hidden" name="id_comen" value="$id_com"> 
                                <input type="hidden" name="esRespuesta" value="true">
                                <input type="hidden" name="usuario_origen" value="$usuario"> 
                                <textarea name="texto" placeholder="Escribe un comentario..."></textarea>
                                <input type="file" name="archivo"> 
                                <button type="submit" class="botonPubli" name="agregarComentario">Añadir Respuesta</button>
                            </form>
                        </div>
                    </div>
                EOS;
            }
            
        }else{
            if($usuario != $_SESSION['nick']){
                $contenido .= <<<EOS
                    <button type="button" class="botonPubli" name="responder_rec">Responder</button>
                    <div id="respuesta-$modalComId-$modalResId" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <form method="POST" enctype="multipart/form-data" action="../Controlador/Receta_controlador.php" class="formulario" onsubmit="NuevoComentario(event, '{$_SESSION['nick']}','$usuario', '$id_publi', '$tipo_publicacion', 'true')">
                                <input type="hidden" name="id_publi" value="$id_publi">
                                <input type="hidden" name="id_comen" value="$id_com"> 
                                <input type="hidden" name="esRespuesta" value="true">
                                <input type="hidden" name="usuario_origen" value="$usuario"> 
                                <textarea name="texto" placeholder="Escribe un comentario..."></textarea>
                                <input type="file" name="archivo"> 
                                <button type="submit" class="botonPubli" name="agregarComentario">Añadir Respuesta</button>
                            </form>
                        </div>
                    </div>
                EOS;
            }
            
        }
        $contenido .= <<<EOS
                        
            <div class="tweet-footer">                       
        EOS;

        if($usuario == $_SESSION['nick'] || $_SESSION['admin'] == true){
            $contenido .= <<<EOS
                <div class="dropdown">
                    <button class="dropbtn">⋮</button>
                    <div class="dropdown-content">
            EOS;
            if($tipo_publicacion == "publicacion"){
                $contenido .= <<<EOS
                    <form method="POST" action="../Controlador/Publicacion_controlador.php" class="formulario" onsubmit="ComentarioEliminado('$usuario_anterior')">
                EOS;
            }else{
                $contenido .= <<<EOS
                    <form method="POST" action="../Controlador/Receta_controlador.php" class="formulario" onsubmit="ComentarioEliminado('$usuario_anterior')">
                EOS;
            }
            $contenido .= <<<EOS
                            <input type="hidden" name="multi" value="../Recursos/multimedia/$mult"> 
                            <input type="hidden" name="esRespuesta" value="true">
                            <input type="hidden" name="id_publi" value="$id_publi">
                            <input type="hidden" name="id_comentario_origen" value="$id_comentario_anterior">
                            <input type="hidden" name="id_comen" value="$id_com"> 
                            <button type="submit" class="botonPubli" name="eliminarComentario">Eliminar comentario</button>
                        </form>
                        <button type="button" class="botonPubli" name="editar_com_rec">Editar comentario</button>                  
                    </div>
                </div>
                <div id="editCom-$modalComId-$modalResId" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        $multi_editar
            EOS;
            if($tipo_publicacion == "publicacion"){
                $contenido .= <<<EOS
                    <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario">
                EOS;
            }else{
                $contenido .= <<<EOS
                    <form method="POST" enctype="multipart/form-data" action="../Controlador/Receta_controlador.php" class="formulario">
                EOS;
            }
            $contenido .= <<<EOS
                            <textarea name="contenido">$tex</textarea>
                            <input type="hidden" name="archivo_origen" value="$mult"> 
                            <input type="file" name="nuevo_archivo"> 
                            <input type="hidden" name="id_comen" value="$id_com"> 
                            <input type="hidden" name="esRespuesta" value="true">
                            <input type="hidden" name="id_publi" value="$id_publi">
                            <button type="submit" class="botonPubli" name="editarComentario">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            EOS;
        }

        $contenido .= <<<EOS
                        
                        </div>
                        <hr>
        EOS;
        
        if (!empty($comentario['respuestas'])) {
            $usuario_anterior = $usuario;
            $id_comentario_anterior = $id_com;
            $contenido .= '<h3>Respuestas</h3>';
            $modalResId++;    
            $contenido .= mostrarRespuestas($comentario['respuestas'], $modalComId, $modalResId, $id_publi, $tipo_publicacion, $id_comentario_anterior, $usuario_anterior);        
        }
        else{
            $contenido .= '<h3>No hay respuestas</h3>';
            $modalResId++; 
           
        }
        $contenido.= <<<EOS
        </div>
    </div>
</div>
EOS;
    }
    
    return $contenido;
}
?>
