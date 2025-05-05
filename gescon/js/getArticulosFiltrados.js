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
        document.removeEventListener('click', clickFuera);
    }
};

const toggleFC = () => {
    const estaActivo = container.classList.toggle('filtro-container-activo');
    overlay.classList.toggle('filtro-overlay-activo', estaActivo);

    if (estaActivo) {
        setTimeout(() => {
            document.addEventListener('click', clickFuera);
        }, 0);
    } else {
        document.removeEventListener('click', clickFuera);
    }
};

cargarArticulos = (filtros = null) => {
    const container = document.getElementById('resultados-busqueda');
    container.innerHTML = `<p>Cargando artículos...</p>`;
    let queryString = filtros ? `${filtros.toString()}&` : '';
    queryString += `offset=${paginaActual * resultadosPorPagina}&limit=${resultadosPorPagina}`;

    fetch(`/php/api/filtrar.articulos.php?${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (data.total > 0 && data.data.length > 0) {
                fetch(`/assets/svg/svg_articulo.svg`)
                    .then(response => response.text())
                    .then(svg => {
                        svg_articulo = svg;
                        let res = ``;
                        data.data.forEach(articulo => {
                            res += `
                            <div class="articulo-preview">
                                <div class="articulo-preview-tr">
                                    <a href="/articulo/${articulo.articulo_id}"><span>${svg_articulo}</span> ${articulo.titulo}</a>
                                    <p>${articulo.resumen}</p>
                                </div>
                                <div class="articulo-preview-etiquetas">
                                    ${Array.isArray(articulo.topicos) ? articulo.topicos.map(topico => `<span class="etiqueta">${topico.nombre}</span>`).join('') : ''}
                                </div>
                                <div class="articulo-preview-fecha">
                                    <p>Fecha de publicación - ${articulo.fecha_envio}</p>
                                </div>
                            </div>
                            `;
                        });
                        container.innerHTML = res;
                        document.querySelectorAll('.articulo-preview').forEach((preview) => {
                            preview.addEventListener('click', () => {
                                const a = preview.querySelector('a');
                                if (a) {
                                    a.click();
                                }
                            });
                        });
                        actualizarPaginacion();
                    });
            } else {
                container.innerHTML = `<p>No se encontraron resultados.</p>`;
                actualizarPaginacion();
            }
            document.getElementById("filtro-num-resultados").innerHTML = `${data.total} resultados`;
        })
        .catch(error => {
            container.innerHTML = `<p>Error al obtener datos, prueba de nuevo.</p>`;
            container.innerHTML += `<p>${error}</p>`
        });
}

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
    
    form.dispatchEvent(new Event('submit'));
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