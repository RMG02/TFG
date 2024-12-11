

function Perfil() {
    
    var spans = document.getElementsByClassName("close");
    var spans_publi = document.getElementsByClassName("close_publi");
    var comentar = document.getElementsByName("comen");
    var editar = document.getElementsByName("editar");
    var comentarios = document.getElementsByName("comentario");
    var editar_com = document.getElementsByName("editar_com");
    var editar_com_rec = document.getElementsByName("editar_com_rec");
    var responder = document.getElementsByName("responder");
    var comentarios_rec = document.getElementsByName("comentario_rec");
    var responder_rec = document.getElementsByName("responder_rec");

    if(comentar.length != 0){
        comentar[0].onclick = function() {
            var modal = document.getElementById("comen-" + 0);
            modal.style.display = "block";
        };
    }

    if(editar.length != 0){
        editar[0].onclick = function() {
            var modal = document.getElementById("edit-" + 0);
            modal.style.display = "block";
        };
    }
    
    for (let i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
            var modal = this.closest(".modal");
            modal.style.display = "none";
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
            console.log(modal);
            modal.style.display = "block";
        };
    }

   
    var i = 0, j = 0, k = 0, l = 0;
    while (i < comentarios_rec.length) {
        let currentI = i, currentJ = j, currentK = k, currentL = l; 
        var modal = document.getElementById("comentario-" + currentJ + "-" + currentK);
        if (modal) {
            comentarios_rec[currentI].onclick = function() {
                var modal = document.getElementById("comentario-" + currentJ + "-" + currentK);
                modal.style.display = "block";
            };

            var editModal = document.getElementById("editCom-" + currentJ + "-" + currentK);
            if (editModal) {
        
                if(editar_com_rec[currentL]){
                    editar_com_rec[currentL].onclick = function() {
                        var editModal = document.getElementById("editCom-" + currentJ + "-" + currentK);
                        editModal.style.display = "block";
                    };
                    l++;
                }
                
            }

            responder_rec[currentI].onclick = function() {
                var modal = document.getElementById("respuesta-" + currentJ + "-" + currentK);
                modal.style.display = "block"; 
            };

            i++;
            k++;
        } else {
            k = 0;
            j++;
        }
    }
}

document.addEventListener("DOMContentLoaded", Perfil);

function copiarEnlace(input) {
    if (input && (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA')) {
        const url = input.value;
        navigator.clipboard.writeText(url).then(() => {
            alert("Enlace copiado: " + url);
        }).catch(err => {
            console.error('Error al copiar el enlace: ', err);
        });
    } else {
        console.error("Elemento no es seleccionable");
    }
}

document.getElementById('download-btn').addEventListener('click', function () {
    

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Datos receta
    const title = this.dataset.title.toUpperCase();
    const nick = this.dataset.nick.replace(/\\n/g, '\n');
    const ingredients = this.dataset.ingredients.replace(/\\n/g, '\n');
    const preparation = this.dataset.preparation.replace(/\\n/g, '\n');
    const multimedia = this.dataset.multimedia;
    const extension = this.dataset.extension;
    var tiempo = parseInt(this.dataset.tiempo);
    var dificultad = parseInt(this.dataset.dificultad);
    
    // Agregar título
    doc.setFontSize(18);
    doc.text(title, 10, 10);

    let textStartY = 30; // Posición inicial para el texto

    // Comprobar si hay imagen en la receta
    if (multimedia !== '' && ['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
        const img = new Image();
        img.src = `../Recursos/multimedia/${multimedia}`;
        img.onload = function () {
            const imgWidth = this.width;
            const imgHeight = this.height;

            const pageWidth = doc.internal.pageSize.getWidth(); 
            const maxWidth = pageWidth - 20; 
            const scaleFactor = maxWidth / imgWidth;

            const scaledWidth = imgWidth * scaleFactor;
            const scaledHeight = imgHeight * scaleFactor;

            // Agregar imagen al PDF
            doc.addImage(img, extension.toUpperCase(), 10, 20, scaledWidth, scaledHeight);
            textStartY = 20 + scaledHeight + 10; // Actualizar posición inicial del texto

            agregarTexto(doc, ingredients, preparation, nick, textStartY,tiempo,dificultad);//Añadir la informacion
            doc.save(`Receta.pdf`);//guardar el pdf
        };
        img.onerror = function () {
            alert("Error al cargar la imagen. El PDF se generará sin ella."); //se genera el pdf sin la imagen
            agregarTexto(doc, ingredients, preparation, nick, textStartY,tiempo,dificultad);
            doc.save(`Receta.pdf`);
        };
    } else {
        // Si no hay imagen se añade el texto solo
        agregarTexto(doc, ingredients, preparation, nick, textStartY,tiempo,dificultad);
        doc.save(`Receta.pdf`);
    }
});

// Función para agregar texto al PDF
function agregarTexto(doc, ingredients, preparation, nick, startY,tiempox,dificultadx) {
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    const tiempoIcono = new Image();
    tiempoIcono.src = '../Recursos/imagenes/reloj.png';
        doc.text(`Tiempo`, 10, startY);
        doc.addImage(tiempoIcono, 'PNG', 15, startY + 7, 5, 5); 
        doc.text(`${tiempox || 'No especificado'} minutos`, 20, startY + 10);
    
    const dificultadIcono = new Image();
    dificultadIcono.src = '../Recursos/imagenes/chef.png'; 
        doc.text("Dificultad:", 10, startY + 20);
        let xPosition = 20; // Posición inicial para los iconos
        for (let i = 0; i < dificultadx; i++) {
            doc.addImage(dificultadIcono, 'PNG', xPosition, startY + 30, 5, 5);
            xPosition += 6; // Ajusta la distancia entre los iconos
        }
    
    doc.text("Ingredientes:", 10, startY + 40);
    doc.setFontSize(14);
    doc.setFont("helvetica", "normal");
    doc.text(ingredients, 10, startY + 50);
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text("Preparación:", 10, startY + 70);
    doc.setFontSize(14);
    doc.setFont("helvetica", "normal");
    doc.text(preparation, 10, startY + 80);

    doc.setFontSize(12);
    doc.setFont("helvetica", "italic");
    doc.text(`Creado por: ${nick}`, 10, startY + 120);
}

