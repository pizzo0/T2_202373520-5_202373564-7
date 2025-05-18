<div class="buscar-container">
    <div class="main-buscar-filtros-container">
        <button id="modalBtn" data-target="crear-topico-container">+ Crear topico/especialidad</button>
        <?php
        $btn_texto = "Filtrar topicos";
        include "componente.filtro.btn.php";
        ?>
    </div>
    <div id="filtro-view"></div>
    <div class="topicos-container"> <!-- topicos --> </div>
</div>

<?php
$filtros_componente = "gestion.topicos.componente.filtros.php";
include "componente.filtro.php"
?>

<div class="modal" id="crear-topico-container">
    <div class="modal-top">
        <h1>Crea un topico nuevo</h1>
    </div>
    <div class="modal-content">
        <form class="crear-topico-form formulario" method="POST">
            <div>
                <label for="topico">Topico</label>
                <input type="text" id="topico" name="topico" required>
            </div>
            <div class="btns-container">
                <button type="submit">Crear</button>
                <button class="btn-rojo" type="button" id="modalBtn" data-target="crear-topico-container">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<div class="modal-overlay" data-overlay-target="crear-topico-container"> </div>

<script src=<?php getJs("getTopicos");?>></script>