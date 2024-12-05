function Perfil() {
    var boton = document.getElementById("botonInit");
    var spans = document.getElementsByClassName("close");
    var publicaciones = document.getElementsByClassName("tweet");
    

    if (boton) {
        boton.onclick = function() {
            var modal = document.getElementById("eliminar");
            modal.style.display = "block";
        };
    }

    for (let i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
            var modal = this.closest(".modal");
            modal.style.display = "none";
        };
    }

    for (let i = 0; i < publicaciones.length; i++) {
        publicaciones[i].onclick = function() {
            var modal = document.getElementById(i);
            var form = modal.querySelector('form'); 
            if (form) { 
                form.submit();
            }
        };
    }

}

document.addEventListener("DOMContentLoaded", Perfil);
