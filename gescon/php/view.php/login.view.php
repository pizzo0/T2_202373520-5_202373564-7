<?php
global $error;

?>

<div class="menu">
    <h1>Acceso</h1>
    <form method="post" class="formulario">
        <div>
            <label for="correo">Correo</label>
            <input type="email" id="correo" name="correo"
                value="<?= htmlspecialchars($_POST["correo"] ?? "") ?>"
                placeholder="correo@ejemplo.com" required>
        </div>
        <div>
            <label for="pass">Contraseña</label>
            <input type="password" id="pass" name="pass" placeholder="********" required>
        </div>
        <div>
            ¿No tienes una cuenta? <a href="/?page=signup">Regístrate aquí.</a>
        </div>

        <div>
            <button>Acceder</button>
        </div>
        <div>
            
            <?php if (!empty($error)): ?>
                <p class="error" style="display:block;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php
            if (isset($_SESSION['mensaje_login'])) {
                echo "<p class='error' style='display:block;'>" . $_SESSION['mensaje_login'] . "</p>";
                unset($_SESSION['mensaje_login']);
            }
            ?>
        </div>
    </form>
</div>
