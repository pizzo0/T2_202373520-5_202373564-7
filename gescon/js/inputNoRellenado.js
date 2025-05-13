document.addEventListener('DOMContentLoaded', () => {
    const formularios = document.querySelectorAll('.formulario');

    formularios.forEach((form) => {
        const campos = form.querySelectorAll('input, textarea');

        campos.forEach((campo) => {
        let errorDiv = campo.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('error')) {
            errorDiv = document.createElement('div');
            errorDiv.classList.add('error');
            errorDiv.textContent =
            campo.type === 'email'
                ? 'Debes ingresar un email válido'
                : 'Debes rellenar esta casilla';
            campo.insertAdjacentElement('afterend', errorDiv);
        }

        campo.addEventListener('blur', () => {
            if (!campo.checkValidity()) {
            errorDiv.style.display = 'block';
            } else {
            errorDiv.style.display = 'none';
            }
        });

        campo.addEventListener('input', () => {
            errorDiv.style.display = 'none';
        });
        });

        form.addEventListener('submit', (e) => {
        let valido = true;
        campos.forEach((campo) => {
            const errorDiv = campo.nextElementSibling;
            if (!campo.checkValidity()) {
            errorDiv.style.display = 'block';
            valido = false;
            }
        });

        if (!valido) {
            e.preventDefault();
        }
        });
    });
});

limpiarRut = (rut) => {
    return rut.replace(/[^0-9kK]/g, '').toUpperCase();
}

formatearRut = (rutLimpio) => {
    let cuerpo = rutLimpio.slice(0, -1);
    let dv = rutLimpio.slice(-1);

    let aux = '';
    let i = 0;
    for (let pos = cuerpo.length - 1; pos >= 0; pos--) {
        aux = cuerpo[pos] + aux;
        i++;
        if (i % 3 === 0 && pos !== 0) {
            aux = '.' + aux;
        }
    }
    return `${aux}-${dv}`;
}

const inputRut = document.getElementById('rut');
if (inputRut) {
    inputRut.setAttribute('maxlength','12');
    inputRut.addEventListener('input', function () {
        let rut = limpiarRut(this.value);

        if (rut.length > 1) {
            this.value = formatearRut(rut);
        } else {
            this.value = rut;
        }
    });
}