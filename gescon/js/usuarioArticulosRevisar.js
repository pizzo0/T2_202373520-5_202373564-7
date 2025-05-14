let totalArticulosRevisor = 0;
const containerRevisar = document.querySelector('.profile-revisiones-container');
const numRevisar = document.getElementById('num-articulos-revisar');
const filtroRevisadosRevisor = document.getElementById('articulos-revisados-revisor');

document.addEventListener('DOMContentLoaded', () => {
    revisarFiltroRevisor();
});

filtroRevisadosRevisor.addEventListener('click', () => {
    revisarFiltroRevisor();
});

revisarFiltroRevisor = () => {
    totalArticulosRevisor = 0;
    if (filtroRevisadosRevisor.checked) {
        cargarArticulosRevisor(true);
    } else {
        cargarArticulosRevisor(false);
    }
}

cargarArticulosRevisor = (noEstaRevisado = false) => {
    fetch('/php/api/actual.usuario.articulos.revisar.php')
        .then((response) => response.json())
        .then((data) => {
            if (data.total > 0) {
                fetch(`assets/svg/svg_articulo.svg`)
                    .then(response => response.text())
                    .then(svg => {
                        fetch(`/php/api/actual.data.usuario.php`)
                            .then(response => response.json())
                            .then((usuarioData) => {
                                containerRevisar.innerHTML = '';
                                svg_articulo = svg;
                                let res = ``;
                                data.data.forEach(articulo => {
                                    let hizoRevision = false;
                                    if (articulo.formularios) articulo.formularios.forEach((formulario) => {
                                        if (formulario.email_revisor === usuarioData.email && !hizoRevision) {
                                            hizoRevision = true;
                                        }
                                    });

                                    if (hizoRevision && noEstaRevisado) {
                                        return;
                                    }
                                    totalArticulosRevisor++;
                                    // `articulo-flag`
                                    res += `
                                    <div class="articulo-preview ${!hizoRevision ? `articulo-flag` : ``}">
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
                                if (totalArticulosRevisor === 0) {
                                    res += `
                                    <div class="articulo-preview">
                                        <div class="articulo-preview-tr">
                                            <p>No hay articulos</p>
                                        </div>
                                    </div>
                                    `;
                                }
                                containerRevisar.innerHTML += res;
                                document.querySelectorAll('.articulo-preview').forEach((preview) => {
                                    preview.addEventListener('click', () => {
                                        const a = preview.querySelector('a');
                                        if (a) {
                                            a.click();
                                        }
                                    });
                                });
                                numRevisar.innerHTML = `Tienes ${totalArticulosRevisor} articulos publicados ${noEstaRevisado ? '[Por revisar]' : ''}`;
                            });
                        });
            } else {
                containerRevisar.innerHTML = `<p>Aun no publicas articulos</p>`;
                numRevisar.innerHTML = `Tienes 0 articulos publicados ${noEstaRevisado ? '[Por revisar]' : ''}`;
            }
        });
};