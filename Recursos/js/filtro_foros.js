document.getElementById("filtroBtn").addEventListener("click", function() {
    var menu = document.getElementById("menuFiltro");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
});

var buscarPor = 'texto'; 


function filtrarForos() {
    var busqueda, i, texto;
    
    busqueda = document.getElementById('buscador').value.toLowerCase();
    
    foros = document.getElementsByClassName("foro-link");

    for (i = 0; i < foros.length; i++) {
        texto = foros[i].getElementsByTagName("h3")[0].textContent.toLowerCase();

        if (texto.includes(busqueda)) {
            foros[i].style.display = ""; 
        } else {
            foros[i].style.display = "none"; 
        }
    }
}

function convertirFecha(fechaTexto) {

    var match = fechaTexto.match(/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})$/);
    
    if (!match) {
        console.warn("Fecha inválida:", fechaTexto);
        return new Date(0); 
    }

    var dia = match[1];
    var mes = match[2]; 
    var año = match[3];
    var horas = match[4];
    var minutos = match[5];
    var segundos = match[6];

    return new Date(año, mes - 1, dia, horas, minutos, segundos);
}


function ordenarForos(criterio) {

    var botonesOrden = document.getElementById('menuFiltro').getElementsByTagName('button');
    for (var i = 0; i < botonesOrden.length; i++) {

        botonesOrden[i].classList.remove('activo');

        if(botonesOrden[i].id === criterio){
            botonesOrden[i].classList.add('activo');
        }
    }

    var foros = Array.from(document.getElementById("foros").getElementsByClassName("contenedor-foros"));
    foros.sort((a, b) => {
        switch(criterio) {
            case 'btnOrdenarFechaDesc':
                    var fechaTextoA = a.getElementsByClassName("tweet-time")[0].textContent.trim();
                    var fechaTextoB = b.getElementsByClassName("tweet-time")[0].textContent.trim();


                    var fechaA = convertirFecha(fechaTextoA);
                    var fechaB = convertirFecha(fechaTextoB);

                    return fechaB - fechaA; 

            case 'btnOrdenarFechaAsc':
                    var fechaTextoA = a.getElementsByClassName("tweet-time")[0].textContent.trim();
                    var fechaTextoB = b.getElementsByClassName("tweet-time")[0].textContent.trim();


                    var fechaA = convertirFecha(fechaTextoA);
                    var fechaB = convertirFecha(fechaTextoB);

                    return fechaA - fechaB;
            case 'btnOrdenarSusDesc':
                    var suscriptoresA = parseInt(a.getElementsByClassName("comentarios-icon")[0].textContent.trim());
                    var suscriptoresB = parseInt(b.getElementsByClassName("comentarios-icon")[0].textContent.trim());
                
                    return suscriptoresB - suscriptoresA; 
            case 'btnOrdenarSusAsc':
                
                    var suscriptoresA = parseInt(a.getElementsByClassName("comentarios-icon")[0].textContent.trim());
                    var suscriptoresB = parseInt(b.getElementsByClassName("comentarios-icon")[0].textContent.trim());
                        
                    return suscriptoresA - suscriptoresB; 
        }
    });


    var contenedor = document.getElementById("foros");
    foros.forEach(foro => contenedor.appendChild(foro));

    var opciones = document.getElementsByClassName("opciones_orden");
    
    for(var i = 0; i < opciones.length; i++){
        opciones[i].style.display = "none";
    }
}

function mostrarOrdenFechas() {
    var opciones = document.getElementById("opcionesOrdenFechas");
    opciones.style.display = opciones.style.display === "block" ? "none" : "block";
}

function mostrarOrdenSus() {
    var opciones = document.getElementById("opcionesOrdenSus");
    opciones.style.display = opciones.style.display === "block" ? "none" : "block";
}




