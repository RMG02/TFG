var socket = io.connect("http://localhost:3000", { transports: ['websocket'], forceNew: true });

var Usuario = document.querySelector('meta[name="usuario"]').getAttribute('content');
socket.emit("conectado", {usuario: Usuario});

socket.on("notificacion", function(data) {
    console.log("Notificación recibida: ", data);

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
    }, 20000);
});

function enviarDatos(event, usuario, usuario_des, id_publi) {
    fetch('../../Controlador/Publicacion_controlador.php', {
    }).then(response => response.text())
      .then(result => {
          console.log(result);

          if (event.submitter.name === 'darlike') {
              darLike(usuario, usuario_des, id_publi);
          } else if (event.submitter.name === 'dardislike') {
              darDislike(usuario, usuario_des, id_publi);
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






