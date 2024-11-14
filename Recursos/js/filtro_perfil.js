function filtrarPerfil() {
    var busqueda, tweets, i;
    busqueda = document.getElementById('buscador').value.toLowerCase();
    tweets = document.getElementsByClassName("tweet");

    for (i = 0; i < tweets.length; i++) {
        texto = tweets[i].getElementsByTagName("p")[0].textContent.toLowerCase();
        
        if (texto.includes(busqueda)) {
            tweets[i].style.display = ""; // Muestra el tweet si coincide
        } else {
            tweets[i].style.display = "none"; // Oculta el tweet si no coincide
        }
    }
}
