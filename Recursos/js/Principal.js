function Perfil() {
    var botones = document.getElementsByClassName("botonInit");
    var spans = document.getElementsByClassName("close");
    var publicaciones = document.getElementsByClassName("tweet");
    var spans_publi = document.getElementsByClassName("close_publi");
    var editar = document.getElementsByName("editar");
    var comentar = document.getElementsByName("comen");
    var comentarios =  document.getElementsByClassName("comentario");
    var editar_com = document.getElementsByName("editar_com");

    for (let i = 0; i < botones.length; i++) {
        botones[i].onclick = function() {
            var modal = document.getElementById(i + 1);
            modal.style.display = "block";
        };
    }

    for (let i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
            var modal = this.closest(".modal");
            modal.style.display = "none";
        };
    }
    
    // Abrir y manejar modales de publicaciones
    for (let i = 0; i < publicaciones.length; i++) {
        publicaciones[i].onclick = function() {
            var modal = document.getElementById(i + 2);
            modal.style.display = "block";
        };



        // Asegurar que solo haya un botón "editar" y "comen" por publicación
        if (editar[i]) {
            editar[i].onclick = function() {
                var modal = document.getElementById("edit-" + (i + 2));
                modal.style.display = "block";
            }; 
        }
        
        if (comentar[i]) {
            comentar[i].onclick = function() {
                var modal = document.getElementById("comen-" + (i + 2));
                modal.style.display = "block";
            }; 
        }
    }

    // Cerrar modales de publicaciones
    for (let i = 0; i < spans_publi.length; i++) {
        spans_publi[i].onclick = function() {
            var modal = this.closest(".modal_publi");
            modal.style.display = "none";
        };
    }

    for (let i = 0; i < comentarios.length; i++) {
        comentarios[i].onclick = function() {
            var modal = document.getElementById("comantario-" + (i + 2));
            modal.style.display = "block";
        };

        
            editar_com[i].onclick = function() {
                var modal = document.getElementById("editCom-" + (i + 2));
                modal.style.display = "block";
            }; 
        
    }

}

document.addEventListener("DOMContentLoaded", Perfil);
