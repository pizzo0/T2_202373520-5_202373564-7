<?php
// $user = getUsuarioData();
// filtro por autor, fecha de envio, topicos y revisor -> resultado da nombre del articulo, resumen y topicos
?>
<div class="filtro-container">
    <form method="post" id="filtro-form">
        <span id="filtro-num-resultados"><!-- resultados --></span>
        <div>
            <label for="titulo">Titulo:</label>
            <input type="text" name="titulo" id="titulo">
        </div>
        <div>
            <label for="autor">Autor:</label>
            <input type="text" name="autor" id="autor">
        </div>
        <div>
            <label>Fecha:</label>
            <label for="fecha_desde">Desde:</label>
            <input type="date" name="fecha_desde" id="fecha_desde">
            <label for="fecha_hasta">Hasta:</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta">
        </div>
        <div>
            <label for="topicos">Topico:</label>
            <select name="topicos" id="topicos">
                <option value="">Seleccionar topico</option>
            </select>
        </div>
        <div>
            <label for="revisor">Revisor:</label>
            <input type="text" name="revisor" id="revisor">
        </div>
        <button type="submit" onclick="toggleFC()">Filtrar</button>
    </form>
</div>
<div id="filtro-overlay"></div>
<div class="buscar-container">
    <button id="btn-filtrar" onclick="toggleFC()">Filtrar resultados</button>
    <div id="resultados-busqueda">
        <!-- cargan los resultados -->
    </div>
</div>

<script src=<?php getJs(js_file: "getArticulosFiltrados");?>></script>