const toggleNavBtns = document.querySelectorAll('#mostrar-nav');
const nav = document.getElementById('nav');

toggleNavBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
        nav.classList.toggle('nav-activo');
    })
});