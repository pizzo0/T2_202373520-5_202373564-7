<?php
$user = getUsuarioData();
$nombre = explode(" ",$user['nombre'])[0];


$svg_añadir = getAsset("/svg/añadir.svg");
?>
<div class="menu-perfil big-border-radius">
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
                <button type="submit">Guardar</button>
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
                <button type="submit">Guardar</button>
            </div>
        </form>
        <button id="toggle-editar-perfil">Cambiar contraseña</button>
        <button id="editar-perfil-btn">Dejar de modificar perfil</button>
    </div>
</div>
<div class="menu-overlay"></div>
<div class="profile-container">
    <div class="profile-usuario-container">
        <div class="profile-ident">
            <h2><?php echo $nombre; ?></h2>
            <span class="etiqueta2 rol"><?php echo getRolNombre($user['id_rol']); ?></span>
        </div>
        <p>Para editar un articulo, presiona sobre el.</p>
        <p id="num-articulos"></p>
        <button id="editar-perfil-btn">Modificar perfil</button>
        <button onclick="window.location.href='/logout'" >Cerrar sesion</button>
    </div>
    <div class="profile-articulos-container">
        <button onclick="window.location.href='/publicar'" style="width:100%;"><span><?= $svg_añadir ?></span> Crear un nuevo articulo</button>
        <!-- articulos del usuario -->
    </div>
</div>

<script src=<?php getJs("editarPerfil");?>></script>
<script src=<?php getJs("usuarioArticulos");?>></script>