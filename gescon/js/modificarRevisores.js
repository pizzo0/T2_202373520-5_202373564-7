const toggleBtnsRevisor = document.querySelectorAll("#toggle_crear_revisor");
const crearRevisorMenu = document.getElementById('crear-revisor-container');
const overlayRevisorMenu = document.querySelector('.menu-overlay');

toggleBtnsRevisor.forEach((btn) => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        crearRevisorMenu.classList.toggle('crear-revisor-container-activo');
        overlayRevisorMenu.classList.toggle('menu-overlay-activo');
    });
});

overlayRevisorMenu.addEventListener('click', () => {
    toggleBtnsRevisor[0]?.click();
});

const crearArticuloDiv = (articulo, en_revision) => {
    const articuloDiv = document.createElement('div');
    articuloDiv.className = 'selected-topic';
    articuloDiv.setAttribute('data-articulo', articulo);
    articuloDiv.setAttribute('data-estado', en_revision);
    articuloDiv.textContent = articulo;

    if (en_revision) {
        articuloDiv.classList.add("etiqueta-rojo");
    }

    return articuloDiv;
}

const cargarPosiblesArticulos = (dropdownMenu, posiblesArticulos) => {
    let i = 0;
    posiblesArticulos.forEach((articulo) => {
        i++;
        const item = document.createElement('div');
        item.className = 'dropdown-item';
        item.textContent = articulo;
        item.setAttribute('data-articulo', articulo);
        dropdownMenu.appendChild(item);
    });
    if (i === 0) {
        const item = document.createElement('div');
        item.textContent = "No hay articulos para asignar";
        item.classList.add('dropdown-no-item');
        dropdownMenu.appendChild(item);
    }
}

const cargarTopicos = async (dropdownMenu) => {
    try {
        const response = await fetch("/php/api/topicos.php");
        const data = await response.json();

        data.sort((a, b) => a.nombre.localeCompare(b.nombre));
        dropdownMenu.innerHTML = '';

        data.forEach((topico) => {
            const item = document.createElement("div");
            item.classList.add("dropdown-item");
            item.textContent = topico.nombre;
            item.setAttribute("data-id", topico.id);
            dropdownMenu.appendChild(item);
        });
    } catch (error) {
        console.error("Error al cargar los tópicos: ", error);
    }
}

function isTopicAlreadySelected(topicId) {
    const hiddenTopics = document.getElementById("hidden-topics").value.split(",");
    return hiddenTopics.includes(topicId);
}

async function cargarRevisores() {
    fetch('/php/api/data.revisores.php')
        .then(response => response.json())
        .then(result => {
            const container = document.querySelector('.revisores-container');
            container.innerHTML = '';

            if (result.total > 0) {
                result.data.forEach(async (revisor) => {
                    const target = 'modificar-revisor-container';

                    const revisorPreview = document.createElement('div');
                    revisorPreview.className = 'revisor-preview-container';
                    revisorPreview.id = 'modalBtn';
                    revisorPreview.setAttribute('data-target', target);

                    const preview = document.createElement('div');
                    preview.className = 'revisor-preview';

                    const divRut = document.createElement('div');
                    divRut.className = 'revisor-preview-rut';

                    const aRut = document.createElement('a');
                    aRut.className = 'rut-revisor';
                    aRut.textContent = `Rut: ${revisor.rut}`;

                    const spanRol = document.createElement('span');
                    spanRol.className = `etiqueta rol-${revisor.id_rol}`;
                    spanRol.textContent = revisor.id_rol === 2 ? 'Revisor' : revisor.id_rol === 3 ? 'Jefe de Comité' : 'Indefinido';

                    divRut.append(aRut,spanRol);

                    const divData = document.createElement('div');
                    divData.className = 'revisor-preview-data';
                    const spanNombre = document.createElement('span');
                    spanNombre.className = 'nombre-revisor';
                    spanNombre.textContent = `Nombre: ${revisor.nombre}`;
                    const spanCorreo = document.createElement('span');
                    spanCorreo.className = 'correo-revisor';
                    spanCorreo.textContent = `Correo: ${revisor.email}`;
                    divData.appendChild(spanNombre);
                    divData.appendChild(spanCorreo);

                    const divEspecialidades = document.createElement('div');
                    divEspecialidades.className = 'revisor-preview-especialidades';
                    divEspecialidades.id = 'esp-revisor';

                    if (revisor.topicos) {
                        revisor.topicos.forEach((topico) => {
                            const span = document.createElement('span');
                            span.className = 'etiqueta';
                            span.setAttribute('data-id', topico.id_topico);
                            span.textContent = topico.nombre || 'Desconocido';
                            divEspecialidades.appendChild(span);
                        });
                    } else {
                        const itemVacio = document.createElement('div');
                        itemVacio.textContent = 'No tiene especialidades asignadas';
                        divEspecialidades.appendChild(itemVacio);
                    }

                    const divArticulosAsignados = document.createElement('div');
                    divArticulosAsignados.className = 'revisor-preview-articulos';
                    if (revisor.id_articulos) {
                        revisor.id_articulos.forEach((id_articulo) => {
                            const itemArticulo = document.createElement('div');
                            itemArticulo.className = 'etiqueta';
                            itemArticulo.textContent = id_articulo;
                            divArticulosAsignados.appendChild(itemArticulo);
                        });
                    } else {
                        const itemVacio = document.createElement('div');
                        itemVacio.textContent = 'No tiene articulos asignados'
                        divArticulosAsignados.appendChild(itemVacio);
                    }

                    const divAcciones = document.createElement('div');
                    divAcciones.className = 'revisor-preview-acciones btns-container';

                    const formEliminar = document.createElement('form');
                    formEliminar.method = "POST";

                    const hiddenInputRut = document.createElement('input');
                    hiddenInputRut.type = 'hidden';
                    hiddenInputRut.name = 'eliminar';
                    hiddenInputRut.value = revisor.rut;

                    const btnEliminarRevisor = document.createElement('button');
                    btnEliminarRevisor.type = 'submit';
                    btnEliminarRevisor.className = 'btn-rojo';
                    btnEliminarRevisor.textContent = 'Eliminar';

                    btnEliminarRevisor.addEventListener('click', (e) => e.stopPropagation());

                    formEliminar.append(hiddenInputRut,btnEliminarRevisor);

                    const btnAsignarRevisor = document.createElement('button');
                    btnAsignarRevisor.type = 'button';
                    btnAsignarRevisor.textContent = 'Asignar articulos';
                    btnAsignarRevisor.id = "modalBtn";
                    btnAsignarRevisor.setAttribute('data-target', target);

                    const asignacionModal = document.getElementById(target);
                    const asignacionOverlay = document.querySelector(`[data-overlay-target=${target}]`);

                    btnAsignarRevisor.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        
                        const articulos_posibles = revisor.id_articulos_posibles || [];
                        articulos_posibles.sort((a,b) => a - b);

                        const modalModificarRevisor = document.getElementById(target);
                        modalModificarRevisor.innerHTML = '';

                        const modalRevisorContent = document.createElement('div');
                        modalRevisorContent.className = 'modal-content';

                        const h1 = document.createElement('h1');
                        h1.textContent = 'Asignar articulos a revisor';

                        const form = document.createElement('form');
                        form.className = 'formulario modal-form';
                        form.id = 'form-modificar-revisor';
                        form.method = 'POST';

                        const dropdownContainer = document.createElement('div');
                        dropdownContainer.className = 'dropdown-2';
                        dropdownContainer.id = 'dropdown-container';

                        const dropdownBtn = document.createElement('button');
                        dropdownBtn.type = 'button';
                        dropdownBtn.className = 'dropdown-button-2';
                        dropdownBtn.id = 'dropdown-button';
                        dropdownBtn.textContent = '+ Agregar articulo (ID)';

                        const dropdownMenu = document.createElement('div');
                        dropdownMenu.className = 'dropdown-menu';
                        dropdownMenu.id = 'dropdown-menu';

                        cargarPosiblesArticulos(dropdownMenu,articulos_posibles);

                        dropdownContainer.append(dropdownBtn,dropdownMenu);

                        const articulosContainer = document.createElement('div');
                        articulosContainer.id = "revisor-articulos-container";
                        const articulosSeleccionados = [];
                        if (revisor.id_articulos) {
                            articulosContainer.innerHTML = '';
                            for (const articulo of revisor.id_articulos) {
                                try {
                                    const res = await fetch(`/php/api/filtrar.articulos.php?id_articulo=${articulo}`);
                                    const data = await res.json();
                                    const articulo_data = data.data[0];
                                    
                                    const articuloDiv = crearArticuloDiv(articulo,articulo_data.en_revision);
                                    articulosContainer.appendChild(articuloDiv);
                                    articulosSeleccionados.push(articulo);

                                } catch (error) {
                                    console.error(`Error al cargar el articulo ${articulo}:`, error);
                                }
                            }
                        } else {
                            articulosContainer.innerHTML = "No hay articulos asignados.";
                        }

                        const hiddenRutRevisor = document.createElement('input');
                        hiddenRutRevisor.type = 'hidden';
                        hiddenRutRevisor.id = 'hidden-rut-revisor';
                        hiddenRutRevisor.name = 'rut_revisor';
                        hiddenRutRevisor.value = revisor.rut;

                        const hiddenArticulosInput = document.createElement('input');
                        hiddenArticulosInput.type = 'hidden';
                        hiddenArticulosInput.id = 'hidden-articulos';
                        hiddenArticulosInput.name = 'articulos';

                        hiddenArticulosInput.value = articulosSeleccionados.join(',');

                        const hiddenAux = document.createElement('input');
                        hiddenAux.type = 'hidden';
                        hiddenAux.id = 'hidden-aux';
                        hiddenAux.name = 'articulos-previos';

                        hiddenAux.value = articulosSeleccionados.join(',');

                        dropdownBtn.addEventListener('click', () => {
                            dropdownMenu.classList.toggle('show');
                        });

                        dropdownContainer.addEventListener('click', async (e) => {
                            if (e.target.classList.contains('dropdown-item')) {
                                const seleccionado = e.target;
                                const articuloSeleccionado = seleccionado.getAttribute('data-articulo');
                                
                                if (hiddenArticulosInput.value.split(',').includes(articuloSeleccionado)) return;

                                const hiddenValor = hiddenArticulosInput.value ? hiddenArticulosInput.value.split(',') : [];
                                if (hiddenValor.length === 0) {
                                    articulosContainer.innerHTML = '';
                                }

                                hiddenValor.push(articuloSeleccionado);
                                hiddenValor.sort((a,b) => a - b);

                                const articuloDiv = crearArticuloDiv(articuloSeleccionado,0)
                                articulosContainer.appendChild(articuloDiv);

                                hiddenArticulosInput.value = hiddenValor.join(',');

                                dropdownMenu.classList.remove('show');
                            }
                        });

                        articulosContainer.addEventListener('click', async (e) => {
                            if (e.target.classList.contains('selected-topic')) {
                                const articuloDiv = e.target;

                                if (articuloDiv.getAttribute('data-estado') == "1") return;
                                const articuloSeleccionado = articuloDiv.getAttribute('data-articulo');

                                articuloDiv.remove();

                                const aux = hiddenArticulosInput.value.split(',').filter(id => id !== articuloSeleccionado);
                                hiddenArticulosInput.value = aux.join(',');

                                const hiddenValor = hiddenArticulosInput.value ? hiddenArticulosInput.value.split(',') : [];
                                if (hiddenValor.length === 0) articulosContainer.innerHTML = 'No hay articulos asignados.';
                            }
                        });

                        const divArticulos = document.createElement('div');
                        divArticulos.className = 'input-container input-articulos';
                        divArticulos.append(dropdownContainer,articulosContainer,hiddenArticulosInput,hiddenAux,hiddenRutRevisor);

                        const btnAsignarArticulos = document.createElement('button');
                        btnAsignarArticulos.type = 'submit';
                        btnAsignarArticulos.textContent = 'Asignar';

                        const btnCancelarAsig = document.createElement('button');
                        btnCancelarAsig.className = 'btn-rojo';
                        btnCancelarAsig.type = 'button';
                        btnCancelarAsig.textContent = 'Cancelar';
                        btnCancelarAsig.addEventListener('click', () => {
                            asignacionModal.classList.toggle('modal-activo');
                            asignacionOverlay.classList.toggle('menu-overlay-activo');
                        });

                        const btnsContainer = document.createElement('div');
                        btnsContainer.className = 'btns-container';

                        btnsContainer.append(btnAsignarArticulos,btnCancelarAsig);

                        form.append(divArticulos,btnsContainer);

                        modalRevisorContent.append(h1,form);

                        modalModificarRevisor.appendChild(modalRevisorContent);

                        const overlay = document.querySelector(`[data-overlay-target=${target}]`);

                        if (!overlay.dataset.listenerAdded) {
                            overlay.addEventListener('click', () => {
                                modalModificarRevisor.classList.toggle('modal-activo');
                                overlay.classList.toggle('menu-overlay-activo');
                                document.body.classList.toggle('no-scroll');
                            });
                            overlay.dataset.listenerAdded = 'true';
                        }

                        modalModificarRevisor.classList.toggle('modal-activo');
                        overlay.classList.toggle('menu-overlay-activo');
                        document.body.classList.toggle('no-scroll');
                    });

                    divAcciones.append(btnAsignarRevisor,formEliminar);

                    preview.appendChild(divRut);
                    preview.appendChild(divData);
                    preview.appendChild(divEspecialidades);
                    preview.appendChild(divArticulosAsignados);
                    preview.appendChild(divAcciones);

                    preview.addEventListener('click', async () => {
                        const modalModificarRevisor = document.getElementById(target);

                        modalModificarRevisor.innerHTML = '';

                        const modalRevisorContent = document.createElement('div');
                        modalRevisorContent.className = 'modal-content';

                        const form = document.createElement('form');
                        form.className = 'formulario modal-form';
                        form.id = 'form-modificar-revisor';
                        form.method = 'POST';

                        const h1 = document.createElement('h1');
                        h1.textContent = "Modificar revisor";
                        modalRevisorContent.append(h1);

                        const divRut = document.createElement('div');
                        divRut.className = 'input-container';
                        const labelRut = document.createElement('label');
                        labelRut.textContent = 'Rut';
                        labelRut.setAttribute('for','rut_modificar');
                        const inputRut = document.createElement('input');
                        inputRut.type = 'text';
                        inputRut.name = 'rut_modificar';
                        inputRut.id = 'rut_modificar';
                        inputRut.value = revisor.rut;
                        inputRut.readOnly = true;
                        divRut.append(labelRut, inputRut);

                        const divNombre = document.createElement('div');
                        divNombre.className = 'input-container';
                        const labelNombre = document.createElement('label');
                        labelNombre.textContent = 'Nombre';
                        labelNombre.setAttribute('for','nombre_modificar');
                        const inputNombre = document.createElement('input');
                        inputNombre.type = 'text';
                        inputNombre.name = 'nombre_modificar';
                        inputNombre.id = 'nombre_modificar';
                        inputNombre.value = revisor.nombre;
                        inputNombre.required = true;
                        divNombre.append(labelNombre, inputNombre);

                        const divCorreo = document.createElement('div');
                        divCorreo.className = 'input-container';
                        const labelCorreo = document.createElement('label');
                        labelCorreo.textContent = 'Correo';
                        labelCorreo.setAttribute('for','correo_modificar');
                        const inputCorreo = document.createElement('input');
                        inputCorreo.type = 'email';
                        inputCorreo.name = 'correo_modificar';
                        inputCorreo.id = 'correo_modificar';
                        inputCorreo.value = revisor.email;
                        inputCorreo.required = true;
                        divCorreo.append(labelCorreo, inputCorreo);

                        const divEspecialidades = document.createElement('div');
                        divEspecialidades.className = 'input-container input-especialidades';

                        const dropdownContainer = document.createElement('div');
                        dropdownContainer.className = "dropdown";
                        dropdownContainer.id = "dropdown-container";

                        const dropdownBtn = document.createElement('button');
                        dropdownBtn.type = "button";
                        dropdownBtn.className = "dropdow-button";
                        dropdownBtn.id = "dropdown-button";
                        dropdownBtn.textContent = "+ Agregar Especialidades";

                        const dropdownMenu = document.createElement('div');
                        dropdownMenu.className = "dropdown-menu";
                        dropdownMenu.id = "dropdown-menu";

                        await cargarTopicos(dropdownMenu);

                        dropdownContainer.append(dropdownBtn, dropdownMenu);

                        const especialidadesContainer = document.createElement('div');
                        especialidadesContainer.id = "topicos-container";

                        const topicosSeleccionados = [];

                        if (revisor.topicos) {
                            revisor.topicos.forEach((topico) => {
                                const topicDivAux = document.createElement("div");
                                topicDivAux.classList.add("selected-topic");
                                topicDivAux.textContent = topico.nombre;
                                topicDivAux.setAttribute("data-id", topico.id_topico);
                                especialidadesContainer.appendChild(topicDivAux);
    
                                topicosSeleccionados.push(topico.id_topico);
                            });
                        }

                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.id = 'hidden-topics';
                        hiddenInput.name = 'topicos';

                        hiddenInput.value = topicosSeleccionados.join(",");

                        divEspecialidades.append(dropdownContainer, especialidadesContainer, hiddenInput);

                        dropdownBtn.addEventListener("click", () => {
                            dropdownMenu.classList.toggle("show");
                        });

                        dropdownMenu.addEventListener("click", (e) => {
                            if (e.target.classList.contains("dropdown-item")) {
                                const selected = e.target;
                                const topicId = selected.getAttribute("data-id");
                                const topicName = selected.textContent;

                                if (isTopicAlreadySelected(topicId)) return;

                                const topicDiv = document.createElement("div");
                                topicDiv.classList.add("selected-topic");
                                topicDiv.textContent = topicName;
                                topicDiv.setAttribute("data-id", topicId);
                                especialidadesContainer.appendChild(topicDiv);

                                const hiddenVal = hiddenInput.value ? hiddenInput.value.split(",") : [];
                                hiddenVal.push(topicId);
                                hiddenInput.value = hiddenVal.join(",");

                                const existingError = especialidadesContainer.querySelector(".error-message");
                                if (existingError) existingError.remove();

                                dropdownMenu.classList.remove("show");
                            }
                        });

                        especialidadesContainer.addEventListener("click", (e) => {
                            if (e.target.classList.contains("selected-topic")) {
                                const topicDiv = e.target;
                                const topicId = topicDiv.getAttribute("data-id");

                                topicDiv.remove();

                                const updatedIds = hiddenInput.value.split(",").filter(id => id !== topicId);
                                hiddenInput.value = updatedIds.join(",");
                            }
                        });

                        form.addEventListener("submit", (e) => {
                            const hiddenTopicsVal = hiddenInput.value.trim();
                            const existingError = especialidadesContainer.querySelector(".error-message");
                            if (existingError) existingError.remove();

                            if (!hiddenTopicsVal) {
                                e.preventDefault();
                                const errorMsg = document.createElement("p");
                                errorMsg.textContent = "Debes seleccionar al menos un tópico.";
                                errorMsg.classList.add("error-message");
                                especialidadesContainer.appendChild(errorMsg);
                            }
                        });

                        const btnEnviar = document.createElement('button');
                        btnEnviar.type = 'submit';
                        btnEnviar.textContent = 'Guardar';

                        const btnCancelar = document.createElement('button');
                        btnCancelar.className = 'btn-rojo';
                        btnCancelar.type = 'button';
                        btnCancelar.textContent = 'Cancelar';
                        btnCancelar.addEventListener('click', () => {
                            const modalElement = document.getElementById(target);
                            const overlayElement = document.querySelector(`[data-overlay-target=${target}]`);
                            modalElement.classList.toggle('modal-activo');
                            overlayElement.classList.toggle('menu-overlay-activo');
                        });

                        const divInputs = document.createElement('div');
                        divInputs.className = 'inputs-container';
                        divInputs.append(divRut, divNombre, divCorreo, divEspecialidades);

                        const divBtnsRevisor = document.createElement('div');
                        divBtnsRevisor.className = 'btns-container';
                        divBtnsRevisor.append(btnEnviar, btnCancelar);

                        form.append(divInputs, divBtnsRevisor);
                        modalRevisorContent.appendChild(form);
                        modalModificarRevisor.appendChild(modalRevisorContent);

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

                        const overlay = document.querySelector(`[data-overlay-target=${target}]`);

                        if (!overlay.dataset.listenerAdded) {
                            overlay.addEventListener('click', () => {
                                modalModificarRevisor.classList.toggle('modal-activo');
                                overlay.classList.toggle('menu-overlay-activo');
                                document.body.classList.toggle('no-scroll');
                            });
                            overlay.dataset.listenerAdded = 'true';
                        }

                        modalModificarRevisor.classList.toggle('modal-activo');
                        overlay.classList.toggle('menu-overlay-activo');
                        document.body.classList.toggle('no-scroll');
                    });

                    revisorPreview.appendChild(preview);
                    container.appendChild(revisorPreview);
                });
            } else {
                container.textContent = 'No hay revisores para mostrar.';
            }
        })
        .catch(error => {
            console.error('Error al cargar revisores:', error);
        });
}

cargarRevisores();