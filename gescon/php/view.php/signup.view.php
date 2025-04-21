<?php global $error; ?>

<div class="menu">
    <h1>Regístrate</h1>
    <form method="post" class="formulario">
        <div>
            <label for="rut">Rut</label>
            <input type="text" id="rut" name="rut" placeholder="11.111.111-1"
                value="<?= isset($_POST['rut']) ? htmlspecialchars($_POST['rut']) : '' ?>" required>
        </div>
        <div>
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre"
                value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>" required>
        </div>
        <div>
            <label for="correo">Correo</label>
            <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com"
                value="<?= isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : '' ?>" required>
        </div>
        <div>
            <label for="pass">Contraseña</label>
            <input type="password" id="pass" name="pass" placeholder="********" required>
        </div>
        <div>
            <label for="pass_confirm">Confirmar contraseña</label>
            <input type="password" id="pass_confirm" name="pass_confirm" placeholder="********" required>
        </div>
        <div>
            ¿Ya tienes una cuenta? <a href="/?page=login">Inicia sesión aquí.</a>
        </div>

        <button type="submit">Registrarme</button>

        <?php if (!empty($error)): ?>
            <div class="error" style="display:block;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
    </form>
</div>