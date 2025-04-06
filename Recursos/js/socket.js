var socket = io.connect("http://localhost:3000", { transports: ['websocket'], forceNew: true });

var Usuario = document.querySelector('meta[name="usuario"]').getAttribute('content');
var contadorActual = localStorage.getItem('notificationCounter');

socket.emit("conectado", {usuario: Usuario, num_noti: contadorActual});


socket.on("actualizar-contador", (data) => {

    var notificationCounter = document.getElementById('notification-counter');
    var contadorActual = data;
    notificationCounter.textContent = contadorActual;
    notificationCounter.style.display = contadorActual > 0 ? 'inline' : 'none';
    localStorage.setItem('notificationCounter', contadorActual);

});




socket.on("Eliminar-nick", function(data) {
    fetch('../../Controlador/Usuario_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'EliminarNick': true,
            'nick' : data.nick,
            'admin' : data.admin
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

    fetch('../../Controlador/Conversaciones_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'EliminarNick': true,
            'nick' : data.nick,
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

    fetch('../../Controlador/Foros_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'EliminarNick': true,
            'nick' : data.nick,
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

    fetch('../../Controlador/Publicacion_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'EliminarNick': true,
            'nick' : data.nick,
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

    fetch('../../Controlador/Receta_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'EliminarNick': true,
            'nick' : data.nick,
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

    fetch('../../Controlador/Notificacion_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'eliminarNotificacionNick': true,
            'nick' : data.nick,
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });



});


socket.on("actualizar-cambioNick", function(data) {
    fetch('../../Controlador/Usuario_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'cambioNick': true,
            'nuevoNick' : data.nuevoNick,
            'nick_pasado' : data.nick,
            'admin' : data.admin
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

    fetch('../../Controlador/Conversaciones_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'cambioNick': true,
            'nuevoNick' : data.nuevoNick,
            'nick_pasado' : data.nick,
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

    fetch('../../Controlador/Foros_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'cambioNick': true,
            'nuevoNick' : data.nuevoNick,
            'nick_pasado' : data.nick,
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

    fetch('../../Controlador/Publicacion_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'cambioNick': true,
            'nuevoNick' : data.nuevoNick,
            'nick_pasado' : data.nick,
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

    fetch('../../Controlador/Receta_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'cambioNick': true,
            'nuevoNick' : data.nuevoNick,
            'nick_pasado' : data.nick,
        })
    })
    .then(response => response.json())
    .then(respuesta => {
        if (respuesta.status === 'success') {
            console.log('Actualizado');
        } else {
            console.error('Error al actualizar', respuesta.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });


});

socket.on("notificacion", function(data) {

    fetch('../../Controlador/Notificacion_controlador.php?get_session_vars=true')
    .then(response => response.json())
    .then(preferencias => {
        window.notilikes = preferencias.notilikes;
        window.noticomentarios = preferencias.noticomentarios;
        window.notiseguidores = preferencias.notiseguidores;
        window.notimensajes = preferencias.notimensajes;

        // Ahora que tenemos las preferencias, evaluamos si mostrar la notificación
        var mostrarNotificacion = false;

        if ((data.tipo === "follow" || data.tipo === "unfollow") && window.notiseguidores) {
            mostrarNotificacion = true;
        } else if (data.tipo === "nuevo comentario" && window.noticomentarios) {
            mostrarNotificacion = true;
        } else if ((data.tipo === "like" || data.tipo === "dislike") && window.notilikes) {
            mostrarNotificacion = true;
        } else if (data.tipo === "mensaje" && window.notimensajes) {
            mostrarNotificacion = true;
        } else if(data.tipo === "foro"){
            mostrarNotificacion = true;
        }

        if (mostrarNotificacion) {
            
            var divNotificaciones = document.getElementById("notificaciones");

            var notificacion = document.createElement("div");
            notificacion.className = "notificacion";
            notificacion.innerHTML = "<strong>" + data.mensaje + "</strong>";

            divNotificaciones.appendChild(notificacion);

            actualizarContadorNotificaciones(1);

            // Hacer una llamada al controlador de notificaciones para hacer unset de la variable de sesión
            fetch('../../Controlador/Notificacion_controlador.php', {
                method: 'POST',
                body: new URLSearchParams({
                    'accion': 'unset'
                })
            })
            .then(response => response.json())
            .then(respuesta => {
                if (respuesta.status === 'success') {
                    console.log('Variable de sesión eliminada correctamente');
                } else {
                    console.error('Error al eliminar la variable de sesión:', respuesta.message);
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
            });

            setTimeout(function() {
                notificacion.classList.add("hidden");
                setTimeout(function() {
                    divNotificaciones.removeChild(notificacion);
                }, 300);
            }, 10000);
        }
    })
    .catch(error => {
        console.error("Error al obtener variables de sesión:", error);
    });
    
});

socket.on("mostrar-mensaje-per", (data, id) => {

    const chatContainer = document.getElementById("chat-cont"); 

    const mensajeDiv = document.getElementById("mensajeEnviado-");
    mensajeDiv.id = "mensajeEnviado-" + id;
    mensajeDiv.setAttribute("onclick", `mostrarOpciones('${id}')`);

    const parrafoP = document.getElementById("contenido-");
    parrafoP.id = "contenido-" + id;

    const modalDiv = document.createElement("div");
    modalDiv.classList.add("modal_men"); 
    modalDiv.id = "mensaje-" + id;
 
    modalDiv.innerHTML = `
            <div class="modal_men-content">
                <span class="close_men" onclick="cerrarModal('mensaje-${id}')">&times;</span>  
                <button type="button" class="mod-men" onclick="eliminarMensaje('${id}', '${data.usuario_receptor}')">Eliminar mensaje</button>
                <button type="button" class="mod-men" onclick="mostrarEditar('${id}')">Editar mensaje</button>
                <div id="edit-${id}" class="modal_men">
                    <div class="modal_men-content">
                        <span class="close_men" onclick="cerrarModal('edit-${id}')">&times;</span>
                        <p>Modifica tu mensaje</p>
                        <input type="text" id="nuevoContenido-${id}" value="${data.contenido}" class="input-mensaje"> 
                        <button type="button" class="mod-men" onclick="editarMensaje('${id}', '${data.usuario_receptor}', document.getElementById('nuevoContenido-${id}').value); cerrarModal('edit-${id}'); cerrarModal('mensaje-${id}')">Editar</button>
                    </div>
                </div>
            </div>
    `;

    chatContainer.appendChild(modalDiv);

    chatContainer.scrollTop = chatContainer.scrollHeight;

    
});


socket.on("mostrar-mensaje", (data) => {

    const chatContainer = document.getElementById("chat-cont");  

    const mensajeDiv = document.createElement("div");
    mensajeDiv.id = "mensajeRecibido-";
    mensajeDiv.classList.add("mensaje_recibido");  

    mensajeDiv.innerHTML = `
        <p class="nombre-usuario"><strong>${data.usuario_emisor}</strong></p>
        <p id="contenido--">${data.contenido}</p>
        <p><small>${new Date(data.hora).toLocaleString()}</small></p>  
    `;

    var hijos = chatContainer.children;
    var tieneMensajes = true;
    for (var i = 0; i < hijos.length; i++) {
        if (hijos[i].id == "mensajeVacio") {
            tieneMensajes = false;
            break;
        }
    }
    if (!tieneMensajes) {
        var mensajeVacio = document.getElementById("mensajeVacio")
        chatContainer.removeChild(mensajeVacio);
    }
    
    chatContainer.appendChild(mensajeDiv);

    chatContainer.scrollTop = chatContainer.scrollHeight;

    
});

socket.on("mostrar-mensaje-modal", (data) => {

    const chatContainer = document.getElementById("chat-cont"); 
    const mensajeDiv = document.getElementById("mensajeRecibido-");
    mensajeDiv.id = "mensajeRecibido-" + data;
    const parrafoP = document.getElementById("contenido--");
    parrafoP.id = "contenido-" + data;

    chatContainer.scrollTop = chatContainer.scrollHeight;

    
});


socket.on("elim-mensaje", (data) => {
    var chatContainer = document.getElementById("chat-cont");  
    var mensajeDiv = document.getElementById("mensajeRecibido-" + data);
    if (mensajeDiv) {
        chatContainer.removeChild(mensajeDiv);
    } 
    var hijos = chatContainer.children;
    
    if (hijos.length == 0) {
        var mensajeVacio = document.createElement("h2");
        mensajeVacio.id = "mensajeVacio";
        mensajeVacio.style.color = "white";
        mensajeVacio.textContent = "No hay mensajes";
        chatContainer.appendChild(mensajeVacio);
    }
    
});


socket.on("edit-mensaje", (data) => {

    var mensajeDiv = document.getElementById("contenido-" + data.mensaje_id);
    if (mensajeDiv) {
        mensajeDiv.innerHTML = `${data.contenido}`;
    }
    
});



socket.on("actualizar_usuario", function(data) {
    // Hacer una llamada al controlador de notificaciones para hacer unset de la variable de sesión
    fetch('../../Controlador/Usuario_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'usuariopropio': 'unset'
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.status === 'success') {
            console.log('Variable de sesión eliminada correctamente');
        } else {
            console.error('Error al eliminar la variable de sesión:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
});


socket.on("unset_noti", function(data) {
    // Hacer una llamada al controlador de notificaciones para hacer unset de la variable de sesión
    fetch('../../Controlador/Notificacion_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'accion': 'unset'
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.status === 'success') {
            console.log('Variable de sesión eliminada correctamente');
        } else {
            console.error('Error al eliminar la variable de sesión:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
});


socket.on("decremento", function(data) {
    actualizarContadorNotificacionesDecremento(data);

    // Hacer una llamada al controlador de notificaciones para hacer unset de la variable de sesión
    fetch('../../Controlador/Notificacion_controlador.php', {
        method: 'POST',
        body: new URLSearchParams({
            'accion': 'unset'
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.status === 'success') {
            console.log('Variable de sesión eliminada correctamente');
        } else {
            console.error('Error al eliminar la variable de sesión:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
});

function LogOut(){
    socket.emit("desconectado", {usuario: Usuario, num_noti: localStorage.getItem('notificationCounter')});
}
function actualizarContadorNotificacionesDecremento(decremento) {
    var notificationCounter = document.getElementById('notification-counter');
    var contadorActual = parseInt(notificationCounter.textContent) || 0;
    contadorActual -= decremento;
    if(contadorActual >= 0){
        notificationCounter.textContent = contadorActual;
        notificationCounter.style.display = contadorActual > 0 ? 'inline' : 'none';
        localStorage.setItem('notificationCounter', contadorActual);
    }
    
}


function actualizarContadorNotificaciones(incremento) {

    var notificationCounter = document.getElementById('notification-counter');
    var contadorActual = parseInt(notificationCounter.textContent) || 0;
    contadorActual += incremento;
    notificationCounter.textContent = contadorActual;
    notificationCounter.style.display = contadorActual > 0 ? 'inline' : 'none';
    localStorage.setItem('notificationCounter', contadorActual);
}

function enviarDatos(event, usuario, usuario_des, id_publi, likes, dislikes, tipo_publicacion, id_com, respuesta) {
    fetch('../../Controlador/Publicacion_controlador.php', {
    }).then(response => response.text())
      .then(result => {

        if (event.submitter.name === 'darlike') {
            // Comprobar si el usuario ya está en el array de likes
            
            if (!likes.includes(usuario)) {
                if(dislikes.includes(usuario)){
                    socket.emit("decrementar-reaccion", {usuario: usuario_des});
                }
                darLike(usuario, usuario_des, id_publi, tipo_publicacion);
            } else {
                socket.emit("decrementar-reaccion", {usuario: usuario_des});
            }
        } else if (event.submitter.name === 'dardislike') {
            // Comprobar si el usuario ya está en el array de dislikes
            if (!dislikes.includes(usuario)) {
                if(likes.includes(usuario)){
                    socket.emit("decrementar-reaccion", {usuario: usuario_des});
                }
                darDislike(usuario, usuario_des, id_publi, tipo_publicacion);
            } else {
                socket.emit("decrementar-reaccion", {usuario: usuario_des});
            }
        } else if(event.submitter.name === 'agregarComentario'){
            socket.emit("nuevo-comentario", { usuario: usuario, usuario_des: usuario_des, id_publi: id_publi, tipo: "nuevo comentario", tipo_publicacion: tipo_publicacion, id_com: id_com, respuesta:respuesta });
        }
          
      }).catch(error => {
          console.error('Error al enviar datos:', error);
      });
}

function enviarCambioNick(event, nick_act) {
    fetch('../../Controlador/Usuario_controlador.php', {
    }).then(response => response.text())
      .then(result => {

        var nuevoNick = document.getElementById('nick').value;
        if(nuevoNick !== ""){
            if(nuevoNick !== nick_act){
                socket.emit("cambioNick", { nick: nick_act, nuevoNick: nuevoNick, admin:false});
            }
        }
        
          
      }).catch(error => {
          console.error('Error al enviar datos:', error);
      });
}

function enviarCambioNickAdmin(event, nick_act) {
    fetch('../../Controlador/Admin_controlador.php', {
    }).then(response => response.text())
      .then(result => {

        var nuevoNick = document.getElementById('nick_nuevo').value;
        if(nuevoNick !== ""){
            if(nuevoNick !== nick_act){
                socket.emit("cambioNick", { nick: nick_act, nuevoNick: nuevoNick, admin:true});
            }
        }
        
          
      }).catch(error => {
          console.error('Error al enviar datos:', error);
      });
}

function eliminarCuenta(event) {
    fetch('../../Controlador/Usuario_controlador.php', {
    }).then(response => response.text())
      .then(result => {

        var nick = document.getElementById('nick_borrado').value;
        
        socket.emit("EliminarNick", { nick: nick, admin:false});
          
      }).catch(error => {
          console.error('Error al enviar datos:', error);
      });
}

function eliminarCuentaAdmin(event) {
    fetch('../../Controlador/Admin_controlador.php', {
    }).then(response => response.text())
      .then(result => {

        var nick = document.getElementById('nick_borrado').value;
        
        socket.emit("EliminarNick", { nick: nick, admin:true});
      
      }).catch(error => {
          console.error('Error al enviar datos:', error);
      });
}


function enviarSeguidor(usuario_actual, usuario_dest, tipo) {
    if(tipo == "Seguir"){
        socket.emit("follow", { usuario_actual: usuario_actual, usuario_dest: usuario_dest});
    }else if(tipo == "Dejar de Seguir"){
        socket.emit("unfollow", { usuario_actual: usuario_actual, usuario_dest: usuario_dest});
    }

}

function darLike(usuario, usuario_des, id_publi, tipo_publicacion) {
    socket.emit("like-dado", { usuario: usuario, usuario_des: usuario_des, id_publi: id_publi, tipo: "like", tipo_publicacion: tipo_publicacion });
}

function darDislike(usuario, usuario_des, id_publi, tipo_publicacion) {
    socket.emit("dislike-dado", { usuario: usuario, usuario_des: usuario_des, id_publi: id_publi, tipo: "dislike", tipo_publicacion: tipo_publicacion});
}

function unset(){
    socket.emit("unset", {});

}

function ComentarioEliminado(usuario){
    unset();
    socket.emit("decrementar-reaccion", {usuario: usuario});

}

function enviarMensaje(usuario_actual, usuario_dest, chatId, mensaje, compartir) {
    if (mensaje.trim() === '') {
        alert('El mensaje no puede estar vacío');
        return;
    }

    const chatContainer = document.getElementById("chat-cont");  
    const mensajeDiv = document.createElement("div");
    mensajeDiv.classList.add("mensaje_enviado");  
    mensajeDiv.id = "mensajeEnviado-";
    mensajeDiv.innerHTML = `
        <p class="nombre-usuario"><strong>Tú</strong></p>
        <p id="contenido-">${mensaje}</p>
        <p><small>${new Date(new Date().toISOString()).toLocaleString()}</small></p> 
    `;

    var hijos = chatContainer.children;
    var tieneMensajes = true;
    for (var i = 0; i < hijos.length; i++) {
        if (hijos[i].id == "mensajeVacio") {
            tieneMensajes = false;
            break;
        }
    }
    if (!tieneMensajes) {
        var mensajeVacio = document.getElementById("mensajeVacio")
        chatContainer.removeChild(mensajeVacio);
    }

    chatContainer.appendChild(mensajeDiv);

    chatContainer.scrollTop = chatContainer.scrollHeight;

    if(!compartir){
        document.getElementById('contenido').value = '';
    }


    socket.emit("nuevo-mensaje", {usuario_actual: usuario_actual, usuario_dest: usuario_dest, chatId: chatId, mensaje: mensaje});

}


function enviarPublicacionForo(idForo, notificaciones, usuarioEmisor, titulo) {
    console.log("Función enviarPublicacionForo llamada con los siguientes parámetros:");
    console.log("idForo:", idForo);
    console.log("notificaciones:", notificaciones);
    console.log("usuarioEmisor:", usuarioEmisor);
    console.log("titulo:", titulo);
    socket.emit("nueva-publi-foro", {usuarioEmisor: usuarioEmisor, usuarios_noti: notificaciones, foroId: idForo, titulo: titulo});
}




function NuevoComentario(event, nickuser, nick, id_publi, tipo_publicacion, respuesta) {

    event.preventDefault();
    var datosForm = new FormData(event.target);
    datosForm.append('agregarComentario', 'true');
    var url = (tipo_publicacion == "receta") ? '../../Controlador/Receta_controlador.php' : '../../Controlador/Publicacion_controlador.php';
    fetch(url, {
        method: 'POST',
        body: datosForm
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            var id_comentario = data.id_comentario;
            if(respuesta){
                enviarDatos(event, nickuser, nick, id_publi, '', '', tipo_publicacion, id_comentario, respuesta);
            }
            else{
                enviarDatos(event, nickuser, nick, id_publi, '', '', tipo_publicacion, id_comentario, '');
            }
            window.location.href = data.redirect_url;
        } else {
            console.error('Error al agregar comentario:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });

}
    

function eliminarMensaje(mensajeId, usuario) {
    var chatContainer = document.getElementById("chat-cont");  
    var mensajeDiv = document.getElementById("mensajeEnviado-" + mensajeId);
    var modalDiv = document.getElementById("mensaje-" + mensajeId);

    if (mensajeDiv) {
        mensajeDiv.parentNode.removeChild(mensajeDiv);
    } 
    if (modalDiv) {
        modalDiv.parentNode.removeChild(modalDiv);
    }

    var hijos = chatContainer.children;
    
    if (hijos.length == 0) {
        var mensajeVacio = document.createElement("h2");
        mensajeVacio.id = "mensajeVacio";
        mensajeVacio.style.color = "white";
        mensajeVacio.textContent = "No hay mensajes";
        chatContainer.appendChild(mensajeVacio);
    }

    socket.emit("eliminar-mensaje", { mensajeId: mensajeId, usuario: usuario});

}

function editarMensaje(mensajeId, usuario, contenido) {

    var mensajeDiv = document.getElementById("contenido-" + mensajeId);

    if (mensajeDiv) {
        mensajeDiv.innerHTML = `${contenido}`;
    }
    
    socket.emit("editar-mensaje", { mensajeId: mensajeId, usuario: usuario, contenido: contenido});

}