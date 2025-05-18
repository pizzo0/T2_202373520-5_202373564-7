<?php


$svg_ordenar = getAsset("/svg/ordenar.svg");
$svg_buscar = getAsset("/svg/buscar.svg");
$titulo = isset($titulo) ? $titulo : "Articulos";
?>

<div class="buscar-container">
    <div class="buscar-filtros-container">
        <h1><?= htmlspecialchars($titulo) ?></h1>
        <?php include "buscar.componente.busqueda.view.php" ?>
    </div>
    <div id="filtro-view"></div>
    <span id="filtro-num-resultados"></span>
    <div id="resultados-busqueda">
        <!-- cargan los resultados -->
    </div>
</div>

<?php
$filtros_componente = "buscar.componente.filtros.view.php";
include "componente.filtro.php";
?>

<script>
    document.querySelector('input[name="titulo"]').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.querySelector('.r-button').click();
        }
    });
</script>

<script src=<?php getJs(js_file: "getArticulosFiltrados");?>></script>