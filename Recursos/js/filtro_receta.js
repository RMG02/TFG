var buscarPor = 'texto'; // Por defecto buscar por texto
var tipoFiltroActual = "btnFiltrarTodos"; // Por defecto, sin filtros
var DifiActual = null;
var contFiltrosTipo = 0;
var contFiltroDifi = 0;

document.getElementById("filtroBtn").addEventListener("click", function() {
    var menu = document.getElementById("menuFiltro");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
});


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
}

function filtrarPublicaciones() {
    var busqueda, publicaciones, i, nick, texto;
    
    busqueda = document.getElementById('buscador').value.toLowerCase();
    publicaciones = document.getElementsByClassName("tweetrecetas");
    
    for (i = 0; i < publicaciones.length; i++) {
        nick = publicaciones[i].getElementsByTagName("strong")[0].textContent.toLowerCase();
        texto = publicaciones[i].getElementsByTagName("p")[0].getElementsByTagName("strong")[0].textContent.toLowerCase();
        tipo_receta = publicaciones[i].getElementsByClassName("tweet-tipo")[0].textContent;
        difi_receta = parseInt(publicaciones[i].getElementsByClassName("dificultad_receta")[0].textContent);

        var cumpleBusqueda = (buscarPor === "nick" && nick.startsWith(busqueda)) || (buscarPor === "texto" && texto.includes(busqueda));

        var cumpleTipo = (contFiltrosTipo === 0 || (tipoFiltroActual === tipo_receta));

        var cumpleDifi = (contFiltroDifi === 0 || (DifiActual === difi_receta));

        publicaciones[i].style.display = (cumpleBusqueda && cumpleTipo && cumpleDifi) ? "" : "none";
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
                    var fechaTextoA = a.getElementsByClassName("tweetrecetas-time")[0].textContent.trim();
                    var fechaTextoB = b.getElementsByClassName("tweetrecetas-time")[0].textContent.trim();

                    var fechaA = convertirFecha(fechaTextoA);
                    var fechaB = convertirFecha(fechaTextoB);

                    return fechaB - fechaA; 

            case 'btnOrdenarFechaAsc':
                    var fechaTextoA = a.getElementsByClassName("tweetrecetas-time")[0].textContent.trim();
                    var fechaTextoB = b.getElementsByClassName("tweetrecetas-time")[0].textContent.trim();

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
            case 'btnOrdenarTiempoDesc':
                    var tiempoA = parseInt(a.getElementsByClassName("tiempo_receta")[0].textContent.trim());
                    var tiempoB = parseInt(b.getElementsByClassName("tiempo_receta")[0].textContent.trim());

                    return tiempoB - tiempoA;
            case 'btnOrdenarTiempoAsc':
                    var tiempoA = parseInt(a.getElementsByClassName("tiempo_receta")[0].textContent.trim());
                    var tiempoB = parseInt(b.getElementsByClassName("tiempo_receta")[0].textContent.trim());

                    return tiempoA - tiempoB;
            case 'btnOrdenarDifiDesc':
                    var dificultadA = a.getElementsByClassName("dificultad_receta")[0].textContent.trim();
                    var dificultadB = b.getElementsByClassName("dificultad_receta")[0].textContent.trim();

                    return dificultadB - dificultadA;
            case 'btnOrdenarDifiAsc':
                    var dificultadA = a.getElementsByClassName("dificultad_receta")[0].textContent.trim();
                    var dificultadB = b.getElementsByClassName("dificultad_receta")[0].textContent.trim();

                    return dificultadA - dificultadB;
        }
    });

    var contenedor = document.getElementById("publicaciones");
    publicaciones.forEach(publi => contenedor.appendChild(publi));

    var opciones = document.getElementsByClassName("opciones_orden");

    for(var i = 0; i < opciones.length; i++){
        opciones[i].style.display = "none";
    }
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

function mostrarTipos() {
    var opciones = document.getElementById("opcionesFiltroTipo");
    opciones.style.display = opciones.style.display === "block" ? "none" : "block";
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

function mostrarOrdenTiempo() {
    var opciones = document.getElementById("opcionesOrdenTiempo");
    opciones.style.display = opciones.style.display === "block" ? "none" : "block";
}

function mostrarOrdenDifi() {
    var opciones = document.getElementById("opcionesOrdenDifi");
    opciones.style.display = opciones.style.display === "block" ? "none" : "block";
}

function mostrarFiltroDifi() {
    var opciones = document.getElementById("opcionesFiltroDifi");
    opciones.style.display = opciones.style.display === "block" ? "none" : "block";
}

function filtrarPorTipo(tipo) {
    var id = "btnFiltrar" + tipo
    var botonSinFiltros = document.getElementById('btnFiltrarTodos');
    var botonesFiltroTipos = document.getElementById('opcionesFiltroTipo').getElementsByTagName('button');
    var botonesFiltroDifi = document.getElementById('opcionesFiltroDifi').getElementsByTagName('button');
    var publicaciones;
    
    publicaciones = document.getElementsByClassName("tweetrecetas");

    if(tipoFiltroActual === "btnFiltrarTodostipo" || tipo === "btnFiltrarTodos"){
        tipoFiltroActual = tipo;
        botonSinFiltros.classList.add('activo');

        for(var i = 0; i < botonesFiltroTipos.length; i++){
            botonesFiltroTipos[i].classList.remove('activo');
        }

        for(var i = 0; i < botonesFiltroDifi.length; i++){
            botonesFiltroDifi[i].classList.remove('activo');
        }

        for (var i = 0; i < publicaciones.length; i++) {
            publicaciones[i].style.display = "";  
        }

        contFiltrosTipo = 0;
        contFiltroDifi = 0;
    }
    else {
        botonSinFiltros.classList.remove('activo');
        if(document.getElementById(id).classList.contains('activo')){
            document.getElementById(id).classList.remove('activo');

            if(!Number.isInteger(tipo)){
                contFiltrosTipo -= 1;
            }
            else{
                contFiltroDifi -= 1;
            }
        }
        else{
            document.getElementById(id).classList.add('activo');
            if(!Number.isInteger(tipo)){
                tipoFiltroActual = tipo;
                contFiltrosTipo += 1;

                if(contFiltrosTipo === 2){
                    for(var i = 0; i < botonesFiltroTipos.length; i++){
                        if( botonesFiltroTipos[i].id === id){
                            continue;
                        }
                        if(botonesFiltroTipos[i].classList.contains('activo')){
                            botonesFiltroTipos[i].classList.remove('activo');
                            contFiltrosTipo -= 1;
                        }
                    }
                }

                
            }
            else{
                DifiActual = tipo;
                contFiltroDifi += 1;
                if(contFiltroDifi === 2){
                    for(var i = 0; i < botonesFiltroDifi.length; i++){
                        if( botonesFiltroDifi[i].id === id){
                            continue;
                        }
                        if(botonesFiltroDifi[i].classList.contains('activo')){
                            botonesFiltroDifi[i].classList.remove('activo');
                            contFiltroDifi -= 1;
                        }
                    }
                }
                
            }
        }

        for (i = 0; i < publicaciones.length; i++) {
            if(contFiltroDifi === 1){
                difi_receta = parseInt(publicaciones[i].getElementsByClassName("dificultad_receta")[0].textContent);
            }
            else{
                difi_receta = null;
            }

            if(contFiltrosTipo === 1){
                tipo_receta = publicaciones[i].getElementsByClassName("tweet-tipo")[0].textContent;
            }
            else{
                tipo_receta = null;
            }

            if(tipo_receta !== null && difi_receta !== null){
                if ((tipoFiltroActual === tipo_receta) && (difi_receta === DifiActual)){
                    publicaciones[i].style.display = ""; 
                }
                else{
                    publicaciones[i].style.display = "none"; 
                }
            }
            else if(tipo_receta !== null && difi_receta === null){
                if (tipoFiltroActual === tipo_receta){
                    publicaciones[i].style.display = ""; 
                }
                else{
                    publicaciones[i].style.display = "none"; 
                }
            }
            else{
                if (difi_receta === DifiActual){
                    publicaciones[i].style.display = ""; 
                }
                else{
                    publicaciones[i].style.display = "none"; 
                }
            }

        }

    }

    
    var opciones = document.getElementsByClassName("opciones_filtro_difi");

    for(var i = 0; i < opciones.length; i++){
        opciones[i].style.display = "none";
    }

    var opciones = document.getElementsByClassName("opciones_filtro_tipo");

    for(var i = 0; i < opciones.length; i++){
        opciones[i].style.display = "none";
    }

}
