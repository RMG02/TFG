function procesarNotificaciones(notificaciones) {
    let noVistas = 0;
    // Recorrer todas las notificaciones y contar las no vistas
    if (notificaciones.length > 0) {
        for (let i = 0; i < notificaciones.length; i++) {
            if (!notificaciones[i].vista) {
                noVistas++;
            }
        }
    }

    // Mostrar el contador de notificaciones si hay no vistas
    var notificationContador = document.getElementById('notification-counter');
    if (noVistas > 0) {
        notificationContador.textContent = noVistas;
        notificationContador.style.display = 'inline';
    } else {
        notificationContador.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
});
