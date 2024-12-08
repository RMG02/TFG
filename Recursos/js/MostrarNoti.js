document.addEventListener('DOMContentLoaded', function() {
    const notificaciones = document.querySelectorAll('.notificacion');

    window.mostrarTodas = function() {
        notificaciones.forEach(noti => {
            noti.style.display = 'block';
        });
    }

    window.mostrarNoVistas = function() {
        notificaciones.forEach(noti => {
            if (noti.classList.contains('no-vista')) {
                noti.style.display = 'block';
            } else {
                noti.style.display = 'none';
            }
        });
    }

    window.marcarComoVista = function(id) {
        const notificacion = document.querySelector('.notificacion[data-id="' + id + '"]');
        if (notificacion) {
            notificacion.classList.remove('no-vista');
            notificacion.classList.add('vista');
            notificacion.querySelector('button').remove(); // Remove the button
            // Aquí puedes hacer una llamada a tu backend para actualizar el estado en la base de datos
            // Ejemplo:
            // fetch('/ruta/a/tu/backend', {
            //     method: 'POST',
            //     body: JSON.stringify({ id: id, vista: true }),
            //     headers: { 'Content-Type': 'application/json' }
            // });
        }
    }
});
