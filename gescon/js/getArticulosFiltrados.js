
function toggleFC() {
    const container = document.getElementsByClassName('filtro-container')[0];
    container.classList.toggle('filtro-container-activo');
}

function cargarArticulos(filtros = null) {
    const container = document.getElementById('resultados-busqueda');
    const queryString = filtros ? `?${filtros.toString()}` : '';

    console.log(`php/api/filtrar.articulos.php${queryString}`);

    fetch(`php/api/filtrar.articulos.php${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (data.total > 0) {
                let res = `<div>`;
                data.data.forEach(articulo => {
                    res += `
                    <div>
                        <h2>${articulo.titulo}</h2>
                        <p>${articulo.resumen}</p>
                        <p>${articulo.fecha_envio}</p>
                        ${articulo.topicos.split(',').map(topico => `<span class="etiqueta">${topico}</span>`).join('')}
                    </div>
                    `;
                });
                res += `</div>`;
                container.innerHTML = res;
            } else {
                container.innerHTML = `No se encontraron resultados.`;
            }
        })
        .catch(error => {
            container.innerHTML = `Error al obtener datos, prueba de nuevo.`;
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