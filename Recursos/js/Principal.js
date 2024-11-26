function Perfil() {
    var boton = document.getElementById("botonInit");
    var spans = document.getElementsByClassName("close");
    var publicaciones = document.getElementsByClassName("tweet");
    var spans_publi = document.getElementsByClassName("close_publi");
    var editar = document.getElementsByName("editar");
    var comentar = document.getElementsByName("comen");
    var comentarios =  document.getElementsByClassName("comentario");
    var editar_com = document.getElementsByName("editar_com");
    
    
    if(boton){
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
    

    var j = 0;
    // Abrir y manejar modales de publicaciones
    for (let i = 0; i < publicaciones.length; i++) {
        publicaciones[i].onclick = function() {
            var modal = document.getElementById(i);
            modal.style.display = "block";
        };

        var modal = document.getElementById("edit-" + i);      
        if (modal) {
            editar[j].onclick = function() {
                var modal = document.getElementById("edit-" + i);
                modal.style.display = "block";
            }; 
            j++;
        }
        
        comentar[i].onclick = function() {
            var modal = document.getElementById("comen-" + i);
            modal.style.display = "block";
        }; 
        
    }

    // Cerrar modales de publicaciones
    for (let i = 0; i < spans_publi.length; i++) {
        spans_publi[i].onclick = function() {
            var modal = this.closest(".modal_publi");
            modal.style.display = "none";
        };
    }

    j = 0;
    for (let i = 0; i < comentarios.length; i++) {
        comentarios[i].onclick = function() {
            var modal = document.getElementById("comentario-" + i);
            modal.style.display = "block";
        };

        var modal = document.getElementById("editCom-" + i); 
        
        if (modal) {
            editar_com[j].onclick = function() {
                var modal = document.getElementById("editCom-" + i);
                modal.style.display = "block";
            }; 
            j++;
        }
    
    }
}

document.addEventListener("DOMContentLoaded", Perfil);
