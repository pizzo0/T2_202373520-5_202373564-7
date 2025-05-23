<div class="menu-perfil big-border-radius">
    <div class="modal-top">
        <h1>Opciones</h1>
    </div>
    <div class="menu-perfil-forms">
        <form method="post" class="formulario nombre-email-form form-activo">
            <div class="form-div">
                <h2>Cambiar nombre y/o correo.</h2>
                <div class="input-container">
                    <label for="nombre">Cambiar nombre</label>
                    <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($user["nombre"] ?? "") ?>" required>
                </div>
                <div class="form-div">
                    <div class="input-container">
                        <label for="correo">Cambiar email</label>
                        <input type="text" name="correo" id="correo" value="<?= htmlspecialchars($user["email"] ?? "") ?>" required>
                    </div>
                </div>
                <button type="submit">Guardar cambios</button>
            </div>
            <div class="btns-container">
                <?php if ($user['id_rol'] != 3) : ?>
                    <button type="button" clasS="btn-rojo modalBtnEliminarCuenta" id="modalBtn" data-target="eliminar-cuenta">Eliminar cuenta</button>
                <?php endif; ?>
                <button type="button" class="btn-rojo" onclick="window.location.href='/logout'" >Cerrar sesion</button>
            </div>
        </form>
        <form method="post" class="formulario pass-form">
            <h2>Cambiar contraseña</h2>
            <div class="form-div">
                <div class="input-container">
                    <label for="old_pass">Contraseña anterior</label>
                    <input type="password" name="old_pass" id="old_pass" required>
                </div>
                <div class="input-container">
                    <label for="pass">Contraseña</label>
                    <input type="password" name="pass" id="pass" required>
                </div>
                <div class="input-container">
                    <label for="pass_confirm">Confirmar contraseña</label>
                    <input type="password" name="pass_confirm" id="pass_confirm" required>
                </div>
                <button type="submit" class="mid-btn">Guardar nueva contraseña</button>
            </div>
        </form>
        <div class="btns-container no-wrap">
            <button class="mid-btn" id="toggle-editar-perfil">Cambiar contraseña</button>
            <button class="mid-btn" id="editar-perfil-btn">Salir de opciones</button>
        </div>
    </div>
</div>
<div class="menu-overlay"></div>

<?php if ($user['id_rol'] != 3) : ?>           
    <div class="modal-overlay" data-overlay-target="eliminar-cuenta"></div>
    <div class="modal" id="eliminar-cuenta">
        <div class="modal-top">
            <h1>Eliminar cuenta</h1>
        </div>
        <div class="modal-content">
            <p>Para eliminar tu cuenta definitivamente, ingresa tu contraseña:</p>
            <form method="POST" class="formulario form-gap">
                <div class="input-container">
                    <label for="confirmar-eliminar">Contraseña</label>
                    <input type="password" name="confirmar-eliminar" id="confirmar-eliminar" required>
                </div>
                <div class="btns-container">
                    <button type="submit" class="btn-rojo">Eliminar cuenta</button>
                    <button type="button" id="cerrar-eliminar-cuenta">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>