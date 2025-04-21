<?php
$user = getUsuarioData();
$nombre = explode(" ",$user['nombre'])[0];

?>
<div class="menu-perfil">
    <h1>Bienvenido <?php echo $nombre; ?></h1>
    <div class="menu-perfil-forms">
        <form method="post" class="formulario">
            <label for="nombre">Cambiar nombre</label>
            <div>
                <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($user["nombre"] ?? "") ?>" required>
                <button>Confirmar</button>
            </div>
        </form>
        <form method="post" class="formulario">
            <label for="correo">Cambiar email</label>
            <div>
                <input type="text" name="correo" id="correo" value="<?= htmlspecialchars($user["email"] ?? "") ?>" required>
                <button>Confirmar</button>
            </div>
        </form>
        <form method="post" class="formulario">
            <label for="pass">Cambiar contraseña</label>
            <div>
                <label for="pass">Contraseña</label>
                <input type="text" name="pass" id="pass" required>
                <label for="pass_confirm">Confirmar contraseña</label>
                <input type="text" name="pass_confirm" id="pass_confirm" required>
                <button>Confirmar</button>
            </div>
        </form>
    </div>
    <a href="/profile?page=logout" class="btn">Cerrar sesion</a>
</div>