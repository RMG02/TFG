
document.addEventListener("DOMContentLoaded", function() {
    var chatContainer = document.querySelector(".chat-container");
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
});


function mostrarOpciones(mensajeId) {
    var opciones = document.getElementById("mensaje-" + mensajeId);
    if (opciones) {
        opciones.style.display = "flex";
    }
}

function mostrarEditar(mensajeId){
    var editar = document.getElementById("edit-" + mensajeId);
    if (editar) {
        editar.style.display = "flex";
    }
}

function cerrarModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
    }
}
