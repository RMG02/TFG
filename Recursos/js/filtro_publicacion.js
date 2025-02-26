document.getElementById("filtroBtn").addEventListener("click", function() {
    var menu = document.getElementById("menuFiltro");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
});

var buscarPor = 'texto'; // Por defecto buscar por texto

function setBuscarPor(tipo) {
    document.getElementById('btnBuscarNick').classList.remove('activo');
    document.getElementById('btnBuscarTexto').classList.remove('activo');

    // Ahora añadimos la clase 'active' al botón que se ha clickeado
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

    var publicaciones = Array.from(document.getElementsByClassName("tweet"));
    var publicacionesOrdenadas;
    
    switch(criterio) {
        case 'btnOrdenarFechaDesc':
            publicacionesOrdenadas = publicaciones.sort((a, b) => {
                var fechaTextoA = a.getElementsByClassName("tweet-time")[0].textContent.trim();
                var fechaTextoB = b.getElementsByClassName("tweet-time")[0].textContent.trim();


                var fechaA = convertirFecha(fechaTextoA);
                var fechaB = convertirFecha(fechaTextoB);

                return fechaB - fechaA; 
            });
            break;

        case 'btnOrdenarFechaAsc':
            publicacionesOrdenadas = publicaciones.sort((a, b) => {
                var fechaTextoA = a.getElementsByClassName("tweet-time")[0].textContent.trim();
                var fechaTextoB = b.getElementsByClassName("tweet-time")[0].textContent.trim();


                var fechaA = convertirFecha(fechaTextoA);
                var fechaB = convertirFecha(fechaTextoB);

                return fechaA - fechaB;
            });
            break;
        case 'btnOrdenarLikesDesc':
            publicacionesOrdenadas = publicaciones.sort((a, b) => {
                var likesA = parseInt(a.getElementsByClassName("btn-like")[0].textContent.trim());
                var likesB = parseInt(b.getElementsByClassName("btn-like")[0].textContent.trim());
            
                return likesB - likesA; 
            });
            break;
        case 'btnOrdenarLikesAsc':
            publicacionesOrdenadas = publicaciones.sort((a, b) => {
                var likesA = parseInt(a.getElementsByClassName("btn-like")[0].textContent.trim());
                var likesB = parseInt(b.getElementsByClassName("btn-like")[0].textContent.trim());
            
                return likesA - likesB; 
            });
            break;
        case 'btnOrdenarDislikesDesc':
            publicacionesOrdenadas = publicaciones.sort((a, b) => {
                var dislikesA = parseInt(a.getElementsByClassName("btn-dislike")[0].textContent.trim());
                var dislikesB = parseInt(b.getElementsByClassName("btn-dislike")[0].textContent.trim());
            
                return dislikesB - dislikesA; 
            });
            break;
        case 'btnOrdenarDislikesAsc':
            publicacionesOrdenadas = publicaciones.sort((a, b) => {
                var dislikesA = parseInt(a.getElementsByClassName("btn-dislike")[0].textContent.trim());
                var dislikesB = parseInt(b.getElementsByClassName("btn-dislike")[0].textContent.trim());
            
                return dislikesA - dislikesB; 
            });
            break;
        
    }

    var contenedor = document.getElementById("publicaciones");
    contenedor.innerHTML = ''; 
    publicacionesOrdenadas.forEach(publicacion => {
        contenedor.appendChild(publicacion); 
    });
}


