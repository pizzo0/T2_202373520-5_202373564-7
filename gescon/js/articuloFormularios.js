crearRevisionPreview = (formulario,index,email,id_rol) => {
    const calidad = formulario.calidad;
    const originalidad = formulario.originalidad;
    const valoracion = formulario.valoracion;
    const argumentos = formulario.argumentos_valoracion;
    const comentarios = formulario.comentarios;

    const previewFormulario = document.createElement('div');
    previewFormulario.className = 'preview-formulario';

    const emailRevisor = document.createElement('span');
    emailRevisor.textContent = formulario.revisor.nombre;
    emailRevisor.className = 'etiqueta rol-2'

    const divAcciones = document.createElement('div');
    divAcciones.className = 'btns-container';

    const editarBtn = document.createElement('button');
    editarBtn.type = 'button';
    editarBtn.id = 'modalBtn';
    editarBtn.setAttribute('data-target','editar-form');
    editarBtn.textContent = 'Editar';

    const consultarBtn = document.createElement('button');
    consultarBtn.type = 'button';
    consultarBtn.id = 'modalBtn';
    consultarBtn.setAttribute('data-target','consultar-form');
    consultarBtn.textContent = 'Consultar';

    const eliminarFormularioBtn= document.createElement('button');
    eliminarFormularioBtn.type = 'button';
    eliminarFormularioBtn.id = 'eliminarFormulario';
    eliminarFormularioBtn.className = 'btn-rojo';
    eliminarFormularioBtn.textContent = 'Eliminar';

    editarBtn.addEventListener('click', () => {
        document.querySelector('[data-target=ver-revisiones]').click()

        const target = editarBtn.getAttribute('data-target');
        const modal = document.getElementById(target);
        const overlay = document.querySelector(`[data-overlay-target=${target}]`);
        const bClose = document.querySelectorAll(`[data-close-target=${target}]`);

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

        bClose.forEach(c => {
            c.addEventListener('click', () => {
                modal.classList.remove('modal-activo');
                overlay.classList.remove('menu-overlay-activo');
                document.body.classList.remove('no-scroll');
            });
        });

        const form = modal.querySelector('.editar-formulario');
        form.querySelector(`input[name="id_formulario"]`).value = formulario.id_formulario;
        form.querySelector(`input[name="rut_revisor"]`).value = formulario.revisor.rut;
        form.querySelector(`input[name="calidad"][value="${calidad}"]`).checked = true;
        form.querySelector(`input[name="originalidad"][value="${originalidad}"]`).checked = true;
        form.querySelector(`input[name="valoracion"][value="${valoracion}"]`).checked = true;
        form.querySelector('#argumentos').value = argumentos;
        form.querySelector('#comentarios').value = comentarios;
    });

    consultarBtn.addEventListener('click', () => {
        document.querySelector('[data-target=ver-revisiones]').click()

        const target = consultarBtn.getAttribute('data-target');
        const modal = document.getElementById(target);
        const overlay = document.querySelector(`[data-overlay-target=${target}]`);
        const bClose = document.querySelectorAll(`[data-close-target=${target}]`);

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

        bClose.forEach(c => {
            c.addEventListener('click', () => {
                modal.classList.remove('modal-activo');
                overlay.classList.remove('menu-overlay-activo');
                document.body.classList.remove('no-scroll');
            });
        });

        modal.querySelector('h1').textContent = `[${(index + 1)}] Revision`;
        
        const modalConsulta = modal.querySelector('.modal-content');
        modalConsulta.innerHTML = `
            <div class="vista-articulo-evaluacion">
                <span class="etiqueta2">Calidad: ${calidad}/7</span>
                <span class="etiqueta2">Originalidad: ${originalidad}/7</span>
                <span class="etiqueta2">Valoración: ${valoracion}/7</span>
            </div>
            <div>
                <span class="label">Argumentos de Valoración:</span>
                <p class="input-box">${argumentos}</p>
            </div>
            ${comentarios ?
            `<div>
                <span class="label">Comentarios:</span>
                <p class="input-box">${comentarios}</p>
            </div>`
            : ``}
        `;
    });

    eliminarFormularioBtn.addEventListener('click', () => {
        if (!confirm("¿Quieres eliminar el formulario?")) {
            return;
        }

        fetch(`/php/api/formulario.eliminar.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id_formulario: formulario.id_formulario,
                rut_revisor: formulario.revisor.rut
            })
        })
        .then((response) => response.json().then(data => ({ok: response.ok, data})))
        .then(({ok,data}) => {
            if (!data.ok) {
                alert("Error: " + (data.error || "Error desconocido"));
            } else {
                alert("Formulario eliminado correctamente.");
                location.href = location.pathname;
            }
        })
        .catch((error) => {
            console.error(error);
            alert("Ocurrió un error inesperado.");
            location.href = location.pathname;
        });
    });

    if (email === formulario.revisor.email) {
        divAcciones.appendChild(editarBtn);
    }
    divAcciones.appendChild(consultarBtn);
    if (email === formulario.revisor.email ||  id_rol === 3) {
        divAcciones.appendChild(eliminarFormularioBtn);
    }

    previewFormulario.append(emailRevisor,divAcciones);

    return previewFormulario;
}

cargarFormularios = () => {
    const id_articulo = window.location.pathname.split('/')[2];

    fetch(`/php/api/filtrar.articulos.php?id_articulo=${id_articulo}`)
        .then((response) => response.json())
        .then((data) => {
            articulo = data.data[0];

            const formulariosContainer = document.querySelector('.formularios-container');

            formulariosContainer.textContent = '';

            let i = 0;
            fetch(`/php/api/actual.data.usuario.php`)
                .then((response) => response.json())
                .then((usuario) => {
                    if (articulo.formularios) {
                        articulo.formularios.forEach((formulario,index) => {
                            i++;
                            formulariosContainer.appendChild(crearRevisionPreview(formulario,index,usuario.email,usuario.id_rol));
                        });
                    }
                    if (i == 0) {
                        formulariosContainer.innerHTML = '<div class="preview-formulario">No hay revisiones por el momento.</div>';
                    }
                });

        });
}

document.addEventListener('DOMContentLoaded', () => cargarFormularios());