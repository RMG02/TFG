function filtrarUsuarios() {
    var busqueda, tweets, i, email;
    busqueda = document.getElementById('buscador').value.toLowerCase();
    tweets = document.querySelectorAll("#publicaciones .tweet");

    for (i = 0; i < tweets.length; i++) {
        // Encuentra el elemento que contiene el email dentro del tweet
        email = tweets[i].querySelector(".tweet-header strong").textContent.toLowerCase();
        
        // Compara el email con la búsqueda
        if (email.startsWith(busqueda)) {
            tweets[i].style.display = ""; // Muestra el tweet si coincide
        } else {
            tweets[i].style.display = "none"; // Oculta el tweet si no coincide
        }
    }
}
