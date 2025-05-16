const params = new URLSearchParams(window.location.search);
let paginaActual = params.has('offset') ? parseInt(params.get('offset')) : 0;
const resultadosPorPagina = 20;
let totalResultados = 0;
let cambioDePagina = false;

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

filtrosView.addEventListener('wheel', e => {
    if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
        e.preventDefault();
        filtrosView.scrollLeft += e.deltaY;
    }
},{passive:false});

ordenarPorSelect.addEventListener('change', () => form.requestSubmit());

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

    if (paginaActual !== 0 && cambioDePagina) {
        nuevoParams.append("offset",paginaActual);
    }

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

    const topicosSelect = document.getElementById('topicos');

    params.forEach((v,k) => {
        if ((k == "revisado" || k == "necesita-revisores") && v == 0) return;

        let key = k;
        let val = v;

        if (val == 1 && key != "topicos") val = "Activo";
        if (val == 0 && key != "topicos") val = "Apagado";

        if (key == "ordenar_por") {
            key = "Ordenar por";
            val = ordenarPorSelect.options[ordenarPorSelect.options.selectedIndex].label;
        }
        if (key == "topicos") {
            key = "topico";
            opcion = Array.from(topicosSelect.options).find(opt => opt.value === v);
            val = opcion?.label;
        }
        if (key == "id_articulo") key = "ID del Articulo";
        if (key == "fecha_desde") key = "fecha desde";
        if (key == "fecha_hasta") key = "fecha_hasta";
        if (key == "necesita-revisores") key = "necesita revisores";
        
        key = String(key).charAt(0).toUpperCase() + String(key).slice(1);

        const filtroItem = document.createElement('span');
        filtroItem.className = 'filtro-etiqueta';
        filtroItem.setAttribute('data-filtro-target',k);
        filtroItem.textContent = key + ": " + val;

        filtroItem.addEventListener('click', () => {
            params.delete(k);

            const input = document.querySelector(`[name="${k}"]`);
            if (input) {
                if (input.tagName == "SELECT") {
                    if(input.options[0]) {
                        input.value = input.options[0].value;
                    }
                } else {
                    input.value = '';
                }
                form.requestSubmit();
            }
            
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