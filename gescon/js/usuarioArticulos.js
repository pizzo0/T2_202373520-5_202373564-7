let totalArticulos = 0;
const container = document.querySelector('.profile-articulos-results');
const numeroArticulos = document.getElementById('num-articulos');
const filtroRevisados = document.getElementById('articulos-revisados');
const tabArticulos = document.querySelector('[data-target=tabArticulos]');

let articulosCargados = false;

const guardarFiltro = () => {
    sessionStorage.setItem('filtroRevisados', filtroRevisados.checked);
}

document.addEventListener('DOMContentLoaded', () => {
    const revisadoSession = sessionStorage.getItem('filtroRevisados');

    if (revisadoSession) {
        filtroRevisados.checked = revisadoSession === 'true';
    } else {
        filtroRevisados.checked = false;
    }
});

tabArticulos.addEventListener('click', () => {
    revisarFiltro();
});

filtroRevisados.addEventListener('click', () => {
    articulosCargados = false;
    revisarFiltro();
    guardarFiltro();
});

revisarFiltro = () => {
    if (articulosCargados) {
        return;
    }
    container.innerHTML = '';
    totalArticulos = 0;
    reiniciarCarga();
    cargarArticulosAutor(filtroRevisados.checked ? true : false);
}

cargarArticulosAutor = async (estaRevisado = false) => {
    articulosCargados = true;
    fetch('/php/api/actual.usuario.articulos.php')
        .then((response) => response.json())
        .then((data) => {
            if (data.total > 0) {
                fetch(`assets/svg/svg_articulo.svg`)
                    .then(response => response.text())
                    .then(svg => {
                        container.innerHTML = '';
                        svg_articulo = svg;
                        data.data.forEach(async (articulo) => {
                            if (!articulo.revisado && estaRevisado) {
                                return;
                            }
                            totalArticulos++;
                            container.appendChild(await crearPreview(articulo));
                        });
                        if (totalArticulos === 0) {
                            container.innerHTML += `
                            <div class="articulo-preview">
                                <div class="articulo-preview-tr">
                                    <p>No hay articulos</p>
                                </div>
                            </div>
                            `;
                        }
                        document.querySelectorAll('.articulo-preview').forEach((preview) => {
                            preview.addEventListener('click', () => {
                                const a = preview.querySelector('a');
                                if (a) {
                                    a.click();
                                }
                            });
                        });
                        numeroArticulos.innerHTML = `Tienes ${totalArticulos} articulos publicados ${estaRevisado ? '[Evaluados]' : ''}`;
                    });
            } else {
                container.innerHTML = `<p>Aun no publicas articulos</p>`;
                numeroArticulos.innerHTML = `Tienes 0 articulos publicados ${estaRevisado ? '[Evaluados]' : ''}`;
            }
            progreso = 100;
        });
}