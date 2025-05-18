<?php

$svg_filtro = getAsset("/svg/filtro.svg");
$btn_texto = isset($btn_texto) ? $btn_texto : "Filtrar resultados";
?>
<button type="button" id="btn-filtrar" onclick="toggleFC()"><span><?= $svg_filtro ?></span> <?= $btn_texto ?></button>