const resultadosPorPagina = 20;
let totalResultados = 0;

const resContainer = document.getElementById('resultados-busqueda');

const paginasNav = document.querySelector('.paginas-nav');
const paginaInfo = document.getElementById('pagina-info');
const btnAnterior = document.getElementById('btnAnterior');
const btnSiguiente = document.getElementById('btnSiguiente');

btnAnterior.addEventListener('click', () => {
    if (paginaActual > 0) {
        paginaActual--;
        cambioDePagina = true;
        form.requestSubmit();
    }
});

btnSiguiente.addEventListener('click', () => {
    if ((paginaActual + 1) * resultadosPorPagina < totalResultados) {
        paginaActual++;
        cambioDePagina = true;
        form.requestSubmit();
    }
});

const cargarArticulos = async () => {
    iniciarCarga(); // inicia barra de carga
    try {
        cargarFiltros();
    } catch (error) {
        console.error(error);
    }
    
    const resp = await fetch(`/php/api/filtrar.articulos.php?${params.toString()}`);
    const data = await resp.json();

    progreso = progreso >= 50 ? progreso : 50; // barra de carga xd

    const articulos = data.data;
    totalResultados = data.total;

    if (numResultados) numResultados.forEach(numContainer => {
        numContainer.innerHTML = totalResultados + " articulos encontrados";
    });

    const aux = document.createElement('div');
    aux.id = "resultados-busqueda";

    if (articulos.length > 0) for (const articulo of articulos) {
        const articuloPreview = await crearPreview(articulo);
        aux.appendChild(articuloPreview);
    } else {
        const noArticulos = document.createElement('span')
        noArticulos.textContent = 'No se encontraron articulos...';

        aux.appendChild(noArticulos);
    }
    progreso = 100; // termina la carga

    const totalPaginas =  Math.max(1,Math.ceil(totalResultados/resultadosPorPagina));
    paginaInfo.textContent = `Pagina ${paginaActual + 1} de ${totalPaginas}`;
    paginasNav.style.display = "flex";
    resContainer.replaceWith(aux);
}

cargarArticulos();