<?php if ($esAutor || $esRevisor || ($user ? $user['id_rol'] === 3 : false)) : ?>
    <div class="articulo-acciones">
        <?php if ($esRevisor) : ?>
            <div class="modal" id="crear-form">
                <div class="modal-content">
                    <form class="modal-form crear-formulario formulario" method="POST">
                        <?php if ($sePuedeRevisar) : ?>
                            <h1>Formulario</h1>
                            <div>
                                <span class="label">Calidad:</span>
                                <div class="input-box">
                                    <?php for ($i = 1; $i <= 7; $i++): ?>
                                        <label class="reset-label input-radio-label" for="calidad_<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                            <input type="radio" id="calidad_<?php echo $i; ?>" name="calidad" value="<?php echo $i; ?>" <?php if ($i == 7) echo 'checked' ?> >
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div>
                            <span class="label">Originalidad:</span>
                                <div class="input-box">
                                <?php for ($i = 1; $i <= 7; $i++): ?>
                                        <label class="reset-label input-radio-label" for="originalidad_<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                            <input type="radio" id="originalidad_<?php echo $i; ?>" name="originalidad" value="<?php echo $i; ?>" <?php if ($i == 7) echo 'checked' ?> >
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div>
                            <span class="label">Valoración:</span>
                                <div class="input-box">
                                <?php for ($i = 1; $i <= 7; $i++): ?>
                                        <label class="reset-label input-radio-label" for="valoracion_<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                            <input type="radio" id="valoracion_<?php echo $i; ?>" name="valoracion" value="<?php echo $i; ?>" <?php if ($i == 7) echo 'checked' ?> >
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div>
                                <label for="argumentos">Argumentos de valoración:</label>
                                <textarea id="argumentos" name="argumentos" class="input" style="min-height:100px;" required></textarea>
                            </div>
                            <div>
                                <label for="comentarios">Comentarios:</label>
                                <textarea id="comentarios" name="comentarios" class="input" style="min-height:100px;"></textarea>
                            </div>
                        <?php else : ?>
                            <h1>No puedes revisar aun</h1>
                            <div>
                                <p><?= $fecha_revision_texto ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="btns-container">
                            <?php if ($sePuedeRevisar) : ?>
                                <button type="submit">Enviar</button>
                            <?php endif; ?>
                            <button class="btn-rojo" type="button" id="modalBtn" data-target="crear-form">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-overlay" data-overlay-target="crear-form"></div>
        <?php endif ?>
        <?php if ($esAutor || $esRevisor || ($user ? $user['id_rol'] === 3 : false)) : ?>
            <div class="modal" id="ver-revisiones">
                <div class="modal-content">
                    <div class="revisiones-container">
                        <h2>Revisiones</h2>
                        <div class="formularios-container">
                            <!-- aqui van los formularios -->
                        </div>
                    </div>
                    <div class="modal" id="consultar-form"></div>
                    <div class="modal-overlay" data-overlay-target="consultar-form"></div>
                    <script src=<?php getJs("articuloFormularios");?>></script>
                </div>
            </div>
            <div class="modal-overlay" data-overlay-target="ver-revisiones"></div>
        <?php endif ?>
    </div>
<?php endif ?>

<div class="nav-articulo-container">
    <div class="nav-articulo">
        <?php if ($esAutor) : ?>
            <button type="button" class="nav-articulo-btn" id="editar" onClick="window.location.href='/editar/<?=$id_articulo?>'">         
                <span class="nav-articulo-icon"><?= getAsset("/svg/editar.svg") ?></span>
            </button>
        <?php endif; ?>
        <?php if ($esRevisor || $esAutor || ($user ? $user['id_rol'] === 3 : false)) : ?>
            <button type="button" class="nav-articulo-btn" id="modalBtn" data-target="ver-revisiones">
                <span class="nav-articulo-icon"><?= getAsset("/svg/revisiones.svg") ?></span>
            </button>
        <?php endif; ?>
        <?php if ($esRevisor) : ?>
            <button type="button" class="nav-articulo-btn" id="modalBtn" data-target="crear-form" <?= $yaReviso?>>
                <span class="nav-articulo-icon"><?= getAsset("/svg/formulario.svg") ?></span>
            </button>
        <?php endif; ?>
        <button type="button" class="nav-articulo-btn" id="ir-perfil" onClick="window.location.href='/perfil'">
            <span class="nav-articulo-icon"><?= getAsset("/svg/user.svg") ?></span>
        </button>
    </div>
</div>

<script src=<?php getJs("modal");?>></script>