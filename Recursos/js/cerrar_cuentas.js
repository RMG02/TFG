function cerrar() {
    var botones = document.getElementsByClassName("botonInit");
    var spans = document.getElementsByClassName("close");

    for (let i = 0; i < botones.length; i++) {
        botones[i].onclick = function() {
            var modal = document.getElementById(i);
            modal.style.display = "block";
        };

        spans[i].onclick = function() {
            var modal = document.getElementById(i);
            modal.style.display = "none";
        };
    }

}

window.onload = cerrar;
