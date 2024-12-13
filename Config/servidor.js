var express = require("express");
var app = express();
var server = require("http").Server(app);
var io = require("socket.io")(server);
var axios = require('axios'); 
var usuarios_conectados = {}; 
var contador_notificaciones = {};

  
io.on("connection", (socket) => {

    socket.on("conectado", (data) => {

        if(data.usuario){
            usuarios_conectados[data.usuario] = socket.id;
            if(contador_notificaciones.hasOwnProperty(data.usuario)){
                io.to(usuarios_conectados[data.usuario]).emit("actualizar-contador", contador_notificaciones[data.usuario]);
                delete contador_notificaciones[data.usuario];
            }
            
            
        }
    });

    socket.on("decrementar-reaccion", (data) => {
        var socketID = usuarios_conectados[data.usuario];
        if (socketID) {
            io.to(socketID).emit("decremento", 1);
        }
        else{
            contador_notificaciones[data.usuario] -= 1;

        }
        
    });

    socket.on("like-dado", (data) => {
        if(data.tipo_publicacion == "publicacion"){
            var tipo_mensaje = data.usuario + " ha dado like a tu publicación";
            var tipo_enlace = "http://localhost:8000/Vista/Verpublicacion.php?id=" + data.id_publi;
        }
        else if(data.tipo_publicacion == "receta"){
            var tipo_mensaje = data.usuario + " ha dado like a tu receta";
            var tipo_enlace = "http://localhost:8000/Vista/Verreceta.php?id=" + data.id_publi;
        }
        var socketID = usuarios_conectados[data.usuario_des];
        var notificacion = { 
            usuario_publi: data.usuario_des, 
            usuario_accion: data.usuario,
            mensaje: tipo_mensaje,
            id_publi: data.id_publi,
            enlace: tipo_enlace,
            tipo: data.tipo,
            fecha: new Date().toISOString(),
            tipo_publicacion: data.tipo_publicacion
        };
  
        if (socketID) {
            io.to(socketID).emit("notificacion", notificacion);
        }
        else{
            contador_notificaciones[data.usuario_des] += 1;
        }
        
        
        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)         
        
    });
  
    socket.on("dislike-dado", (data) => {
        if(data.tipo_publicacion == "publicacion"){
            var tipo_mensaje = data.usuario + " ha dado dislike a tu publicación"
            var tipo_enlace = "http://localhost:8000/Vista/Verpublicacion.php?id=" + data.id_publi;
        }
        else if(data.tipo_publicacion == "receta"){
            var tipo_mensaje = data.usuario + " ha dado dislike a tu receta";
            var tipo_enlace = "http://localhost:8000/Vista/Verreceta.php?id=" + data.id_publi;
        }

        var socketID = usuarios_conectados[data.usuario_des];
        var notificacion = { 
            usuario_publi: data.usuario_des, 
            usuario_accion: data.usuario,
            mensaje: tipo_mensaje,
            id_publi: data.id_publi,
            enlace: tipo_enlace,
            tipo: data.tipo,
            fecha: new Date().toISOString(),
            tipo_publicacion: data.tipo_publicacion
        };
  
        if (socketID) {
            io.to(socketID).emit("notificacion", notificacion);
            
        }
        else{
            contador_notificaciones[data.usuario_des] += 1;

        }
        
        
        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)          
    });

    socket.on("nuevo-comentario", (data) => {
        if(data.tipo_publicacion == "publicacion"){
            var tipo_mensaje = data.usuario + " ha comentado tu publicación";
            var tipo_enlace = "http://localhost:8000/Vista/Verpublicacion.php?id=" + data.id_publi;
        }
        else if(data.tipo_publicacion == "receta"){
            var tipo_mensaje = data.usuario + " ha comentado tu receta";
            var tipo_enlace = "http://localhost:8000/Vista/Verreceta.php?id=" + data.id_publi;
        }
        var socketID = usuarios_conectados[data.usuario_des];
        var notificacion = { 
            usuario_publi: data.usuario_des, 
            usuario_accion: data.usuario,
            mensaje: tipo_mensaje,
            id_publi: data.id_publi,
            enlace: tipo_enlace,
            tipo: data.tipo,
            fecha: new Date().toISOString(),
            tipo_publicacion: data.tipo_publicacion
        };
  
        if (socketID) {
            io.to(socketID).emit("notificacion", notificacion);
        }
        else{
            contador_notificaciones[data.usuario_des] += 1;
        }
        
        

        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)         
        
    });
    
    socket.on('desconectado', (data) => {
        contador_notificaciones[data.usuario] = data.num_noti;
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



