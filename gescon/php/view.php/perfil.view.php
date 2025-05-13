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
        <div class="btns-container">
            <button id="toggle-editar-perfil">Cambiar contraseña</button>
            <!-- <form method="POST">
                <input type="hidden" name="eliminar" value="1">
                <button clasS="btn-rojo" type="submit">Eliminar cuenta</button>
            </form> -->
            <button clasS="btn-rojo modalBtnEliminarCuenta" type="submit" id="modalBtn" data-target="eliminar-cuenta">Eliminar cuenta</button>
            <button class="btn-rojo" onclick="window.location.href='/logout'" >Cerrar sesion</button>
        </div>
        <button id="editar-perfil-btn">Salir de opciones</button>
    </div>
</div>

<div class="modal-overlay" data-overlay-target="eliminar-cuenta"></div>
<div class="modal" id="eliminar-cuenta">
    <div class="modal-content">
        <h1>Elimniar cuenta</h1>
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

<div class="menu-overlay"></div>
<div class="profile-container">
    <div class="profile-usuario-container">
        <div class="profile-ident">
            <h2><?php echo $nombre; ?></h2>
            <span class="etiqueta2 rol-<?= $user['id_rol'] ?>"><?php echo getRolNombre($user['id_rol']); ?></span>
        </div>
        <p>Para editar un articulo, presiona sobre el.</p>
        <button id="editar-perfil-btn">Opciones</button>
        <?php if ($user['id_rol'] === 3) : ?>
            <a class="btn" style="width: 100%;" href="/gestion">Gestión</a>
        <?php endif ?>
    </div>
    
    <div class="profile-content-container">
        <div class="nav-tabs" id="navTabs">
            <button class="tab-btn tab-btn-activo" id="tabBtn" data-target="tabArticulos">Articulos</button>
            <?php if ($user['id_rol'] >= 2) : ?>
                <button class="tab-btn" id="tabBtn" data-target="tabRevisiones">Revisar</button>
            <?php endif ?>
        </div>
        <div class="tabs-container overflow-fix" id="tabContent">
            <div class="tab overflow-fix" id="tabArticulos">
                <div class="profile-articulos-container overflow-fix">
                    <button onclick="window.location.href='/publicar'" style="width:100%;"><span><?= $svg_añadir ?></span> Crear un nuevo articulo</button>
                    <p id="num-articulos"></p>
                    <div class="input-container">
                        <label class="checkbox-label" for="articulos-revisados">
                            <input type="checkbox" class="checkbox" id="articulos-revisados" name="articulos-revisados">
                            <span class="checkbox-slider"></span>
                        </label>
                        <label class="reset-label" for="articulos-revisados">Mostrar solo articulos revisados</label>
                    </div>
                    <div class="profile-articulos-results">
                        <!-- articulos del usuario -->
                    </div>
                </div>
            </div>
            <?php if ($user['id_rol'] >= 2) : ?>
            <div class="tab overflow-fix" id="tabRevisiones">
                <p id="num-articulos-revisar"></p>
                <div class="profile-revisiones-container">
                    <!-- articulos que puede revisar -->
                </div>
            </div>
            <script src=<?php getJs("usuarioArticulosRevisar");?>></script>
            <?php endif ?>
        </div>
    </div>
</div>

<script src=<?php getJs("editarPerfil");?>></script>
<script src=<?php getJs("usuarioArticulos");?>></script>
<script src=<?php getJs("tabs");?>></script>
<script src=<?php getJs("modal");?>></script>