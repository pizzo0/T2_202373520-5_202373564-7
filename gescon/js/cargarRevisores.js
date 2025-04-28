const toggleBtnsRevisor = document.querySelectorAll("#toggle_crear_revisor");
const crearRevisorMenu = document.getElementById('crear-revisor-container');
const overlayRevisorMenu = document.querySelector('.menu-overlay');

toggleBtnsRevisor.forEach((btn) => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        crearRevisorMenu.classList.toggle('crear-revisor-container-activo');
        overlayRevisorMenu.classList.toggle('menu-overlay-activo');
    })
});

overlayRevisorMenu.addEventListener('click', () => {
    toggleBtnsRevisor[0].click();
})

function cargarRevisores() {
    fetch(`/php/api/data.revisores.php`)
        .then((response) => response.json())
        .then((result) => {
            const container = document.querySelector('.revisores-container');
            container.innerHTML = '';
            if (result.total > 0) {

                result.data.forEach(revisor => {
                    const div = document.createElement('div');
                    div.className = 'revisor-preview-container';

                    const form = document.createElement('form');
                    form.className = 'revisor-preview';
                    form.method = 'POST';

                    const formEliminar = document.createElement('form');
                    formEliminar.className = 'revisor-eliminar'
                    formEliminar.method = 'POST';

                    const divRut = document.createElement('div');
                    divRut.className = 'revisor-preview-rut';
                    const spanRut = document.createElement('span');
                    spanRut.className = 'rut-revisor';
                    spanRut.textContent = revisor.rut;
                    divRut.appendChild(spanRut);

                    const divData = document.createElement('div');
                    divData.className = 'revisor-preview-data';
                    const spanNombre = document.createElement('span');
                    spanNombre.className = 'nombre-revisor';
                    spanNombre.textContent = revisor.nombre;
                    const spanCorreo = document.createElement('span');
                    spanCorreo.className = 'correo-revisor';
                    spanCorreo.textContent = revisor.email;
                    divData.appendChild(spanNombre);
                    divData.appendChild(spanCorreo);

                    const divEspecialidades = document.createElement('div');
                    divEspecialidades.className = 'revisor-preview-especialidades';
                    divEspecialidades.id = 'esp-revisor';
                    
                    // 🔥 Cambiamos esta parte:
                    if (Array.isArray(revisor.id_topicos) && Array.isArray(revisor.nombres_topicos)) {
                        revisor.id_topicos.forEach((id, index) => {
                            const span = document.createElement('span');
                            span.className = 'etiqueta';
                            span.setAttribute('data-id', id);
                            span.textContent = revisor.nombres_topicos[index]['nombre'];
                            divEspecialidades.appendChild(span);
                        });
                    }

                    const divAcciones = document.createElement('div');
                    divAcciones.className = 'revisor-preview-acciones';

                    const btnAñadir = document.createElement('button');
                    btnAñadir.id = 'esp_revisor_añadir';
                    btnAñadir.type = 'button';
                    btnAñadir.textContent = 'Añadir especialidad';

                    divAcciones.appendChild(btnAñadir);

                    const btnEliminar = document.createElement('button');
                    btnEliminar.id = 'eliminar_revisor';
                    btnEliminar.className = 'btn-rojo';
                    btnEliminar.type = 'submit';
                    btnEliminar.textContent = 'Eliminar';

                    const inputRut = document.createElement('input');
                    inputRut.type = "hidden";
                    inputRut.name= "rut_revisor";
                    inputRut.value= `${revisor.rut}`;

                    const inputEliminar = document.createElement('input');
                    inputEliminar.type = "hidden";
                    inputEliminar.name= "tipo";
                    inputEliminar.value= "eliminar";                    
                    
                    formEliminar.appendChild(inputRut);
                    formEliminar.appendChild(inputEliminar);
                    formEliminar.appendChild(btnEliminar);

                    form.appendChild(divRut);
                    form.appendChild(divData);
                    form.appendChild(divEspecialidades);
                    form.appendChild(divAcciones);

                    div.appendChild(form);
                    div.appendChild(formEliminar);

                    container.appendChild(div);
                });
            } else {
                console.log('No hay revisores para mostrar.');
            }
        })
        .catch(error => {
            console.error('Error al cargar revisores.');
        });
}

cargarRevisores();