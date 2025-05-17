<?php


$en_busqueda_principal = false;
if ($_SERVER['REQUEST_URI'] === '/buscar') {
    $en_busqueda_principal = true;
}
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
        <select class="select-input" style="max-width:130px;" name="ordenar_por" id="ordenar_por" form="filtro-form">
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
    <div>
        <button class="btn-w-icon btn-colorful" id="modalBtn" data-target="buscar-ayuda">
            <span class="btn-icon">
                <?= getAsset("/svg/info.svg") ?>
            </span>
        </button>
    </div>
</div>
<div class="paginas-nav">
    <span id="pagina-info">Pagina 0 de 0</span>
    <button id="btnAnterior"><</button>
    <button id="btnSiguiente">></button>
</div>

<div id="buscar-ayuda" class="modal">
    <div class="modal-top">
        <h1>Informacion sobre los articulos</h1>
        <button class="btn-w-icon" id="modalClose" data-close-target="buscar-ayuda">
            <span class="btn-icon">
                <?= getAsset('/svg/cerrar.svg') ?>
            </span>
        </button>
    </div>
    <div class="modal-content">
        <?php if ($en_busqueda_principal) : ?>
            <p>Tienes filtros a tu disposicion al apretar el boton "filtrar resultados", con los cuales puedes buscar un articulo con mas facilidad.</p>
            <p>Recuerda que tambien tienes la barra de busqueda, para buscar el titulo del articulo que buscas.</p>
        <?php endif; ?>
        <p>Como diferenciar los articulos:</p>
        <div class="articulo-preview">
            <div class="articulo-preview-tr">
                <p>Un articulo (<span><?= getAsset('/svg/svg_articulo.svg') ?></span>) tendra este estilo cuando esta en revision o ya haya sido revisado.</p>
            </div>
            <div class="articulo-preview-tr">
                <p>Los articulos que esten evaluados tendran un icono de "revisado" (<span title="Revisado" class="revisado-svg"><?= getAsset('/svg/revisado.svg') ?></span>) al final del titulo, con el cual puedes identificarlos.</p>
            </div>
        </div>
        <div class="articulo-preview articulo-flag2">
            <div class="articulo-preview-tr">
                <p>Un articulo tendra este estilo cuando los autores del mismo aun pueden editarlo.</p>
            </div>
        </div>
        <?php if (!$en_busqueda_principal) : ?>
            <div class="articulo-preview articulo-flag">
                <div class="articulo-preview-tr">
                    <p>Un articulo tendra este estilo cuando le falten revisores. Este estilo se superpondra a los demas.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="modal-overlay" data-overlay-target="buscar-ayuda"></div>