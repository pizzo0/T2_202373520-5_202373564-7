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

const filtrosView = document.getElementById('filtro-view');
const ordenarPorSelect = document.getElementById('ordenar_por');

ordenarPorSelect.addEventListener('change', () => form.requestSubmit());

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

form.addEventListener('submit', (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const nuevoParams = new URLSearchParams();

    const revisadoChecked = formData.get("revisado") === "on" ? 1 : 0;
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
    // se filtran los revisados automaticamente si estamos en /buscar :v
    if (!params.has("revisado") && (location.pathname === '/buscar')) {
        params.append("revisado", 1);
        document.getElementById('revisado').checked = true;
    }

    if (!params.has("ordenar_por")) {
        params.append("ordenar_por","fecha_envio_desc");
        ordenarPorSelect.value = "fecha_envio_desc";
    }

    const resp = await fetch(`/php/api/filtrar.articulos.php?${params.toString()}`);
    const data = await resp.json();

    const ordenarPorTexto = Array.from(ordenarPorSelect.options).find(opcion => opcion.selected).textContent;

    params.forEach((v,k) => {
        if (k == "revisado" && v == 0) return;

        let key = k;
        let val = v;

        if (val == 1) val = "Activo";
        if (val == 0) val = "Apagado";

        if (key == "ordenar_por") {
            key = "Ordenar por";
            val = ordenarPorTexto;
        }
        if (key == "id_articulo") key = "ID del Articulo";
        if (key == "topicos") key = "topico";
        if (key == "fecha_desde") key = "fecha desde";
        if (key == "fecha_hasta") key = "fecha_hasta";
        
        key = String(key).charAt(0).toUpperCase() + String(key).slice(1);

        const filtroItem = document.createElement('span');
        filtroItem.className = 'filtro-etiqueta';
        filtroItem.setAttribute('data-filtro-target',k);
        filtroItem.textContent = key + ": " + val;
        console.log(key + ": " + val)

        filtroItem.addEventListener('click', () => {
            params.delete(k);

            const input = form.querySelector(`[name="${k}"]`);
            if (input) input.remove();

            form.requestSubmit();
        });

        filtrosView.appendChild(filtroItem);
    });

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

    const totalPaginas =  Math.max(1,Math.ceil(totalResultados/resultadosPorPagina));
    paginaInfo.textContent = `Pagina ${paginaActual + 1} de ${totalPaginas}`;
    resContainer.replaceWith(aux);
}

cargarArticulos();