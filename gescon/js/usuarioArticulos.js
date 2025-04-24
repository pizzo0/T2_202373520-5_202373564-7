const container = document.getElementsByClassName('profile-articulos-container')[0];
const numeroArticulos = document.getElementById('num-articulos');

document.addEventListener('DOMContentLoaded', () => {
    fetch('php/api/actual.usuario.articulos.php')
        .then((response) => response.json())
        .then((data) => {
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
                container.innerHTML += res;
            } else {
                container.innerHTML += `<p>Aun no publicas articulos</p>`;
            }
            numeroArticulos.innerHTML = `Tienes ${data.total} articulos publicados`;
        })
})