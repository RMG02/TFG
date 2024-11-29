function Perfil() {
    var boton = document.getElementById("botonInit");
    var spans = document.getElementsByClassName("close");
    var publicaciones = document.getElementsByClassName("tweet");
    var spans_publi = document.getElementsByClassName("close_publi");
    var editar = document.getElementsByName("editar");
    var comentar = document.getElementsByName("comen");
    var comentarios = document.getElementsByName("comentario");
    var editar_com = document.getElementsByName("editar_com");
    var editar_com_rec = document.getElementsByName("editar_com_rec");
    var responder = document.getElementsByName("responder");
    var comentarios_rec = document.getElementsByName("comentario_rec");
    console.log("comentarios normales", comentarios);
    console.log("comentarios recursivos", comentarios_rec);
    console.log("editar recursivos", editar_com_rec);

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

    var j = 0;
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

        responder[i].onclick = function() {
            var modal = document.getElementById("respuesta-" + i);
            modal.style.display = "block";
        };
    }

    var i = 0;
    var j = 0;
    var k = 0;
    while (i < comentarios_rec.length) {
        let currentI = i, currentJ = j, currentK = k;
        var modal = document.getElementById("comentario-" + currentJ + "-" + currentK);
        console.log("i, j y k", currentI, currentJ, currentK);
        console.log("editMODAL",modal);
        if (modal) {
            comentarios_rec[currentI].onclick = function() {
                var modal = document.getElementById("comentario-" + currentJ + "-" + currentK);
                console.log(modal);
                modal.style.display = "block";
            };

            /*var editModal = document.getElementById("editCom-" + currentJ + "-" + currentK);
            if (editModal) {
                
                editar_com_rec[currentI].onclick = function() {
                    var editModal = document.getElementById("editCom-" + currentJ + "-" + currentK);
                    console.log("click i, j y k", currentI, currentJ, currentK);
                    console.log(editModal);
                    editModal.style.display = "block";
                };
            }*/

            i++;
            k++;
        } else {
            k = 0;
            j++;
        }
    }
}

document.addEventListener("DOMContentLoaded", Perfil);
