const notificacion = document.querySelector('.notificacion');

if (notificacion) {
    let hideTimeout, removeTimeout;

    const cerrarNotificacion = () => {
        setTimeout(() => {
            
        })
        if (!notificacion.classList.contains('noti-active')) return;

        clearTimeout(hideTimeout);
        notificacion.classList.remove('noti-active');

        clearTimeout(removeTimeout);
        removeTimeout = setTimeout(() => {
            notificacion.remove();
        }, 600);
    };

    setTimeout(() => {
        notificacion.classList.add('noti-active');
    }, 100)

    hideTimeout = setTimeout(() => {
        cerrarNotificacion();
    }, 5000);

    notificacion.addEventListener('click', cerrarNotificacion);
}