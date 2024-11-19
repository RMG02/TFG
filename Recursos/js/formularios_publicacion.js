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
        const file = e.target.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
        const previewContainer = document.querySelector('.formulario');
    
        
        const oldPreview = document.querySelector('#preview');
        if (oldPreview) oldPreview.remove();
    
        if (file && allowedTypes.includes(file.type)) {
            const preview = document.createElement('div');
            preview.id = 'preview';
    
            
            if (file.type.startsWith('image')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                preview.appendChild(img);
            }
    
           
            if (file.type.startsWith('video')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                preview.appendChild(video);
            }
    
            previewContainer.appendChild(preview);
        } else {
            alert("El archivo no es válido.");
            e.target.value = ""; // Reinicia el input
        }
    });
    
    


}

document.addEventListener("DOMContentLoaded", form_publi);
