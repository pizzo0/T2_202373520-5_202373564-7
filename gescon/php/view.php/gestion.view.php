<?php


$user = getUsuarioData();
?>
<?php if ($user['id_rol'] < 3) : ?>
    <?php include '404.view.php'; ?>
<?php else : ?>
    <div id="crear-revisor-container">
        <h1>Registrar un revisor</h1>
        <form method="post" id="crear-revisor-form" class="formulario">
            <div class="inputs-container">
                <div class="input-container">
                    <label for="rut">Rut</label>
                    <input type="text" id="rut" name="rut" required>
                </div>
                <div class="input-container">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="input-container">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" placeholder="revisor@gescon.com" required>
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
    <div class="menu-overlay"></div>
    <button id="toggle_crear_revisor">+ Crear revisor</button>
    <div class="revisores-container">
        <!-- revisores -->
    </div>
    <script src=<?php getJs("cargarRevisores");?>></script>
<?php endif ?>