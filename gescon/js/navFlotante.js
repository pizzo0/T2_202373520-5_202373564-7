document.querySelectorAll('.nav-flotante-btn').forEach(b => {
    const cb = b.querySelector('.nav-flotante-btn-content');
    const target = b.getAttribute('data-target');
    const modal = target ? document.getElementById(target) : null;

    const expandirBoton = () => {
        const fitContentBtn = cb.scrollWidth + parseFloat(getComputedStyle(b).paddingLeft) * 2;
        b.style.width = fitContentBtn + "px";
        b.classList.add('nav-flotante-btn-expandido');
    };

    const colapsarBoton = () => {
        b.style.width = `65px`;
        b.classList.remove('nav-flotante-btn-expandido');
    };

    b.addEventListener('mouseenter', expandirBoton);

    b.addEventListener('mouseleave', () => {
        if (!modal) return colapsarBoton();
        if (modal.classList.contains('modal-activo')) return;

        colapsarBoton();
    });

    if (modal) {
        const observer = new MutationObserver(() => {
            if (!modal.classList.contains('modal-activo')) {
                colapsarBoton();
            }
        });
        observer.observe(modal, { attributes: true, attributeFilter: ['class'] });
    }
});