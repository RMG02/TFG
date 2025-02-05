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



socket.on("notificacion", function(data) {

    fetch('../../Controlador/Notificacion_controlador.php?get_session_vars=true')
    .then(response => response.json())
    .then(preferencias => {
        window.notilikes = preferencias.notilikes;
        window.noticomentarios = preferencias.noticomentarios;
        window.notiseguidores = preferencias.notiseguidores;
        window.notimensajes = preferencias.notimensajes;

        console.log("Configuración de notificaciones actualizada:");
        console.log("Likes:", window.notilikes);
        console.log("Comentarios:", window.noticomentarios);
        console.log("Seguidores:", window.notiseguidores);
        console.log("Mensajes:", window.notimensajes);

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
        }

        if (!mostrarNotificacion) {
            actualizarContadorNotificaciones(1);
            return;
        }

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
    })
    .catch(error => {
        console.error("Error al obtener variables de sesión:", error);
    });
    
});

socket.on("mostrar-mensaje", (data) => {

    const chatContainer = document.getElementById("chat-cont");  

    const mensajeDiv = document.createElement("div");
    mensajeDiv.classList.add("mensaje_recibido");  

    mensajeDiv.innerHTML = `
        <p><strong>${data.usuario_emisor}:</strong> ${data.contenido}</p>
        <p><small>${new Date(data.hora).toLocaleString()}</small></p>  
    `;

    chatContainer.appendChild(mensajeDiv);

    chatContainer.scrollTop = chatContainer.scrollHeight;

    
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
          console.log(result);

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

function enviarMensaje(usuario_actual, usuario_dest, chatId, mensaje) {
    if (mensaje.trim() === '') {
        alert('El mensaje no puede estar vacío');
        return;
    }

    const chatContainer = document.getElementById("chat-cont");  

    const mensajeDiv = document.createElement("div");
    mensajeDiv.classList.add("mensaje_enviado");  

    mensajeDiv.innerHTML = `
        <p><strong>Tú:</strong> ${mensaje}</p>
        <p><small>${new Date(new Date().toISOString()).toLocaleString()}</small></p>  
    `;

    chatContainer.appendChild(mensajeDiv);

    chatContainer.scrollTop = chatContainer.scrollHeight;

    socket.emit("nuevo-mensaje", {usuario_actual: usuario_actual, usuario_dest: usuario_dest, chatId: chatId, mensaje: mensaje});

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
    

