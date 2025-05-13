cargarPosiblesRevisores = async (dropdownMenu,topicos = null,id_articulo = null) => {
    let query = `/php/api/data.revisores.php`;

    if (topicos) {
        const aux = [];
        topicos.forEach(topico => {
            aux.push(topico.id);
        });
        topicos = aux.join(',');
        query = `/php/api/data.revisores.php?topicos=${topicos}${id_articulo ? `&id_articulo=${id_articulo}` : ``}`;
    }

    try {
        fetch(query)
            .then((resultado) => resultado.json())
            .then((data) => {
                dropdownMenu.innerHTML = '';

                let i = 0;
                data.data.forEach((revisor) => {
                    i++;
                    const item = document.createElement('div');
                    item.classList.add('dropdown-item');
                    item.textContent = revisor.email;
                    item.setAttribute('data-rut', revisor.rut);
                    dropdownMenu.appendChild(item);
                });
                if (i === 0) {
                    const item = document.createElement('div');
                    item.textContent = "No hay revisores para asignar";
                    item.classList.add('dropdown-no-item');
                    dropdownMenu.appendChild(item);
                }

            }).catch((error) => {
                console.log(error);
            });
    } catch (error) {
        console.error("Error al cargar los revisores:", error);
    }
}

crearArticuloPreview = async (articulo) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'articulo-preview';
    wrapper.id = 'modalWrapper';
    wrapper.setAttribute('data-target','asignar-articulo');

    const topSection = document.createElement('div');
    topSection.className = 'articulo-preview-tr';

    const link = document.createElement('a');
    link.href = `/articulo/${articulo.id_articulo}`;

    link.addEventListener('click', (e) => e.stopPropagation());

    const iconSpan = document.createElement('span');

    const response = await fetch(`/assets/svg/svg_articulo.svg`);
    const svg_articulo = await response.text();
    iconSpan.innerHTML = svg_articulo;

    link.appendChild(iconSpan);
    link.append(`[${articulo.id_articulo}] ${articulo.titulo}`);
    topSection.appendChild(link);

    const resumenP = document.createElement('p');
    resumenP.textContent = articulo.resumen;
    topSection.appendChild(resumenP);

    const revisoresDiv = document.createElement('div');
    revisoresDiv.className = 'articulo-preview-revisores';

    let i = 0;
    if (articulo.revisores) {
        articulo.revisores.forEach(revisor => {
            i++;
            const revisorPreview = document.createElement('span');
            revisorPreview.className = 'etiqueta rol-2';
            revisorPreview.textContent = revisor.email;

            revisoresDiv.appendChild(revisorPreview);
        });
    }

    if (i < 3) wrapper.classList.add('articulo-necesita-revisores');

    const etiquetasDiv = document.createElement('div');
    etiquetasDiv.className = 'articulo-preview-etiquetas';

    if (Array.isArray(articulo.topicos)) {
        articulo.topicos.forEach(topico => {
            const etiquetaSpan = document.createElement('span');
            etiquetaSpan.className = 'etiqueta';
            etiquetaSpan.textContent = topico.nombre;
            etiquetasDiv.appendChild(etiquetaSpan);
        });
    }

    const fechaDiv = document.createElement('div');
    fechaDiv.className = 'articulo-preview-fecha';

    const fechaP = document.createElement('p');
    fechaP.textContent = `Publicación - ${obtenerTiempo(articulo.fecha_envio)}`;
    fechaDiv.appendChild(fechaP);

    wrapper.appendChild(topSection);
    if (articulo.revisores) {
        wrapper.appendChild(revisoresDiv);
    }
    wrapper.appendChild(etiquetasDiv);
    wrapper.appendChild(fechaDiv);

    const target = 'asignar-articulo';
    const asignacionModal = document.getElementById(target);
    const asignacionOverlay = document.querySelector(`[data-overlay-target=${target}]`);

    wrapper.addEventListener('click', async () => {
        if (!asignacionOverlay.dataset.listenerAdded) {
            asignacionOverlay.addEventListener('click', () => {
                asignacionModal.classList.toggle('modal-activo');
                asignacionOverlay.classList.toggle('menu-overlay-activo');
                document.body.classList.toggle('no-scroll');
            });
            asignacionOverlay.dataset.listenerAdded = 'true';
        }
        asignacionModal.classList.toggle('modal-activo');
        asignacionOverlay.classList.toggle('menu-overlay-activo');
        document.body.classList.toggle('no-scroll');

        asignacionModal.innerHTML = '';

        const asignacionContainer = document.createElement('div');
        asignacionContainer.classList.add('modal-content');

        const h2 = document.createElement('h2')
        h2.innerHTML = '[' + articulo.id_articulo + '] ' + articulo.titulo;

        const formRevisorAleatorio = document.createElement('form');
        formRevisorAleatorio.method = 'POST';

        const asignarAleatorio = document.createElement('button');
        asignarAleatorio.type = 'submit';
        asignarAleatorio.textContent = 'Asignar revisor aleatorio'

        const hiddenIdArticuloAleatorio = document.createElement('input');
        hiddenIdArticuloAleatorio.type = 'hidden';
        hiddenIdArticuloAleatorio.name = 'id_articulo_revisor_aleatorio';
        hiddenIdArticuloAleatorio.value = articulo.id_articulo;

        formRevisorAleatorio.append(asignarAleatorio,hiddenIdArticuloAleatorio);

        const form = document.createElement('form');
        form.className = 'formulario modal-form';
        form.id = 'form-asignar-revisores';
        form.method = 'POST';

        const divRevisores = document.createElement('div');
        divRevisores.className = 'input-container input-revisores';

        const dropdownContainer = document.createElement('div');
        dropdownContainer.className = 'dropdown-2';
        dropdownContainer.id = 'dropdown-container';

        const dropdownBtn = document.createElement('button');
        dropdownBtn.type = 'button';
        dropdownBtn.className = 'dropdown-button-2';
        dropdownBtn.id = 'dropdown-button';
        dropdownBtn.textContent = '+ Agregar Revisor';

        const dropdownMenu = document.createElement('div');
        dropdownMenu.className = 'dropdown-menu';
        dropdownMenu.id = 'dropdown-menu';

        await cargarPosiblesRevisores(dropdownMenu,articulo.topicos,articulo.id_articulo);

        dropdownContainer.append(dropdownBtn,dropdownMenu);
        
        const revisoresContainer = document.createElement('div');
        revisoresContainer.id = 'articulo-revisores-container';
        const revisoresSeleccionados = [];
        if (articulo.revisores) {
            articulo.revisores.forEach(revisor => {
                const revisorDiv = document.createElement('div');
                revisorDiv.className = 'selected-topic'
                revisorDiv.setAttribute('data-rut',revisor.rut);
                revisorDiv.innerHTML = revisor.email;
                revisoresContainer.appendChild(revisorDiv);
                
                revisoresSeleccionados.push(revisor.rut);
            });
        } else {
            revisoresContainer.innerHTML = 'No hay revisores asignados.'
        }

        const hiddenIdArticulo = document.createElement('input');
        hiddenIdArticulo.type = 'hidden';
        hiddenIdArticulo.id = 'hidden-id-articulo';
        hiddenIdArticulo.name = 'id_articulo';
        hiddenIdArticulo.value = articulo.id_articulo;

        const hiddenRevisoresInput = document.createElement('input');
        hiddenRevisoresInput.type = 'hidden';
        hiddenRevisoresInput.id = 'hidden-revisores';
        hiddenRevisoresInput.name = 'revisores';

        hiddenRevisoresInput.value = revisoresSeleccionados.join(',');

        dropdownBtn.addEventListener('click', () => {
            dropdownMenu.classList.toggle('show');
        });

        dropdownContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('dropdown-item')) {
                const seleccionado = e.target;
                const rutRevisor = seleccionado.getAttribute('data-rut');
                const emailRevisor = seleccionado.textContent;

                if (hiddenRevisoresInput.value.split(',').includes(rutRevisor)) return;

                const divRevisor = document.createElement('div');
                divRevisor.className = 'selected-topic';
                divRevisor.textContent = emailRevisor;
                divRevisor.setAttribute('data-rut', rutRevisor);

                const hiddenValor = hiddenRevisoresInput.value ? hiddenRevisoresInput.value.split(",") : [];
                if (hiddenValor.length === 0) {
                    revisoresContainer.innerHTML = ''
                }
                revisoresContainer.appendChild(divRevisor);

                hiddenValor.push(rutRevisor);
                hiddenRevisoresInput.value = hiddenValor.join(',');

                const existingError = revisoresContainer.querySelector('.error-message');
                if (existingError) existingError.remove();

                dropdownMenu.classList.remove("show");
            }
        });

        revisoresContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('selected-topic')) {
                const divRevisor = e.target;
                const rutRevisor = divRevisor.getAttribute('data-rut');

                divRevisor.remove();

                const aux = hiddenRevisoresInput.value.split(',').filter(rut => rut !== rutRevisor);
                hiddenRevisoresInput.value = aux.join(',');

                const hiddenValor = hiddenRevisoresInput.value ? hiddenRevisoresInput.value.split(",") : [];
                if (hiddenValor.length === 0) revisoresContainer.innerHTML = 'No hay revisores asignados.';
            }
        });

        divRevisores.append(dropdownContainer,revisoresContainer,hiddenRevisoresInput,hiddenIdArticulo);

        const btnEnviarAsignacion = document.createElement('button');
        btnEnviarAsignacion.type = 'submit';
        btnEnviarAsignacion.textContent = 'Asignar'

        const btnCancelarAsig = document.createElement('button');
        btnCancelarAsig.className = 'btn-rojo';
        btnCancelarAsig.type = 'button';
        btnCancelarAsig.textContent = 'Cancelar';
        btnCancelarAsig.addEventListener('click', () => {
            asignacionModal.classList.toggle('modal-activo');
            asignacionOverlay.classList.toggle('menu-overlay-activo');
            document.body.classList.toggle('no-scroll');
        });

        const btnsContainer = document.createElement('div');
        btnsContainer.className = 'btns-container';

        btnsContainer.append(btnEnviarAsignacion,btnCancelarAsig);

        form.append(divRevisores,btnsContainer);
        
        const h1 = document.createElement('h1');
        h1.textContent = 'Asignar revisores a articulo';

        asignacionContainer.append(h1,h2,formRevisorAleatorio,form);

        asignacionModal.appendChild(asignacionContainer);
    });

    return wrapper;
}