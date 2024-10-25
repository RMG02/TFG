function initModal() {
    var modal = document.getElementById("myModal");
    var botonIni = document.getElementsByClassName("botonInit");
    var span = document.getElementsByClassName("close");
    var botonCancel = document.getElementsByClassName("cancelAction");

    for (let i = 0; i < botonIni.length; i++) {
        botonIni[i].onclick = function() {
            modal.style.display = "block";
        };

        span[i].onclick = function() {
            modal.style.display = "none";
        };

        botonCancel[i].onclick = function(){
            modal.style.display = "none";
        };
    }
    

}

window.onload = initModal;
