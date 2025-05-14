const crearPreview = async (articulo,hizoRevision = false) => {
    const wrapper = document.createElement("div");
    wrapper.className = "articulo-preview" + (hizoRevision ? ` articulo-flag` : '');

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