var express = require("express");
var app = express();
var server = require("http").Server(app);
var io = require("socket.io")(server);
var axios = require('axios'); 
var usuarios_conectados = {}; 
  
io.on("connection", (socket) => {

    socket.on("conectado", (data) => {
        if(data.usuario){
            usuarios_conectados[data.usuario] = socket.id;
        }
    });

    socket.on("decrementar-reaccion", (data) => {
        var socketID = usuarios_conectados[data.usuario];
        if (socketID) {
            io.to(socketID).emit("decremento", 1);
        }
    });

    socket.on("like-dado", (data) => {
        var socketID = usuarios_conectados[data.usuario_des];
        var notificacion = { 
            usuario_publi: data.usuario_des, 
            usuario_accion: data.usuario,
            mensaje: data.usuario + " ha dado like a tu publicación",
            id_publi: data.id_publi,
            enlace: "http://localhost:8000/Vista/Verpublicacion.php?id=" + data.id_publi,
            tipo: data.tipo,
            fecha: new Date().toISOString(),
            vista: false
        };
  
        if (socketID) {
            io.to(socketID).emit("notificacion", notificacion);
        }

        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)         
        
    });
  
    socket.on("dislike-dado", (data) => {
        var socketID = usuarios_conectados[data.usuario_des];
        var notificacion = { 
            usuario_publi: data.usuario_des, 
            usuario_accion: data.usuario,
            mensaje: data.usuario + " ha dado dislike a tu publicación",
            id_publi: data.id_publi,
            enlace: "http://localhost:8000/Vista/Verpublicacion.php?id=" + data.id_publi,
            tipo: data.tipo,
            fecha: new Date().toISOString(),
            vista: false
        };
  
        if (socketID) {
            io.to(socketID).emit("notificacion", notificacion);
            
        }
        
        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)          
    });
  
      socket.on('disconnect', () => {
          for (let usuario in usuarios_conectados) {
              if (usuarios_conectados[usuario] === socket.id) {
                  delete usuarios_conectados[usuario];
                  break;
              }
          }
      });
});

server.listen(3000, function () {
    console.log("Servidor corriendo en http://localhost:3000");
  });



