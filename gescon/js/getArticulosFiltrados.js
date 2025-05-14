const params = new URLSearchParams(window.location.search);
let paginaActual = params.get('offset') ? parseInt(params.get('offset')) : 0;
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

    for (const [k,v] of formData.entries()) if (v.trim() !== "") {
        if (k === "necesita-revisores") {
            nuevoParams.append(k,"1");
            continue;
        }
        nuevoParams.append(k,v);
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

let crearPreview = async (articulo) => {
    const wrapper = document.createElement("div");
    wrapper.className = "articulo-preview";

    const contacto = document.createElement("div");
    contacto.className = "articulo-preview-contacto";

    const spanContacto = document.createElement("span");
    spanContacto.textContent = "Contacto - " + articulo.contacto.nombre;

    contacto.appendChild(spanContacto);

    const tr = document.createElement("div");
    tr.className = "articulo-preview-tr";

    const enlace = document.createElement("a");
    enlace.href = `/articulo/${articulo.id_articulo}`;

    const icono = document.createElement("span");
    const resp = await fetch(`/assets/svg/svg_articulo.svg`);
    icono.innerHTML = await resp.text();
    enlace.appendChild(icono);
    enlace.append(` ${articulo.titulo}`);

    tr.appendChild(enlace);

    const resumen = document.createElement("p");
    resumen.textContent = articulo.resumen;
    tr.appendChild(resumen);

    const etiquetas = document.createElement("div");
    etiquetas.className = "articulo-preview-etiquetas";
    if (Array.isArray(articulo.topicos)) {
        articulo.topicos.forEach(t => {
            const span = document.createElement("span");
            span.className = "etiqueta";
            span.textContent = t.nombre;
            etiquetas.appendChild(span);
        });
    }

    const autores = document.createElement("div");
    autores.className = "articulo-preview-autores";
    if (Array.isArray(articulo.autores)) {
        articulo.autores.forEach(a => {
            const span = document.createElement("span");
            span.className = "etiqueta rol-1";
            span.textContent = a.nombre;
            autores.appendChild(span);
        });
    }

    const fecha = document.createElement("div");
    fecha.className = "articulo-preview-fecha";
    const pFecha = document.createElement("p");
    pFecha.textContent = `Publicación - ${obtenerTiempo(articulo.fecha_envio)}`;
    fecha.appendChild(pFecha);

    wrapper.append(contacto,tr,etiquetas,autores,fecha)

    wrapper.addEventListener("click", () => enlace.click());

    return wrapper;
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