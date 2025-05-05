const container = document.getElementsByClassName('profile-articulos-results')[0];
const numeroArticulos = document.getElementById('num-articulos');

document.addEventListener('DOMContentLoaded', () => {
    fetch('/php/api/actual.usuario.articulos.php')
        .then((response) => response.json())
        .then((data) => {
            if (data.total > 0) {
                fetch(`assets/svg/svg_articulo.svg`)
                    .then(response => response.text())
                    .then(svg => {
                        svg_articulo = svg;
                        let res = ``;
                        data.data.forEach(articulo => {
                            res += `
                            <div class="articulo-preview">
                                <div class="articulo-preview-tr">
                                    <a href="/editar/${articulo.articulo_id}"><span>${svg_articulo}</span> ${articulo.titulo}</a>
                                    <p>${articulo.resumen}</p>
                                </div>
                                <div class="articulo-preview-etiquetas">
                                    ${articulo.topicos.map(topico => `<span class="etiqueta">${topico.nombre}</span>`).join('')}
                                </div>
                                <div class="articulo-preview-fecha">
                                    <p>Fecha de publicación - ${articulo.fecha_envio}</p>
                                </div>
                            </div>
                            `;
                        });
                        container.innerHTML += res;
                        document.querySelectorAll('.articulo-preview').forEach((preview) => {
                            preview.addEventListener('click', () => {
                                const a = preview.querySelector('a');
                                if (a) {
                                    a.click();
                                }
                            });
                        });
                    });
            } else {
                container.innerHTML += `<p>Aun no publicas articulos</p>`;
            }
            numeroArticulos.innerHTML = `Tienes ${data.total} articulos publicados`;
        });
});