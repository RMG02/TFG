function filtrarUsuarios() {
    var busqueda, filas, i, email;
    busqueda = document.getElementById('buscador').value.toLowerCase();
    filas = document.getElementById("userList").getElementsByTagName("tr");

    for (i = 1; i < filas.length; i++) {  
        email = filas[i].getElementsByTagName("td")[1].textContent.toLowerCase(); 
        if (email.startsWith(busqueda)) {
            filas[i].style.display = "";
        }
        else{
            filas[i].style.display = "none";
        }
               
    }
}
