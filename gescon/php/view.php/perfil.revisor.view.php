<div class="tab" id="tabRevisiones">
    <div class="profile-articulos-container">
        <p id="num-articulos-revisar"></p>
        <div class="switch-container">
            <label class="checkbox-label" for="articulos-revisados-revisor">
                <input type="checkbox" class="checkbox" id="articulos-revisados-revisor" name="articulos-revisados-revisor">
                <span class="checkbox-slider"></span>
            </label>
            <label class="reset-label" for="articulos-revisados-revisor">Solo articulos no revisados por ti</label>
        </div>
        <div class="profile-revisiones-container">
            <!-- articulos que puede revisar -->
        </div>
    </div>
</div>
<script src=<?php getJs("usuarioArticulosRevisar");?>></script>