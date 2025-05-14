<?php


$filtro_extra = isset($filtro_extra) ? $filtro_extra : false;
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
        <?php if ($filtro_extra === true) : ?>
            <span>Filtros extra:</span>
            <div class="checkbox-div">
                <label class="checkbox-label" for="necesita-revisores">
                    <input type="checkbox" class="checkbox" id="necesita-revisores" name="necesita-revisores">
                    <span class="checkbox-slider"></span>
                </label>
                <label class="reset-label" for="necesita-revisores">Necesita revisores</label>
                <!-- HACER ESTE FILTRO!!!! -->
            </div>
        <?php endif ?>
        <br>
        <button type="submit" onclick="toggleFC()">Filtrar</button>
    </form>
</div>
<div id="filtro-overlay"></div>