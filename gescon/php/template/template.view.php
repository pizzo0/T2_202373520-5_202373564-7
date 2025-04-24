<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php getTitulo(); ?> - <?php getNombre(); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href=<?php getStyle("styles"); ?>>
    <link rel="stylesheet" href=<?php getStyle("publicar"); ?>>
    <link rel="stylesheet" href=<?php getStyle("barra"); ?>>
    <link rel="stylesheet" href=<?php getStyle("notificacion"); ?>>
    <link rel="stylesheet" href=<?php getStyle("buscar"); ?>>
    <link rel="stylesheet" href=<?php getStyle("profile"); ?>>
</head>
<body>
    <header class="no-overflow">
        <nav class="no-overflow">
            <span><?php getNombre(); ?></span>
            <?php getNav(""); ?>
        </nav>
    </header>

    <main>
        <?php getContenido(); ?>
    </main>
    <?php if (isset($_SESSION["notificacion"])): ?>
        <div class="notificacion noti-<?= $_SESSION["notificacion"]["tipo"] ?>">
            <span><?= htmlspecialchars($_SESSION["notificacion"]["mensaje"]) ?></span>
            <button class="noti-cerrar">X</button>
        </div>
        <?php unset($_SESSION["notificacion"]); ?>
    <?php endif; ?>
    <script src=<?php getJs("inputNoRellenado");?>></script>
    <script src=<?php getJs("notificacion");?>></script>
</body>
</html>