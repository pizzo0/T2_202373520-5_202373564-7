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