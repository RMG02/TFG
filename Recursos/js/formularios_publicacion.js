function form_publi(){
    
    var boton_publica = document.getElementById("publicaBtn");
    var spans = document.getElementsByClassName("close");

    boton_publica.onclick = function() {
        var modal = document.getElementById("formPublicacion");
        modal.style.display = "block";
    };

    for (let i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
            var modal = spans[i].closest(".modal"); 
            modal.style.display = "none";
        };
    }
    
    document.querySelector('input[name="image"]').addEventListener('change', function (e) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
        const file = e.target.files[0];
    
        if (file && !allowedTypes.includes(file.type)) {
            alert("Solo puedes subir imágenes o videos.");
            e.target.value = ""; // Reinicia el input
        }
    });


}

document.addEventListener("DOMContentLoaded", form_publi);
