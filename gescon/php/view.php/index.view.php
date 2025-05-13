<?php


$svg_buscar = getAsset("/svg/buscar.svg");
?>
<div class="index-content">
    <h1 class="main-h1">{Gescon}</h1>
</div>
<div class="index-buscar-container main-buscar-container">
    <form method="POST" action="/buscar" class="main-buscar-container">
        <input class="l-input" type="text" name="titulo" placeholder="Escribe el titulo de un articulo aqui">
        <button class="r-button"><?= $svg_buscar ?></button>
    </form>
</div>