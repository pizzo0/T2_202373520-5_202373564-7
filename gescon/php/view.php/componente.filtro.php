<div class="filtro-container">
    <form method="GET" id="filtro-form">
        <span id="filtro-num-resultados"><!-- resultados --></span>
        <div id="filtros-form" class="filtros">
            <?php if (isset($filtros_componente)) include $filtros_componente; ?>
        </div>
        <div class="btns-container">
            <button type="submit" onclick="toggleFC()">Filtrar</button>
            <button type="button" onclick="toggleFC()" class="btn-rojo">Cancelar</button>
        </div>
    </form>
</div>
<div id="filtro-overlay"></div>
<script src=<?php getJs("filtro");?>></script>