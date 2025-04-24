const container = document.getElementsByClassName('filtro-container')[0];
const overlay = document.getElementById('filtro-overlay');

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
    const queryString = filtros ? `?${filtros.toString()}` : '';

    console.log(`php/api/filtrar.articulos.php${queryString}`);

    fetch(`php/api/filtrar.articulos.php${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (data.total > 0) {
                let res = ``;
                data.data.forEach(articulo => {
                    res += `
                    <div class="articulo-preview">
                        <h2 class="articulo-preview-titulo">${articulo.titulo}</h2>
                        <p class="articulo-preview-resumen">${articulo.resumen}</p>
                        <p class="articulo-preview-fecha">Fecha de publicación: ${articulo.fecha_envio}</p>
                        <div class="articulo-preview-etiquetas">
                            ${articulo.topicos.split(',').map(topico => `<span class="etiqueta">${topico}</span>`).join('')}
                        </div>
                    </div>
                    `;
                });
                container.innerHTML = res;
            } else {
                container.innerHTML = `<p>No se encontraron resultados.</p>`;
            }
            document.getElementById("filtro-num-resultados").innerHTML = `${data.total} resultados`;
        })
        .catch(error => {
            container.innerHTML = `<p>Error al obtener datos, prueba de nuevo.</p>`;
        });
}

window.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filtro-form');
    const filtros = form ? new URLSearchParams(new FormData(form)) : null;
    cargarArticulos(filtros);
});

document.getElementById('filtro-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const filtros = new URLSearchParams(new FormData(e.target));
    cargarArticulos(filtros);
});

document.addEventListener("DOMContentLoaded", () => {
    fetch("php/api/topicos.php")
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