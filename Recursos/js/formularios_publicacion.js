function form_publi(){
    
    var boton_publica = document.getElementById("publicaBtn");
    var spans = document.getElementsByClassName("close");

    boton_publica.onclick = function() {
        var modal = document.getElementById("formPublicacion");
        modal.style.display = "block";
    };

    for (let i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
            var modal = spans[i].closest(".modal"); 
            modal.style.display = "none";
        };
    }


}

document.addEventListener("DOMContentLoaded", form_publi);
