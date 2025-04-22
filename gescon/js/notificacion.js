const notificacion = document.querySelector('.notificacion');
if (notificacion) {
    notificacion.classList.add('noti-active')
    setTimeout(() => {
        notificacion.classList.remove('noti-active')
            setTimeout(() => {
                notificacion.remove()
            }, 6000);
    }, 5000);
}