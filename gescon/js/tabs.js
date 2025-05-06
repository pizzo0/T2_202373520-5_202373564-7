const tabBtns = document.querySelectorAll('.tab-btn');
const tabs = document.querySelectorAll('.tab');

tabBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
        const target = btn.getAttribute('data-target');

        tabBtns.forEach((b) => b.classList.remove('tab-btn-activo'));
        tabs.forEach((t) => t.classList.remove('tab-activo'));

        btn.classList.add('tab-btn-activo');
        document.getElementById(target).classList.add('tab-activo');

        sessionStorage.setItem('tab-guardado', target);
    });
});

const ultimoTab = sessionStorage.getItem('tab-guardado');
const ultimoBtn = document.querySelector(`.tab-btn[data-target="${ultimoTab}"]`);
if (ultimoBtn) {
    ultimoBtn.click();
} else {
    tabBtns[0]?.click();
}