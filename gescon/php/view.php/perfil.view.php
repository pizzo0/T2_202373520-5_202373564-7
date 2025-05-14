<?php
$user = getUsuarioData();
$nombre = explode(" ",$user['nombre'])[0];


$svg_añadir = getAsset("/svg/añadir.svg");

?>
<?php include "perfil.opciones.view.php"; ?>

<div class="profile-container">
    <div class="profile-usuario-container">
        <div class="profile-ident">
            <h2><?php echo $nombre; ?></h2>
            <span class="etiqueta2 rol-<?= $user['id_rol'] ?>"><?php echo getRolNombre($user['id_rol']); ?></span>
        </div>
        <button id="editar-perfil-btn">Opciones</button>
    </div>
    
    <div class="profile-content-container">
        <div class="nav-tabs" id="navTabs">
            <button class="tab-btn tab-btn-activo" id="tabBtn" data-target="tabArticulos">Articulos</button>
            <?php if ($user['id_rol'] >= 2) : ?>
                <button class="tab-btn" id="tabBtn" data-target="tabRevisiones">Revisar</button>
            <?php endif ?>
        </div>
        <div class="tabs-container" id="tabContent">
            <?php
                include "perfil.autor.view.php";
                if ($user['id_rol'] >= 2) {
                    include "perfil.revisor.view.php";
                }
            ?>
        </div>
    </div>
</div>

<script src=<?php getJs("modal");?>></script>
<script src=<?php getJs("editarPerfil");?>></script>
<script src=<?php getJs("tabs");?>></script>