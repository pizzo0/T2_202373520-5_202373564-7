<div class="tab tab-activo">
    <button id="toggle_crear_revisor">+ Crear revisor</button>
    <div class="revisores-container"> <!-- revisores --> </div>
</div>

<div class="modal" id="modificar-revisor-container"></div>
<div class="modal-overlay" data-overlay-target="modificar-revisor-container"></div>

<div class="menu-overlay"></div>
<div id="crear-revisor-container" class="big-border-radius">
    <h1>Registrar un revisor</h1>
    <form method="post" id="crear-revisor-form" class="formulario">
        <div class="inputs-container">
            <div class="input-container">
                <label for="rut">Rut</label>
                <input type="text" id="rut" name="rut" <?php
                    if (isset($_POST['pass'])) echo "value=" . $_POST['rut'];
                ?> required>
            </div>
            <div class="input-container">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" <?php
                    if (isset($_POST['pass'])) echo "value=" . $_POST['nombre'];
                ?> required>
            </div>
            <div class="input-container">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" placeholder="revisor@gescon.com" <?php
                    if (isset($_POST['pass'])) echo "value=" . $_POST['correo'];
                ?> required>
            </div>
            <div class="input-container">
                <label for="pass">Contraseña</label>
                <input type="password" id="pass" name="pass" placeholder="********" required>
            </div>
            <div class="input-container">
                <label for="pass_confirm">Contraseña</label>
                <input type="password" id="pass_confirm" name="pass_confirm" placeholder="********" required>
            </div>
        </div>
        <div class="btns-container">
            <button type="submit">Registrar</button>
            <button id="toggle_crear_revisor" class="btn-rojo">Cancelar</button>
        </div>
    </form>
</div>

<script src=<?php getJs("modificarRevisores");?>></script>