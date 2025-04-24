const notificacion = document.querySelector('.notificacion');

if (notificacion) {
    let hideTimeout, removeTimeout;

    const cerrarNotificacion = () => {
        if (!notificacion.classList.contains('noti-active')) return;

        clearTimeout(hideTimeout);
        notificacion.classList.remove('noti-active');

        clearTimeout(removeTimeout);
        removeTimeout = setTimeout(() => {
            notificacion.remove();
        }, 600);
    };

    notificacion.classList.add('noti-active');

    hideTimeout = setTimeout(() => {
        cerrarNotificacion();
    }, 5000);

    notificacion.addEventListener('click', cerrarNotificacion);
}