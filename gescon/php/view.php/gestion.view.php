<?php


$user = getUsuarioData();
$tab = $_GET['sub'] ?? 'revisores';
?>
<?php if ($user['id_rol'] < 3) : ?>
    <?php include '404.view.php'; ?>
<?php else : ?>
    <div class="nav-tabs">
        <a class="btn tab-btn <?= $tab == 'revisores' ? 'tab-btn-activo' : '' ?>" href="/gestion">Revisores</a>
        <a class="btn tab-btn <?= $tab == 'topicos' ? 'tab-btn-activo' : '' ?>" href="/gestion/topicos">Topicos</a>
        <a class="btn tab-btn <?= $tab == 'asignacion' ? 'tab-btn-activo' : '' ?>" href="/gestion/asignacion">Asignación</a>
    </div>
    <div class="tabs-container">
        <?php include "gestion.$tab.view.php"; ?>
    </div>

    <script src=<?php getJs("modal");?>></script>
<?php endif ?>