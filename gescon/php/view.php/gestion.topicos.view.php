<div class="tab tab-activo">
    <button id="modalBtn" data-target="crear-topico-container">+ Crear topico/especialidad</button>
    <div class="topicos-container"> <!-- topicos --> </div>
</div>
<div class="modal" id="crear-topico-container">
    <div class="modal-content">
        <form class="crear-topico-form formulario" method="POST">
            <p>Crea un topico nuevo</p>
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