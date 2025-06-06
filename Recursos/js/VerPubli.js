

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

    j = 0; k = 0;
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
        var modal = document.getElementById("respuesta-" + i);
        if(modal){
            responder[k].onclick = function() {
                var modal = document.getElementById("respuesta-" + i);
                console.log(modal);
                modal.style.display = "block";
            };
            k++;
        }
       
    }

   
    var i = 0, j = 0, k = 0, l = 0, r = 0;
    while (i < comentarios_rec.length) {
        let currentI = i, currentJ = j, currentK = k, currentL = l, currentR = r; 
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
            var respModal = document.getElementById("respuesta-" + currentJ + "-" + currentK);
            if(respModal){
                if(responder_rec[currentR]){
                    responder_rec[currentR].onclick = function() {
                        var respModal = document.getElementById("respuesta-" + currentJ + "-" + currentK);
                        respModal.style.display = "block"; 
                    };
                    r++;
                }
                
            }
            

            i++;
            k++;
        } else {
            k = 0;
            j++;
        }
    }
}

document.addEventListener("DOMContentLoaded", Perfil);


function modal_compartir(id) {

    
    var modal = document.getElementById("compartir-" + id);
    modal.style.display = "flex";
    
}

function cerrar_modal_compartir(id){
    var modal = document.getElementById(id);
    modal.style.display = "none";
}

function filtrarUsuariosCompartir(id) {
    var busqueda = document.getElementById('buscador-compartir').value.toLowerCase();
    var nombres = document.getElementsByTagName('li'); // Obtiene todos los <li>
    var seccion = document.getElementById('seccion-usuarios-' + id).style.display !== 'none' ? 'usuarios' : 'foros';

    for (var i = 0; i < nombres.length; i++) {
        var boton = nombres[i].getElementsByTagName("button")[0]; // Busca el botón dentro del <li>
        if (boton) {
            var nick = boton.textContent.toLowerCase(); // Obtiene solo el texto del botón sin espacios
            if (nick.startsWith(busqueda)) {
                nombres[i].style.display = ""; // Muestra el usuario si coincide
            } else {
                nombres[i].style.display = "none"; // Oculta el usuario si no coincide
            }
        }
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
    const tipo = this.dataset.tipo;
    const multimedia = this.dataset.multimedia;
    const extension = this.dataset.extension;
    var tiempo = parseInt(this.dataset.tiempo);
    var dificultad = parseInt(this.dataset.dificultad);

    // Agregar título
    doc.setFontSize(18);
    doc.text(title, 10, 10);
    doc.setFontSize(14);
    doc.text(tipo, 10, 18);

    let textStartY = 30; // Posición inicial para el texto

    // Comprobar si hay imagen
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
            textStartY = 20 + scaledHeight + 10;

            agregarTexto(doc, ingredients, preparation, nick, textStartY, tiempo, dificultad);
            doc.save(`Receta.pdf`);
        };
        img.onerror = function () {
            alert("Error al cargar la imagen. El PDF se generará sin ella.");
            agregarTexto(doc, ingredients, preparation, nick, textStartY, tiempo, dificultad);
            doc.save(`Receta.pdf`);
        };
    } else {
        agregarTexto(doc, ingredients, preparation, nick, textStartY, tiempo, dificultad);
        doc.save(`Receta.pdf`);
    }
});

// Función para agregar texto al PDF
function agregarTexto(doc, ingredients, preparation, nick, startY, tiempox, dificultadx) {
    const pageHeight = doc.internal.pageSize.getHeight();
    const pageWidth = doc.internal.pageSize.getWidth();
    const textWidth = pageWidth - 20;
    let currentY = startY;

    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");

    // Tiempo
    const tiempoIcono = new Image();
    tiempoIcono.src = '../Recursos/imagenes/reloj.png';
    doc.text("Tiempo:", 10, currentY);
    doc.addImage(tiempoIcono, 'PNG', 35, currentY - 4, 5, 5);
    doc.text(`${tiempox || 'No especificado'} minutos`, 42, currentY);

    // Dificultad
    const dificultadIcono = new Image();
    dificultadIcono.src = '../Recursos/imagenes/chef.png';
    doc.text("Dificultad:", 140, currentY);
    let xPosition = 170;
    for (let i = 0; i < dificultadx; i++) {
        doc.addImage(dificultadIcono, 'PNG', xPosition, currentY - 5, 5, 5);
        xPosition += 6;
    }

    currentY += 20;

    // Ingredientes
    doc.text("Ingredientes:", 10, currentY);
    doc.setFontSize(14);
    doc.setFont("helvetica", "normal");
    currentY += 10;

    const ingredientsLines = doc.splitTextToSize(ingredients, textWidth);
    ingredientsLines.forEach(line => {
        if (currentY > pageHeight - 20) {
            doc.addPage();
            currentY = 20;
        }
        doc.text(line, 10, currentY);
        currentY += 7;
    });

    // Preparación
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    if (currentY > pageHeight - 20) {
        doc.addPage();
        currentY = 20;
    }
    doc.text("Preparación:", 10, currentY);
    currentY += 10;

    doc.setFontSize(14);
    doc.setFont("helvetica", "normal");
    const preparationLines = doc.splitTextToSize(preparation, textWidth);
    preparationLines.forEach(line => {
        if (currentY > pageHeight - 20) {
            doc.addPage();
            currentY = 20;
        }
        doc.text(line, 10, currentY);
        currentY += 7;
    });

    // Creado por
    doc.setFontSize(12);
    doc.setFont("helvetica", "italic");
    if (currentY > pageHeight - 20) {
        doc.addPage();
        currentY = 20;
    }
    doc.text(`Creado por: ${nick}`, 10, currentY);
}



function mostrarUsuarios(id) {
    document.getElementById('seccion-usuarios-' + id).style.display = 'block';
    document.getElementById('seccion-foros-' + id).style.display = 'none';
    document.getElementById('btn-usuarios-' + id).classList.add('activo');
    document.getElementById('btn-foros-' + id).classList.remove('activo');
    document.getElementById('buscador-compartir').placeholder = "Buscar por nick...";
}

function mostrarForos(id) {
    document.getElementById('seccion-usuarios-' + id).style.display = 'none';
    document.getElementById('seccion-foros-' + id).style.display = 'block';
    document.getElementById('btn-usuarios-' + id).classList.remove('activo');
    document.getElementById('btn-foros-' + id).classList.add('activo');
    document.getElementById('buscador-compartir').placeholder = "Buscar por título...";
}

