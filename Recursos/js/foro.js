
function abrirModal(modalId, nick1, nick2) {
    var modal = document.getElementById(modalId);
    console.log(modal, nick1, nick2);
    if (modal && nick1 === nick2) {
        modal.style.display = "flex";
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
