<div class="main-buscar-container">
    <input type="text" class="l-input" name="titulo" form="filtro-form" placeholder="Escribe el titulo de un articulo aqui" value="<?= $_POST['titulo'] ?? '' ?>">
    <button type="submit" class="r-button" form="filtro-form"><?= $svg_buscar ?></button>
</div>
<div class="main-buscar-filtros-container">
    <button type="button" id="btn-filtrar" onclick="toggleFC()"><span><?= $svg_filtro ?></span> Filtrar resultados</button>
    <div class="ordenar-container" id="select">
        <label for="ordenar_por">Ordenar por:</label>
        <select class="select-input" style="width:200px;" name="ordenar_por" id="ordenar_por">
            <option value="fecha_envio_desc">Fecha de publicación (reciente primero)</option>
            <option value="fecha_envio_asc">Fecha de publicación (antiguo primero)</option>
            <option value="contacto_asc">Contacto [Autor] (A-Z)</option>
            <option value="contacto_desc">Contacto [Autor] (Z-A)</option>
            <option value="titulo_asc">Título (A-Z)</option>
            <option value="titulo_desc">Título (Z-A)</option>
        </select>
    </div>
</div>
<div class="paginas-nav">
    <span id="pagina-info"></span>
    <button id="btnAnterior"><</button>
    <button id="btnSiguiente">></button>
</div>