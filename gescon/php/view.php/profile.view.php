<?php
$user = getUsuarioData();
$nombre = explode(" ",$user['nombre'])[0];

?>
<div class="menu-perfil">
    <div class="menu-perfil-forms">
        <form method="post" class="formulario nombre-form form-activo">
            <label for="nombre">Cambiar nombre</label>
            <div class="form-div">
                <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($user["nombre"] ?? "") ?>" required>
                <button>Guardar</button>
            </div>
        </form>
        <form method="post" class="formulario email-form form-activo">
            <label for="correo">Cambiar email</label>
            <div class="form-div">
                <input type="text" name="correo" id="correo" value="<?= htmlspecialchars($user["email"] ?? "") ?>" required>
                <button>Guardar</button>
            </div>
        </form>
        <form method="post" class="formulario pass-form">
            <label for="pass">Cambiar contraseña</label>
            <div class="form-div">
                <label for="old_pass">Contraseña anterior</label>
                <input type="password" name="old_pass" id="old_pass" required>
                <label for="pass">Contraseña</label>
                <input type="password" name="pass" id="pass" required>
                <label for="pass_confirm">Confirmar contraseña</label>
                <input type="password" name="pass_confirm" id="pass_confirm" required>
                <button>Guardar</button>
            </div>
        </form>
        <button id="toggle-editar-perfil">Cambiar contraseña</button>
        <button id="editar-perfil-btn">Dejar de editar perfil</button>
    </div>
</div>
<div class="menu-overlay"></div>
<div class="profile-container">
    <div class="profile-usuario-container">
        <h1><?php echo $nombre; ?></h1>
        <p id="num-articulos"></p>
        <button id="editar-perfil-btn">Editar perfil</button>
        <button onclick="window.location.href='/logout'" >Cerrar sesion</button>
    </div>
    <div class="profile-articulos-container">
        <!-- articulos del usuario -->
    </div>
</div>

<script src=<?php getJs("editarPerfil");?>></script>
<script src=<?php getJs("usuarioArticulos");?>></script>