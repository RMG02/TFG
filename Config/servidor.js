var express = require("express");
var app = express();
var server = require("http").Server(app);
var io = require("socket.io")(server);
var axios = require('axios'); 
var usuarios_conectados = {}; 
var contador_notificaciones = {};
var usuarios_en_chat = {}

  
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

    // Cuando el usuario entra al chat, puedes actualizar su estado
    socket.on('entrar_chat', (data) => {
        usuarios_en_chat[data.usuario] = data.chatId;
        io.emit("actualizar-estado-usuarios", usuarios_en_chat);


    });

    // Cuando el usuario sale del chat, actualiza su estado
    socket.on('salir_chat', (usuario) => {
        delete usuarios_en_chat[usuario];
        io.emit("actualizar-estado-usuarios", usuarios_en_chat);

    });


    socket.on('cambioNick', (data) => {
        var datos = { 
            nick_act: data.nick_act, 
            nuevoNick: data.nuevoNick,
            admin:data.admin
        };

        io.emit("actualizar-cambioNick", datos);

    });

    socket.on("decrementar-reaccion", (data) => {
        var socketID = usuarios_conectados[data.usuario];
        if (socketID) {
            io.to(socketID).emit("decremento", 1);
        }
        else{
            contador_notificaciones[data.usuario]--;
            if(contador_notificaciones[data.usuario] < 0){
                contador_notificaciones[data.usuario] = 0;
            }
        }
        
    });


    socket.on("like-dado", (data) => {
        if(data.tipo_publicacion == "publicacion"){
            var tipo_mensaje = data.usuario + " ha dado like a tu publicación";
            var tipo_enlace = "http://localhost:8000/Controlador/Publicacion_controlador.php?publi_id=true&id=" + data.id_publi;
        }
        else if(data.tipo_publicacion == "receta"){
            var tipo_mensaje = data.usuario + " ha dado like a tu receta";
            var tipo_enlace = "http://localhost:8000/Controlador/Receta_controlador.php?publi_id=true&id=" + data.id_publi;
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
            contador_notificaciones[data.usuario_des]++;
        }
        
        
        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)         
        
    });
  
    socket.on("dislike-dado", (data) => {
        if(data.tipo_publicacion == "publicacion"){
            var tipo_mensaje = data.usuario + " ha dado dislike a tu publicación"
            var tipo_enlace = "http://localhost:8000/Controlador/Publicacion_controlador.php?publi_id=true&id=" + data.id_publi;
            
        }
        else if(data.tipo_publicacion == "receta"){
            var tipo_mensaje = data.usuario + " ha dado dislike a tu receta";
            var tipo_enlace = "http://localhost:8000/Controlador/Receta_controlador.php?publi_id=true&id=" + data.id_publi;
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
            contador_notificaciones[data.usuario_des]++;

        }
        
        
        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)          
    });

    socket.on("unset", () => {
        socket.broadcast.emit("unset_noti");
    });

    socket.on("nuevo-comentario", (data) => {
        if(data.tipo_publicacion == "publicacion"){
            if(data.respuesta){
                var tipo_mensaje = data.usuario + " ha respondido a tu comentario en una publicación";
            }
            else{
                var tipo_mensaje = data.usuario + " ha comentado tu publicación";
            }
            var tipo_enlace = "http://localhost:8000/Controlador/Publicacion_controlador.php?publi_id=true&id=" + data.id_publi;
        }
        else if(data.tipo_publicacion == "receta"){
            if(data.respuesta){
                var tipo_mensaje = data.usuario + " ha respondido a tu comentario en una receta";

            }
            else{
                var tipo_mensaje = data.usuario + " ha comentado tu receta";

            }
            var tipo_enlace = "http://localhost:8000/Controlador/Receta_controlador.php?publi_id=true&id=" + data.id_publi;
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
            tipo_publicacion: data.tipo_publicacion,
            id_comentario: data.id_com,
        };
  
        if (socketID) {
            io.to(socketID).emit("notificacion", notificacion);
        }
        else{
            contador_notificaciones[data.usuario_des]++;
        }
        
        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)         
        
    });

    socket.on("nuevo-mensaje", (data) => {
        var mensaje = data.usuario_actual + " te ha enviado un mensaje";
        var enlace = "http://localhost:8000/Vista/chat.php?conversacionId=" + data.chatId;

        var socketID = usuarios_conectados[data.usuario_dest];
        var socketIDPer = usuarios_conectados[data.usuario_actual];
        var enviar_noti = true;

        var notificacion = { 
            usuario_publi: data.usuario_dest, 
            usuario_accion: data.usuario_actual,
            mensaje: mensaje,
            id_publi: null,
            enlace: enlace,
            tipo: "mensaje",
            fecha: new Date().toISOString(),
            tipo_publicacion: "mensaje"
        };
        
        var men = {
            conversacion_id: data.chatId,
            usuario_emisor: data.usuario_actual,
            usuario_receptor: data.usuario_dest,
            contenido: data.mensaje,
            hora: new Date().toISOString(),
        } 

        if (socketID) {
            if(usuarios_en_chat.hasOwnProperty(data.usuario_dest) && usuarios_en_chat[data.usuario_dest] == data.chatId){
                enviar_noti = false;
            }
            else{
                io.to(socketID).emit("notificacion", notificacion);
            }
            io.to(socketID).emit("mostrar-mensaje", men);
            
        }
        else{
            contador_notificaciones[data.usuario_dest]++;

        }
        
        if(enviar_noti){
            // Usar URLSearchParams para formatear los datos
            var params = new URLSearchParams();
            params.append('notificacion', JSON.stringify(notificacion));
            axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)      

        }
        

        var param_men = new URLSearchParams();
        param_men.append('AgregarMensaje', JSON.stringify(men));


        axios.post('http://localhost:8000/Controlador/Conversaciones_controlador.php', param_men)
            .then(response => {
                const mensajeId = response.data.mensaje_id;
                io.to(socketIDPer).emit("mostrar-mensaje-per", men, mensajeId);
                io.to(socketID).emit("mostrar-mensaje-modal", mensajeId);

                
               
            })
            .catch(error => console.error("Error en la petición:", error));       
   
    });

    socket.on("eliminar-mensaje", (data) => {

        var socketID = usuarios_conectados[data.usuario];
        
        if (socketID) {
            io.to(socketID).emit("elim-mensaje", data.mensajeId);   
        }
        
        var men = {
            mensaje_id: data.mensajeId,
        } 

        var param_men = new URLSearchParams();
        param_men.append('EliminarMensaje', JSON.stringify(men));


        axios.post('http://localhost:8000/Controlador/Conversaciones_controlador.php', param_men)          
   
    });

    socket.on("editar-mensaje", (data) => {
        var socketID = usuarios_conectados[data.usuario];

        var men = {
            mensaje_id: data.mensajeId,
            contenido: data.contenido
        } 
        if (socketID) {
            io.to(socketID).emit("edit-mensaje", men);   
        }
        
        

        var param_men = new URLSearchParams();
        param_men.append('EditarMensaje', JSON.stringify(men));


        axios.post('http://localhost:8000/Controlador/Conversaciones_controlador.php', param_men)          
   
    });


    socket.on("follow", async (data) => {
        var mensaje = data.usuario_actual + " te ha seguido";
        var enlace = "http://localhost:8000/Vista/unsetPerfilPublico.php?nick_user=" + data.usuario_actual;

        var socketID = usuarios_conectados[data.usuario_dest];
        var notificacion = { 
            usuario_publi: data.usuario_dest, 
            usuario_accion: data.usuario_actual,
            mensaje: mensaje,
            id_publi: null,
            enlace: enlace,
            tipo: "follow",
            fecha: new Date().toISOString(),
            tipo_publicacion: "follows"
        };
  
        if (socketID) {
            io.to(socketID).emit("notificacion", notificacion);
            io.to(socketID).emit("actualizar_usuario");
        }
        else{
            contador_notificaciones[data.usuario_dest]++;

        }
        
        
        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        await axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)
        
        if (socketID) {
            io.to(socketID).emit("actualizar_usuario"); 
        }
        
    });

    socket.on("unfollow", async (data) => {
        var mensaje = data.usuario_actual + " te ha dejado de seguir";
        var enlace = "http://localhost:8000/Vista/unsetPerfilPublico.php?nick_user=" + data.usuario_actual;

        var socketID = usuarios_conectados[data.usuario_dest];
        var notificacion = { 
            usuario_publi: data.usuario_dest, 
            usuario_accion: data.usuario_actual,
            mensaje: mensaje,
            id_publi: null,
            enlace: enlace,
            tipo: "unfollow",
            fecha: new Date().toISOString(),
            tipo_publicacion: "follows"
        };
  
        if (socketID) {
            io.to(socketID).emit("notificacion", notificacion);
            io.to(socketID).emit("actualizar_usuario");

        }
        else{
            contador_notificaciones[data.usuario_dest]++;

        }
        
        
        // Usar URLSearchParams para formatear los datos
        var params = new URLSearchParams();
        params.append('notificacion', JSON.stringify(notificacion));

        await axios.post('http://localhost:8000/Controlador/Notificacion_controlador.php', params)

        if (socketID) {
            io.to(socketID).emit("actualizar_usuario"); 
        }
        
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



