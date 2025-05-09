crearRevisionPreview = (formulario,index,email) => {
    const calidad = formulario.calidad;
    const originalidad = formulario.originalidad;
    const valoracion = formulario.valoracion;
    const argumentos = formulario.argumentos_valoracion;
    const comentarios = formulario.comentarios;
    const email_revisor = formulario.email_revisor;

    const previewFormulario = document.createElement('div');
    previewFormulario.className = 'preview-formulario';

    const numRevision = document.createElement('span');
    numRevision.textContent = 'R' + (index + 1);

    const emailRevisor = document.createElement('span');
    emailRevisor.textContent = email_revisor;
    emailRevisor.className = 'etiqueta rol-2'

    const divAcciones = document.createElement('div');
    divAcciones.className = 'btns-container';

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
                email_revisor: formulario.email_revisor
            })
        })
        .then((response) => response.json().then(data => ({ok: response.ok, data})))
        .then(({ ok, data }) => {
            if (!ok) {
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

    const modalConsulta = document.getElementById('consultar-form');

    consultarBtn.addEventListener('click', () => {
        modalConsulta.innerHTML = `
            <div class="revision-view modal-content">
                <h1>[${(index + 1)}] Formulario de Evaluación</h1>
                <div>
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
            </div>
        `;
    });

    divAcciones.appendChild(consultarBtn);
    if (email === email_revisor) {
        divAcciones.appendChild(eliminarFormularioBtn);
    }

    previewFormulario.append(numRevision,emailRevisor,divAcciones);

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
                            formulariosContainer.appendChild(crearRevisionPreview(formulario,index,usuario.email));
                        });
                    }
                    if (i == 0) {
                        formulariosContainer.innerHTML = '<div class="preview-formulario">No hay revisiones por el momento.</div>';
                    }
                });

        });
}

document.addEventListener('DOMContentLoaded', () => cargarFormularios());