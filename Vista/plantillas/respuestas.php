<?php
function mostrarRespuestas($comentarios, $modalComId, $modalResId) {
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
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $multi_com = "<img src='../Recursos/multimedia/$mult' alt='Imagen de la publicación'>";
            } elseif (in_array($extension, ['mp4', 'webm'])) {
                $multi_com = "<video controls><source src='../Recursos/multimedia/$mult' type='video/$extension'></video>";
            }
        } else {
            $multi_com = '';
        }

        $contenido .= <<<EOS
                        <div class="comentario" name="comentario_rec">
                            $multi_com
                            <strong>$usuario:</strong>
                            <span>$tex</span>
                            <span class="comentario-time">$fecha</span>
                            <div class="comentarios-icon">
                                <i class="fa fa-comments"></i> $num_respuestas
                            </div>
                        </div>
                        <div id="comentario-$modalComId-$modalResId" class="modal_publi">
                            <div class="modal_publi-content">
                                <span class="close_publi">&times;</span>
                                <div class="comentario">
                                    $multi_com
                                    <strong>$usuario:</strong>
                                    <br>
                                    <span>$tex</span>
                                    <span class="comentario-time">$fecha</span>
                                    <hr>
                                    <h3>Respuestas</h3>
                                    
        EOS;
        $contenido .= <<<EOS
                    </div>
                </div>
            </div>
        EOS;
        if (!empty($comentario['respuestas'])) {

            //$contenido .= mostrarRespuestas($comentario['respuestas'], $modalResId, 0);
        }

        $modalResId++;
        /*$contenido .= <<<EOS
            </div>
            <div id="respuesta-$modalResId" class="modal_publi" style="display:none;">
                <div class="modal_publi-content">
                    <span class="close_publi">&times;</span>
                    <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario">
                        <textarea name="texto" placeholder="Escribe tu respuesta..."></textarea>
                        <input type="hidden" name="id_com" value="$id_com">
                        <input type="hidden" name="esRespuesta" value="true">
                        <button type="submit" class="botonPubli" name="agregarComentario">Añadir Respuesta</button>
                    </form>
                </div>
            </div>
        </div>
        EOS;

        $modalResId++;
        
        if ($usuario == $_SESSION['nick']) {
            $contenido .= <<<EOS
                <button type="button" class="botonPubli" name="editar_com">Editar comentario</button>
                <div id="editCom-$modalComId" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <form method="POST" enctype="multipart/form-data" action="../Controlador/Publicacion_controlador.php" class="formulario">
                            <textarea name="contenido">$tex</textarea>
                            <input type="hidden" name="archivo_origen" value="$mult"> 
                            <input type="file" name="nuevo_archivo"> 
                            <input type="hidden" name="id_comen" value="$id_com"> 
                            <input type="hidden" name="principal" value="true">
                            <input type="hidden" name="id_publi" value="$id_com">
                            <button type="submit" class="botonPubli" name="editarComentario">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            EOS;
        }

        $contenido .= <<<EOS
        </div>
        EOS;*/
    }

    return $contenido;
}
?>
