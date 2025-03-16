function filtrarUsuarios() {
    var busqueda, filas, i, nick;
    busqueda = document.getElementById('buscador').value.toLowerCase();
    filas = document.getElementById("userList").getElementsByTagName("tr");

    for (i = 1; i < filas.length; i++) {  
        nick = filas[i].getElementsByTagName("td")[2].textContent.toLowerCase(); 
        if (nick.startsWith(busqueda)) {
            filas[i].style.display = "";
        }
        else{
            filas[i].style.display = "none";
        }
               
    }
}
