setTimeout(() => {
    const modalBtns = document.querySelectorAll('#modalBtn');
    
    modalBtns.forEach((b) => {
        const target = b.getAttribute('data-target');
        const modal = document.getElementById(target);
        const overlay = document.querySelector(`[data-overlay-target=${target}]`);
        const bClose = document.querySelectorAll(`[data-close-target=${target}]`);

        b.addEventListener('click', () => {
            if (!overlay.dataset.listenerAdded) {
                overlay.addEventListener('click', () => {
                    modal.classList.toggle('modal-activo');
                    overlay.classList.toggle('menu-overlay-activo');
                    document.body.classList.toggle('no-scroll');
                });
                overlay.dataset.listenerAdded = 'true';
            }
    
            modal.classList.toggle('modal-activo');
            overlay.classList.toggle('menu-overlay-activo');
            document.body.classList.toggle('no-scroll');
        });

        bClose.forEach(c => {
            c.addEventListener('click', () => {
                modal.classList.remove('modal-activo');
                overlay.classList.remove('menu-overlay-activo');
                document.body.classList.remove('no-scroll');
            });
        });
    });
}, 0);