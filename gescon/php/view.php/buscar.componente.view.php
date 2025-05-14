<?php


$svg_filtro = getAsset("/svg/filtro.svg");
$svg_ordenar = getAsset("/svg/ordenar.svg");
$svg_buscar = getAsset("/svg/buscar.svg");
$titulo = isset($titulo) ? $titulo : "Articulos";
include "buscar.componente.filtro.view.php" 
?>

<div class="buscar-container">
    <div class="buscar-filtros-container">
        <h1><?= htmlspecialchars($titulo) ?></h1>
        <?php include "buscar.componente.busqueda.view.php" ?>
    </div>
    <span id="filtro-num-resultados"></span>
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