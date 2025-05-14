const params = new URLSearchParams(window.location.search);
let paginaActual = params.has('offset') ? parseInt(params.get('offset')) : 0;
const resultadosPorPagina = 20;
let totalResultados = 0;

const container = document.querySelector('.filtro-container');
const resContainer = document.getElementById('resultados-busqueda');

const form = document.getElementById('filtro-form');
const overlay = document.getElementById('filtro-overlay');

const numResultados = document.querySelectorAll('#filtro-num-resultados');
const paginaInfo = document.getElementById('pagina-info');
const btnAnterior = document.getElementById('btnAnterior');
const btnSiguiente = document.getElementById('btnSiguiente');

btnAnterior.addEventListener('click', () => {
    if (paginaActual > 0) {
        paginaActual--;
        form.requestSubmit();
    }
});

btnSiguiente.addEventListener('click', () => {
    if ((paginaActual + 1) * resultadosPorPagina < totalResultados) {
        paginaActual++;
        form.requestSubmit();
    }
});

const ordenarPorSelect = document.getElementById('ordenar_por');
ordenarPorSelect.addEventListener('change', () => form.requestSubmit());

form.addEventListener('submit', (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const nuevoParams = new URLSearchParams();

    const revisadoChecked = formData.get("revisado") === "on" ? 1 : (params.has("revisado") ? parseInt(params.get("revisado")) : 1);
    nuevoParams.append("revisado", revisadoChecked ? 1 : 0);
    
    if (!(location.pathname === '/buscar')) {
        const revisoresChecked = formData.get("necesita-revisores") === "on" ? 1 : 0;
        nuevoParams.append("necesita-revisores", revisoresChecked ? 1 : 0);
    }


    for (const [k, v] of formData.entries()) if (k !== "necesita-revisores" && k !== "revisado") if (v.trim() !== "") {
        nuevoParams.append(k, v);
    }

    if (paginaActual !== 0) nuevoParams.append("offset",paginaActual);

    const nuevoHref = `${location.pathname}?${nuevoParams.toString()}`;
    window.location.href = nuevoHref;
});

const clickFuera = (e) => {
    if (!container.contains(e.target)) {
        container.classList.remove('filtro-container-activo');
        overlay.classList.remove('filtro-overlay-activo');
        document.body.classList.remove('no-scroll');
        document.removeEventListener('click', clickFuera);
    }
};

const toggleFC = () => {
    const activo = container.classList.toggle("filtro-container-activo");
    overlay.classList.toggle("filtro-overlay-activo", activo);
    document.body.classList.toggle("no-scroll");

    if (activo) {
        setTimeout(() => document.addEventListener("click", clickFuera), 0);
    } else {
        document.removeEventListener("click", clickFuera);
    }
};

const cargarArticulos = async () => {
    const resp = await fetch(`/php/api/filtrar.articulos.php?${params.toString()}`);
    const data = await resp.json();

    if (!data || !Array.isArray(data.data) || data.total === 0) {
        const noArticulos = document.createElement('p')
        noArticulos.textContent = 'No se encontraron articulos...';

        resContainer.appendChild(noArticulos);
        return;
    }

    const articulos = data.data;
    totalResultados = data.total;

    if (numResultados) numResultados.forEach(numContainer => {
        numContainer.innerHTML = totalResultados + " articulos encontrados";
    });

    const aux = document.createElement('div');
    aux.id = "resultados-busqueda";

    if (articulos) for (const articulo of articulos) {
        const articuloPreview = await crearPreview(articulo);
        aux.appendChild(articuloPreview);
    }

    const totalPaginas =  Math.max(1,Math.ceil(totalResultados/resultadosPorPagina));
    paginaInfo.textContent = `Pagina ${paginaActual + 1} de ${totalPaginas}`;
    resContainer.replaceWith(aux);
}

cargarArticulos();