<div class="tab" id="tabArticulos">
    <div class="profile-articulos-container">
        <div class="switch-container">
            <div class="switch-sub-container">
                <label class="checkbox-label" for="articulos-revisados">
                    <input type="checkbox" class="checkbox" id="articulos-revisados" name="articulos-revisados">
                    <span class="checkbox-slider"></span>
                </label>
                <label class="reset-label" for="articulos-revisados">Solo mis articulos evaluados</label>
            </div>
        </div>
        <p id="num-articulos"></p>
        <div class="profile-articulos-results">
            <!-- articulos del usuario -->
        </div>
    </div>
</div>
<script src=<?php getJs("crearPreview");?>></script>
<script src=<?php getJs("usuarioArticulos");?>></script>