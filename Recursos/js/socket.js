var socket = io.connect("http://localhost:3000", { transports: ['websocket'], forceNew: true });

var Usuario = document.querySelector('meta[name="usuario"]').getAttribute('content');
socket.emit("conectado", {usuario: Usuario});

socket.on("notificacion", function(data) {
    var divNotificaciones = document.getElementById("notificaciones");

    var notificacion = document.createElement("div");
    notificacion.className = "notificacion";
    notificacion.innerHTML = data.mensaje + "<br><a href=" + data.enlace +">Ver publicación</a>";

    divNotificaciones.appendChild(notificacion);

    // Auto-ocultar la notificación después de 5 segundos
    setTimeout(function() {
        notificacion.classList.add("hidden");
        setTimeout(function() {
            divNotificaciones.removeChild(notificacion);
        }, 300);
    }, 10000);
});

function enviarDatos(event, usuario, usuario_des, id_publi, likes, dislikes) {
    fetch('../../Controlador/Publicacion_controlador.php', {
    }).then(response => response.text())
      .then(result => {
          console.log(result);

        if (event.submitter.name === 'darlike') {
            // Comprobar si el usuario ya está en el array de likes
            if (!likes.includes(usuario)) {
                darLike(usuario, usuario_des, id_publi);
            } else {
                console.log("El usuario ya ha dado like a esta publicación.");
            }
        } else if (event.submitter.name === 'dardislike') {
            // Comprobar si el usuario ya está en el array de dislikes
            if (!dislikes.includes(usuario)) {
                darDislike(usuario, usuario_des, id_publi);
            } else {
                console.log("El usuario ya ha dado dislike a esta publicación.");
            }
        }
          
      }).catch(error => {
          console.error('Error al enviar datos:', error);
      });
}

function darLike(usuario, usuario_des, id_publi) {
    socket.emit("like-dado", { usuario: usuario, usuario_des: usuario_des, id_publi: id_publi, tipo: "like" });
}

function darDislike(usuario, usuario_des, id_publi) {
    socket.emit("dislike-dado", { usuario: usuario, usuario_des: usuario_des, id_publi: id_publi, tipo: "dislike"});
}






