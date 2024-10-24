function initModal() {
    var modal = document.getElementById("myModal");
    var boton = document.getElementsByClassName("botonInit");
    var span = document.getElementsByClassName("close");

    for (let i = 0; i < boton.length; i++) {
        boton[i].onclick = function() {
            modal.style.display = "block";
        };

        span[i].onclick = function() {
            modal.style.display = "none";
        };
    }
    

}

window.onload = initModal;
