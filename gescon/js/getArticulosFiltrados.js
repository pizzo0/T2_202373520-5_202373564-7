const container = document.getElementsByClassName('filtro-container')[0];
const overlay = document.getElementById('filtro-overlay');

let paginaActual = 0;
const resultadosPorPagina = 20;
let totalResultados = 0;

const btnAnterior = document.getElementById('btnAnterior');
const btnSiguiente = document.getElementById('btnSiguiente');
const paginaInfo = document.getElementById('pagina-info');

btnAnterior.addEventListener('click', () => {
    if (paginaActual > 0) {
        paginaActual--;
        enviarFormularioConPagina();
    }
});

btnSiguiente.addEventListener('click', () => {
    if ((paginaActual + 1) * resultadosPorPagina < totalResultados) {
        paginaActual++;
        enviarFormularioConPagina();
    }
});

function actualizarPaginacion() {
    btnAnterior.disabled = paginaActual === 0;
    btnSiguiente.disabled = (paginaActual + 1) * resultadosPorPagina >= totalResultados;

    let totalPaginas = Math.ceil(totalResultados / resultadosPorPagina);
    totalPaginas = Math.max(1, totalPaginas);
    paginaInfo.textContent = `Página ${paginaActual + 1} de ${totalPaginas}`;
}

function enviarFormularioConPagina() {
    const form = document.getElementById('filtro-form');
    const formData = new FormData(form);

    const ordenarPor = document.getElementById('ordenar_por');
    if (ordenarPor) {
        formData.append('ordenar_por', ordenarPor.value);
    }

    const filtros = new URLSearchParams(formData);
    cargarArticulos(filtros);
}

const clickFuera = (e) => {
    if (!container.contains(e.target)) {
        container.classList.remove('filtro-container-activo');
        overlay.classList.remove('filtro-overlay-activo');
        document.body.classList.remove('no-scroll');
        document.removeEventListener('click', clickFuera);
    }
};

const toggleFC = () => {
    const estaActivo = container.classList.toggle('filtro-container-activo');
    overlay.classList.toggle('filtro-overlay-activo', estaActivo);
    document.body.classList.toggle('no-scroll');

    if (estaActivo) {
        setTimeout(() => {
            document.addEventListener('click', clickFuera);
        }, 0);
    } else {
        document.removeEventListener('click', clickFuera);
    }
};

crearArticuloPreview = async (articulo) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'articulo-preview';

    const topSection = document.createElement('div');
    topSection.className = 'articulo-preview-tr';

    const link = document.createElement('a');
    link.href = `/articulo/${articulo.id_articulo}`;

    const iconSpan = document.createElement('span');

    const response = await fetch(`/assets/svg/svg_articulo.svg`);
    const svg_articulo = await response.text();
    iconSpan.innerHTML = svg_articulo;

    link.appendChild(iconSpan);
    link.append(` ${articulo.titulo}`);
    topSection.appendChild(link);

    const resumenP = document.createElement('p');
    resumenP.textContent = articulo.resumen;
    topSection.appendChild(resumenP);

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
    wrapper.appendChild(etiquetasDiv);
    wrapper.appendChild(fechaDiv);

    wrapper.addEventListener('click', () => link.click());

    return wrapper;
}

cargarArticulos = async (filtros = null) => {
    const container = document.getElementById('resultados-busqueda');
    container.innerHTML = `<p>Cargando artículos...</p>`;
    let queryString = filtros ? `${filtros.toString()}&` : '';
    queryString += `offset=${paginaActual * resultadosPorPagina}&limit=${resultadosPorPagina}`;

    try {
        const response = await fetch(`/php/api/filtrar.articulos.php?${queryString}`);
        const data = await response.json();

        if (data.total > 0 && data.data.length > 0) {
            container.innerHTML = '';
            
            const previews = await Promise.all(data.data.map(articulo => crearArticuloPreview(articulo)));
            previews.forEach(preview => container.appendChild(preview));

            actualizarPaginacion();
        } else {
            container.innerHTML = `<p>No se encontraron resultados.</p>`;
            actualizarPaginacion();
        }

        document.getElementById("filtro-num-resultados").innerHTML = `${data.total} resultados`;
    } catch (error) {
        container.innerHTML = `<p>Error al obtener datos, prueba de nuevo.</p>`;
        container.innerHTML += `<p>${error}</p>`;
    }
};

window.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filtro-form');
    const formData = new FormData(form);

    if (formData) {
        const ordenarPor = document.getElementById('ordenar_por');
        if (ordenarPor) {
            formData.append('ordenar_por', ordenarPor.value);
        }
        const filtros = form ? new URLSearchParams(formData) : null;
        cargarArticulos(filtros);
    } else {
        cargarArticulos();
    }
    
    // form.dispatchEvent(new Event('submit'));
});

document.getElementById('filtro-form').addEventListener('submit', (e) => {
    e.preventDefault();

    paginaActual = 0;

    const formData = new FormData(e.target);

    const ordenarPor = document.getElementById('ordenar_por');
    if (ordenarPor) {
        formData.append('ordenar_por', ordenarPor.value);
    }

    const filtros = new URLSearchParams(formData);
    cargarArticulos(filtros);
});

document.addEventListener("DOMContentLoaded", () => {
    fetch("/php/api/topicos.php")
        .then((response) => response.json())
        .then((data) => {
            const selectTopicos = document.getElementById("topicos");

            data.sort((a, b) => a.nombre.localeCompare(b.nombre));

            data.forEach((topico) => {
                const item = document.createElement("option");

                item.textContent = topico.nombre;
                item.setAttribute("value", topico.nombre);
                selectTopicos.appendChild(item);
        });
    })
})

document.getElementById('ordenar_por').addEventListener('change', () => {
    paginaActual = 0;
    const form = document.getElementById('filtro-form');
    form.dispatchEvent(new Event('submit'));
});