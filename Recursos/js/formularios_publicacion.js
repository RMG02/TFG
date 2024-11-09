function form_publi(){
    
    var boton_publica = document.getElementById("publicaBtn");
    var boton_publicacion = document.getElementById("publicacionBtn");
    var spans = document.getElementsByClassName("close");

    boton_publica.onclick = function() {
        var modal = document.getElementById("opcionesPublicacion");
        modal.style.display = "block";
    };

    boton_publicacion.onclick = function(){
        var modal = document.getElementById("formPublicacion");
        document.getElementById("opcionesPublicacion").style.display = "none";
        modal.style.display = "block";
        
    }

    for (let i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
            var modal = spans[i].closest(".modal"); 
            modal.style.display = "none";
        };
    }


}

window.onload = form_publi;
