const toggleNavBtns = document.querySelectorAll('#mostrar-nav');
const nav = document.getElementById('nav');

toggleNavBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
        nav.classList.toggle('nav-activo');
    })
});

let x_inicial = 0;
let x_curr = 0;
let x_diff = 0;
let deslizandoNav = false;

nav.addEventListener('touchstart', (e) => {
    x_diff = 0;
    x_inicial = e.touches[0].clientX;
    deslizandoNav = true;
});

nav.addEventListener('touchmove', (e) => {
    if(!deslizandoNav) return;

    x_curr = e.touches[0].clientX;
    x_diff = x_curr - x_inicial;

    console.log(x_diff);

    if (x_diff > 0) {
        nav.style.transform = `translateX(${x_diff}px)`;
    }
});

nav.addEventListener('touchend', () => {
    deslizandoNav = false;

    if (x_diff > 50) {
        toggleNavBtns[0].click();
        setTimeout(() => {
            nav.style.transform = `translateX(0)`;
        }, 300);
    } else {
        nav.style.transform = `translateX(0)`;
        
    }
    
});