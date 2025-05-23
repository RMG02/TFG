document.getElementById("filtroBtn").addEventListener("click", function() {
    var menu = document.getElementById("menuFiltro");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
});

var buscarPor = 'texto'; // Por defecto buscar por texto

function setBuscarPor(tipo) {
    document.getElementById('btnBuscarNick').classList.remove('activo');
    document.getElementById('btnBuscarTexto').classList.remove('activo');

    if (tipo === 'nick') {
        document.getElementById('btnBuscarNick').classList.add('activo');
    } else if (tipo === 'texto') {
        document.getElementById('btnBuscarTexto').classList.add('activo');
    }

    buscarPor = tipo;
    document.getElementById("buscador").value = ''; 
    filtrarPublicaciones(); 
}

function filtrarPublicaciones() {
    var busqueda, publicaciones, i, nick, texto;
    
    busqueda = document.getElementById('buscador').value.toLowerCase();
    
    publicaciones = document.getElementsByClassName("tweet");

    for (i = 0; i < publicaciones.length; i++) {
        nick = publicaciones[i].getElementsByTagName("strong")[0].textContent.toLowerCase();
        texto = publicaciones[i].getElementsByTagName("p")[0].textContent.toLowerCase();

        if (buscarPor === "nick" && nick.startsWith(busqueda)) {
            publicaciones[i].style.display = ""; 
        } else if (buscarPor === "texto" && texto.includes(busqueda)) {
            publicaciones[i].style.display = ""; 
        } else {
            publicaciones[i].style.display = "none"; 
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



// Función para ordenar las publicaciones
function ordenarPublicaciones(criterio) {

    var botonesOrden = document.getElementById('menuFiltro').getElementsByTagName('button');
    for (var i = 0; i < botonesOrden.length; i++) {
        if(botonesOrden[i].id === "btnBuscarNick" || botonesOrden[i].id === "btnBuscarTexto"){
            continue;
        }

        botonesOrden[i].classList.remove('activo');

        if(botonesOrden[i].id === criterio){
            botonesOrden[i].classList.add('activo');
        }
    }

    var publicaciones = Array.from(document.getElementById("publicaciones").getElementsByClassName("contenedor-publicacion"));
    publicaciones.sort((a, b) => {
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
            case 'btnOrdenarLikesDesc':
                    var likesA = parseInt(a.getElementsByClassName("btn-like")[0].textContent.trim());
                    var likesB = parseInt(b.getElementsByClassName("btn-like")[0].textContent.trim());
                
                    return likesB - likesA; 
            case 'btnOrdenarLikesAsc':
                
                    var likesA = parseInt(a.getElementsByClassName("btn-like")[0].textContent.trim());
                    var likesB = parseInt(b.getElementsByClassName("btn-like")[0].textContent.trim());
                
                    return likesA - likesB; 
              
            case 'btnOrdenarDislikesDesc':
                    var dislikesA = parseInt(a.getElementsByClassName("btn-dislike")[0].textContent.trim());
                    var dislikesB = parseInt(b.getElementsByClassName("btn-dislike")[0].textContent.trim());
                
                    return dislikesB - dislikesA; 
            case 'btnOrdenarDislikesAsc':
                    var dislikesA = parseInt(a.getElementsByClassName("btn-dislike")[0].textContent.trim());
                    var dislikesB = parseInt(b.getElementsByClassName("btn-dislike")[0].textContent.trim());
                
                    return dislikesA - dislikesB;
        }
    });


    var contenedor = document.getElementById("publicaciones");
    publicaciones.forEach(publi => contenedor.appendChild(publi));

    var opciones = document.getElementsByClassName("opciones_orden");
    
    for(var i = 0; i < opciones.length; i++){
        opciones[i].style.display = "none";
    }
}

function mostrarOrdenFechas() {
    var opciones = document.getElementById("opcionesOrdenFechas");
    opciones.style.display = opciones.style.display === "block" ? "none" : "block";
}

function mostrarOrdenLikes() {
    var opciones = document.getElementById("opcionesOrdenLikes");
    opciones.style.display = opciones.style.display === "block" ? "none" : "block";
}

function mostrarOrdenDislikes() {
    var opciones = document.getElementById("opcionesOrdenDislikes");
    opciones.style.display = opciones.style.display === "block" ? "none" : "block";
}

function mostrarSeccion(id) {
    var contenido = document.getElementsByClassName('contenido-seccion');
    for (var i = 0; i < contenido.length; i++) {
        contenido[i].classList.remove('activo');   
    }

    document.getElementById(id).classList.add("activo");

    var seccion = document.getElementsByClassName("seccion");
    for (var i = 0; i < seccion.length; i++) {
        seccion[i].classList.remove("activo");
    }

    var botones = document.getElementsByClassName("secciones").getElementsByTagName("button"); 
    for (var i = 0; i < botones.length; i++) {
        if (botones[i].getAttribute("onclick") === `mostrarSeccion('${id}')`) {
            botones[i].classList.add("activo");
            break;
        }
    }
}

