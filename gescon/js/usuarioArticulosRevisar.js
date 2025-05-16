let totalArticulosRevisor = 0;
const containerRevisar = document.querySelector('.profile-revisiones-container');
const numRevisar = document.getElementById('num-articulos-revisar');
const filtroRevisadosRevisor = document.getElementById('articulos-revisados-revisor');
const filtroEvaluado = document.getElementById('articulos-ya-evaluados');

const guardarFiltrosRevisor = () => {
    sessionStorage.setItem('filtroRevisadosRevisor', filtroRevisadosRevisor.checked);
    sessionStorage.setItem('filtroEvaluado', filtroEvaluado.checked);
}

document.addEventListener('DOMContentLoaded', () => {
    const revisadoSession = sessionStorage.getItem('filtroRevisadosRevisor');
    const evaluadoSession = sessionStorage.getItem('filtroEvaluado');

    if (revisadoSession || evaluadoSession) {
        filtroRevisadosRevisor.checked = revisadoSession === 'true';
        filtroEvaluado.checked = evaluadoSession === 'true';
    } else {
        filtroRevisadosRevisor.checked = false;
        filtroEvaluado.checked = true;
    }
    
    revisarFiltroRevisor();
});

filtroRevisadosRevisor.addEventListener('click', () => {
    filtroEvaluado.checked = false;
    revisarFiltroRevisor();
    guardarFiltrosRevisor();
});

filtroEvaluado.addEventListener('click', () => {
    filtroRevisadosRevisor.checked = false;
    revisarFiltroRevisor();
    guardarFiltrosRevisor();
});

revisarFiltroRevisor = () => {
    totalArticulosRevisor = 0;

    const ignorarRevisados = filtroRevisadosRevisor.checked ? true : false;
    const ignorarEvaluados = filtroEvaluado.checked ? true : false;

    cargarArticulosRevisor(ignorarRevisados,ignorarEvaluados);
}

cargarArticulosRevisor = async (noEstaRevisado = false, estaEvaluado = false) => {
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

                                    if (articulo.revisado && estaEvaluado) {
                                        return
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
                                numRevisar.innerHTML = `Tienes ${totalArticulosRevisor} articulos para revisar ${noEstaRevisado ? '[Por revisar]' : ''} ${estaEvaluado ? '[No evaluados]' : ''}`;
                            });
                        });
            } else {
                containerRevisar.innerHTML = `<p>No tienes articulos asignados</p>`;
                numRevisar.innerHTML = `Tienes 0 articulos para revisar ${noEstaRevisado ? '[Por revisar]' : ''} ${estaEvaluado ? '[No evaluados]' : ''}`;
            }
        });
};