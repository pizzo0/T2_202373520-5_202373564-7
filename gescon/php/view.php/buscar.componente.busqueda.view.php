<?php


$titulo = isset($_GET['titulo']) ? ("value='" . $_GET['titulo'] . "'") : '';
$ordenar_por = isset($_GET['ordenar_por']) ? $_GET['ordenar_por'] : '';
?>
<div class="main-buscar-container">
    <input type="text" class="l-input" name="titulo" form="filtro-form" placeholder="Escribe el titulo de un articulo aqui" <?= $titulo ?>">
    <button type="submit" class="r-button" form="filtro-form"><?= $svg_buscar ?></button>
</div>
<div class="main-buscar-filtros-container">
    <button type="button" id="btn-filtrar" onclick="toggleFC()"><span><?= $svg_filtro ?></span> Filtrar resultados</button>
    <div class="ordenar-container" id="select">
        <label for="ordenar_por">Ordenar por:</label>
        <select class="select-input" style="width:200px;" name="ordenar_por" id="ordenar_por" form="filtro-form">
            <option value="fecha_envio_desc" <?= $ordenar_por ? ($ordenar_por === "fecha_envio_desc" ? 'selected' : '') : '' ?>>
                Más reciente
            </option>
            <option value="fecha_envio_asc" <?= $ordenar_por ? ($ordenar_por === "fecha_envio_asc" ? 'selected' : '') : '' ?>>
                Más antiguo
            </option>
            <option value="contacto_asc" <?= $ordenar_por ? ($ordenar_por === "contacto_asc" ? 'selected' : '') : '' ?>>
                Contacto de A-Z
            </option>
            <option value="contacto_desc" <?= $ordenar_por ? ($ordenar_por === "contacto_desc" ? 'selected' : '') : '' ?>>
                Contacto de Z-A
            </option>
            <option value="titulo_asc" <?= $ordenar_por ? ($ordenar_por === "titulo_asc" ? 'selected' : '') : '' ?>>
                Titulo de A-Z
            </option>
            <option value="titulo_desc" <?= $ordenar_por ? ($ordenar_por === "titulo_desc" ? 'selected' : '') : '' ?>>
                Titulo de Z-A
            </option>
        </select>
    </div>
</div>
<div class="paginas-nav">
    <span id="pagina-info"></span>
    <button id="btnAnterior"><</button>
    <button id="btnSiguiente">></button>
</div>