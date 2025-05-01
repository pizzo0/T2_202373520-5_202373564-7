setTimeout(() => {
    const modalBtns = document.querySelectorAll('#modalBtn');
    
    modalBtns.forEach((b) => {
        b.addEventListener('click', () => {
            const target = b.getAttribute('data-target');
            const modal = document.getElementById(target);
            const overlay = document.querySelector(`[data-overlay-target=${target}]`);
    
            if (!overlay.dataset.listenerAdded) {
                overlay.addEventListener('click', () => {
                    b.click();
                });
                overlay.dataset.listenerAdded = 'true';
            }
    
            modal.classList.toggle('modal-activo');
            overlay.classList.toggle('menu-overlay-activo');
        });
    });
}, 100);