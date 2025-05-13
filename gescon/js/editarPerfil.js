const menuPerfil = document.getElementsByClassName("menu-perfil")[0];
const overlay = document.getElementsByClassName('menu-overlay')[0];
const btns = document.querySelectorAll("#editar-perfil-btn");

const formNombreEmail = document.getElementsByClassName('nombre-email-form')[0];
const formPass = document.getElementsByClassName('pass-form')[0];

const toggleFormBtn = document.getElementById('toggle-editar-perfil');

const clickFuera = (e) => {
    if (!menuPerfil.contains(e.target)) {
        menuPerfil.classList.remove('menu-perfil-activo');
        overlay.classList.remove('menu-overlay-activo');
        document.removeEventListener('click', clickFuera);
        if (formPass.classList.contains('form-activo')) {
            cambiarPass();
        }
    }
}

const toggleMP = () => {
    const estaActivo = menuPerfil.classList.toggle('menu-perfil-activo');
    overlay.classList.toggle('menu-overlay-activo', estaActivo);

    if (estaActivo) {
        setTimeout(() => {
            document.addEventListener('click', clickFuera);
        }, 0);
    } else {
        document.removeEventListener('click', clickFuera)
        if (formPass.classList.contains('form-activo')) {
            cambiarPass();
        }
    }
}

btns.forEach(btn => {
    btn.addEventListener('click', () => toggleMP());
});

const cambiarPass = () => {
    const cambiandoPass = formPass.classList.toggle('form-activo');
    formNombreEmail.classList.toggle('form-activo');
    
    if (cambiandoPass) {
        toggleFormBtn.innerHTML = 'Volver atras';
    } else {
        toggleFormBtn.innerHTML = 'Cambiar contraseña';
    }
}

toggleFormBtn.addEventListener('click', cambiarPass);

const btnEliminarCuenta = document.querySelector('.modalBtnEliminarCuenta');
btnEliminarCuenta.addEventListener('click', () => btns[0].click());

const btnCancelarEliminarCuenta = document.querySelector('#cerrar-eliminar-cuenta');
btnCancelarEliminarCuenta.addEventListener('click', () => btnEliminarCuenta.click());