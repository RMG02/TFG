function Perfil() {
    var botones = document.getElementsByClassName("botonInit");
    var spans = document.getElementsByClassName("close");
    var publicaciones = document.getElementsByClassName("tweet");
    var spans_publi = document.getElementsByClassName("close_publi");
    var editar = document.getElementsByName("editar");

    for (let i = 0; i < botones.length; i++) {
        botones[i].onclick = function() {
            var modal = document.getElementById(i + 1);
            modal.style.display = "block";
        };
    }

    for(let i = 0; i < spans.length; i++){
        spans[i].onclick = function() {
            var modal = this.closest(".modal");
            modal.style.display = "none";
        };
    }
    
    for (let i = 0; i < publicaciones.length; i++) {
        publicaciones[i].onclick = function() {
            var modal = document.getElementById(i + 2);
            modal.style.display = "block";
        };

        editar[i].onclick = function() {
            var modal = document.getElementById("edit-" + (i + 2));
            modal.style.display = "block";
        };        
        
        spans_publi[i].onclick = function() {
            var modal = this.closest(".modal_publi");
            modal.style.display = "none";
        };
    }

    

}

window.onload = Perfil;
