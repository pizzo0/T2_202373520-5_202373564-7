let totalArticulos = 0;
const container = document.getElementsByClassName('profile-articulos-results')[0];
const numeroArticulos = document.getElementById('num-articulos');
const filtroRevisados = document.getElementById('articulos-revisados');

document.addEventListener('DOMContentLoaded', () => {
    revisarFiltro();
});

filtroRevisados.addEventListener('click', () => {
    revisarFiltro();
});

revisarFiltro = () => {
    totalArticulos = 0;
    if (filtroRevisados.checked) {
        cargarArticulosAutor(true);
    } else {
        cargarArticulosAutor(false);
    }
}

cargarArticulosAutor = (estaRevisado = false) => {
    fetch('/php/api/actual.usuario.articulos.php')
        .then((response) => response.json())
        .then((data) => {
            if (data.total > 0) {
                fetch(`assets/svg/svg_articulo.svg`)
                    .then(response => response.text())
                    .then(svg => {
                        container.innerHTML = '';
                        svg_articulo = svg;
                        let res = ``;
                        data.data.forEach(articulo => {
                            if (!articulo.revisado && estaRevisado) {
                                return;
                            }
                            totalArticulos++;
                            res += `
                            <div class="articulo-preview ${!articulo.revisado ? `articulo-flag` : ``}">
                                <div class="articulo-preview-tr">
                                    <a href="/articulo/${articulo.id_articulo}"><span>${svg_articulo}</span> ${articulo.titulo}</a>
                                    <p>${articulo.resumen}</p>
                                </div>
                                <div class="articulo-preview-etiquetas">
                                    ${articulo.topicos.map(topico => `<span class="etiqueta">${topico.nombre}</span>`).join('')}
                                </div>
                                <div class="articulo-preview-autores">
                                    ${articulo.autores.map(autor => `<span clasS="etiqueta rol-1">${autor.nombre}</span>`).join('')}
                                </div>
                                <div class="articulo-preview-fecha">
                                    <p>Publicación - ${obtenerTiempo(articulo.fecha_envio)}</p>
                                </div>
                            </div>
                            `;
                        });
                        if (totalArticulos === 0) {
                            res += `
                            <div class="articulo-preview">
                                <div class="articulo-preview-tr">
                                    <p>No hay articulos</p>
                                </div>
                            </div>
                            `;
                        }
                        container.innerHTML += res;
                        document.querySelectorAll('.articulo-preview').forEach((preview) => {
                            preview.addEventListener('click', () => {
                                const a = preview.querySelector('a');
                                if (a) {
                                    a.click();
                                }
                            });
                        });
                        numeroArticulos.innerHTML = `Tienes ${totalArticulos} articulos publicados ${estaRevisado ? '[Revisados]' : ''}`;
                    });
            } else {
                container.innerHTML = `<p>Aun no publicas articulos</p>`;
                numeroArticulos.innerHTML = `Tienes 0 articulos publicados ${estaRevisado ? '[Revisados]' : ''}`;
            }
        });
}