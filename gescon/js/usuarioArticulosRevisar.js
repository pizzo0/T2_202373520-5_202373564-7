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

cargarArticulosRevisor = async (noEstaRevisado = false) => {
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
                                data.data.forEach(async (articulo) => {
                                    let hizoRevision = false;
                                    if (articulo.formularios) articulo.formularios.forEach((formulario) => {
                                        if (formulario.revisor.rut === usuarioData.rut && !hizoRevision) {
                                            hizoRevision = true;
                                        }
                                    });

                                    if (hizoRevision && noEstaRevisado) {
                                        return;
                                    }
                                    totalArticulosRevisor++;
                                    containerRevisar.appendChild(await crearPreview(articulo,!hizoRevision));
                                });
                                if (totalArticulosRevisor === 0) {
                                    containerRevisar.innerHTML += `
                                    <div class="articulo-preview">
                                        <div class="articulo-preview-tr">
                                            <p>No hay articulos para revisar</p>
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
                                numRevisar.innerHTML = `Tienes ${totalArticulosRevisor} articulos ${noEstaRevisado ? '[Por revisar]' : ''}`;
                            });
                        });
            } else {
                containerRevisar.innerHTML = `<p>No tienes articulos asignados</p>`;
                numRevisar.innerHTML = `Tienes 0 articulos ${noEstaRevisado ? '[Por revisar]' : ''}`;
            }
        });
};