<?php
$svg_alerta = getAsset("/svg/alerta.svg");
$svg_ok = getAsset("/svg/ok.svg");
$svg_error = getAsset("/svg/error.svg");
$svg_nav = getAsset("/svg/nav.svg");
$svg_cerrar = getAsset("/svg/cerrar.svg");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php getTitulo(); ?> - <?php getNombre(); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <?php getStyles(); ?>
</head>
<body>
    <header class="no-overflow header">
        <a href="/" class="gescon"><?php getNombre(); ?></a>
        <nav id="nav" class="no-overflow nav">
            <a href="/" class="gescon"><?php getNombre(); ?></a>
            <?php getNav(""); ?>
            <button id="mostrar-nav"><?= $svg_cerrar ?></button>
        </nav>
        <span id="mostrar-nav" class="open-nav"><?= $svg_nav ?></span>
    </header>

    <main>
        <?php getContenido(); ?>
    </main>
    <?php if (isset($_SESSION["notificacion"])): ?>
        <div class="notificacion noti-<?= $_SESSION["notificacion"]["tipo"] ?>">
            <?php if ($_SESSION["notificacion"]["tipo"] == "alerta") : ?>
                <span><?= $svg_alerta ?></span>
            <?php elseif ($_SESSION["notificacion"]["tipo"] == "ok") : ?>
                <span><?= $svg_ok ?></span>
            <?php elseif ($_SESSION["notificacion"]["tipo"] == "error") : ?>
                <span><?= $svg_error ?></span>
            <?php endif; ?>
            <span><?= htmlspecialchars($_SESSION["notificacion"]["mensaje"]) ?></span>
            <button class="noti-cerrar">X</button>
        </div>
        <?php unset($_SESSION["notificacion"]); ?>
    <?php endif; ?>
    <script src=<?php getJs("inputNoRellenado");?>></script>
    <script src=<?php getJs("notificacion");?>></script>
    <script src=<?php getJs("mostrarNav");?>></script>
    <script>
        console.log(`<?php echo getPagina() ;?>`)
    </script>
</body>
</html>