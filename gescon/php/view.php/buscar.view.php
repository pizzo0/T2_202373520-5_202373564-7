<?php
// $user = getUsuarioData();
// filtro por autor, fecha de envio, topicos y revisor -> resultado da nombre del articulo, resumen y topicos

$svg_filtro = getAsset("/svg/filtro.svg");
$svg_ordenar = getAsset("/svg/ordenar.svg");
$svg_buscar = getAsset("/svg/buscar.svg");
?>
<div class="filtro-container">
    <form method="post" id="filtro-form">
        <span id="filtro-num-resultados"><!-- resultados --></span>
        <div>
            <span>Articulo en especifico:</span>
            <br>
            <label for="id_articulo">ID del Articulo:</label>
            <input type="text" name="id_articulo" id="id_articulo">
        </div>
        <div>
            <span>Información del Articulo:</span>
            <br>
            <label for="contacto">Contacto:</label>
            <input type="text" name="contacto" id="contacto">
        </div>
        <div>
            <label for="autor">Autor:</label>
            <input type="text" name="autor" id="autor">
        </div>
        <div>
            <label for="topicos">Topico:</label>
            <select class="select-input select-input-bigger" name="topicos" id="topicos">
                <option value="">Seleccionar topico</option>
            </select>
        </div>
        <div>
            <label for="revisor">Revisor:</label>
            <input type="text" name="revisor" id="revisor">
        </div>
        <div>
            <span>Fecha de publicación:</span>
            <br>
            <label for="fecha_desde">Desde:</label>
            <input type="date" name="fecha_desde" id="fecha_desde">
            <label for="fecha_hasta">Hasta:</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta">
        </div>
        <br>
        <button type="submit" onclick="toggleFC()">Filtrar</button>
    </form>
</div>
<div id="filtro-overlay"></div>

<div class="buscar-container">
    <div class="buscar-filtros-container">
        <h1>Articulos</h1>
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
    </div>
    <div id="resultados-busqueda">
        <!-- cargan los resultados -->
    </div>
</div>

<script>
    document.querySelector('input[name="titulo"]').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.querySelector('.r-button').click();
        }
    });
</script>
<script src=<?php getJs(js_file: "getArticulosFiltrados");?>></script>