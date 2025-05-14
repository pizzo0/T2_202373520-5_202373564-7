const toggleNavBtns = document.querySelectorAll('#mostrar-nav');
const nav = document.getElementById('nav');
const btnSideNav = document.getElementById('nav-close');
const navOptions = Array.from(document.querySelectorAll('.nav-option')).slice(0,-1);


let navActivo = false;

toggleNavBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
        navActivo = nav.classList.toggle('nav-activo');
        document.body.classList.toggle('no-scroll');
    })
});

let x_inicial = 0;
let x_curr = 0;
let x_diff = 0;
let deslizandoNav = false;

nav.addEventListener('touchstart', (e) => {
    if (window.innerWidth > 900) return;
    
    x_diff = 0;
    x_inicial = e.touches[0].clientX;
    deslizandoNav = true;
});

nav.addEventListener('touchmove', (e) => {
    if (window.innerWidth > 900) return;

    if(!deslizandoNav) return;

    x_curr = e.touches[0].clientX;
    x_diff = x_curr - x_inicial;

    if (x_diff > 0) {
        nav.style.transform = `translateX(${x_diff}px)`;
    }
});

nav.addEventListener('touchend', () => {
    if (window.innerWidth > 900) return;

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


let __x_inicial = 0;
let __x_curr = 0;
let __x_diff = 0;
let __abriendoNav = false;

document.body.addEventListener('touchstart', (e) => {
    if (window.innerWidth > 900) return;
    __x_diff = 0;
    __x_inicial = e.touches[0].clientX;
    __abriendoNav = true;
});

document.body.addEventListener('touchmove', (e) => {
    if (window.innerWidth > 900 || !__abriendoNav) return;
    __x_curr = e.touches[0].clientX;
    __x_diff = __x_curr - __x_inicial;
});

document.body.addEventListener('touchend', () => {
    if (window.innerWidth > 900) return;

    __abriendoNav = false;

    if (__x_diff < -150 && !navActivo) {
        toggleNavBtns[0].click();
    }
});

window.addEventListener('DOMContentLoaded', () => {
    const navState = localStorage.getItem('navState');
    if (navState === 'closed') {
        nav.classList.add('nav-closed');
    } else {
        nav.classList.remove('nav-closed');
    }
});

let navOptionActive = localStorage.getItem('navState') === 'closed' ? true : false;

btnSideNav.addEventListener('click', () => {
    if (nav.classList.contains('nav-closed')) {
        nav.classList.remove('nav-closed');
        navOptionActive = false;
        localStorage.setItem('navState', 'open');
    } else {
        nav.classList.add('nav-closed');
        navOptionActive = true;
        localStorage.setItem('navState', 'closed');
    }
});

navOptions.forEach((navOpt) => {
    navOpt.addEventListener('mouseenter', () => {
        if (navOptionActive && nav.classList.contains('nav-closed')) {
            nav.classList.remove('nav-closed');
        }
    });
    navOpt.addEventListener('mouseleave', () => {
        if (navOptionActive && !nav.classList.contains('nav-closed')) {
            nav.classList.add('nav-closed');
        }
    });
});