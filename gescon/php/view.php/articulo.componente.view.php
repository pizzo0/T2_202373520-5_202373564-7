<?php if ($esAutor || $esRevisor || ($user ? $user['id_rol'] === 3 : false)) : ?>
    <div class="articulo-acciones">
        <?php if ($esRevisor) : ?>
            <div class="modal" id="crear-form">
                <div class="modal-top">
                    <h1>Formulario</h1>
                    <button class="btn-w-icon" id="modalClose" data-close-target="crear-form">
                        <span class="btn-icon">
                            <?= getAsset('/svg/cerrar.svg') ?>
                        </span>
                    </button>
                </div>
                <div class="modal-content">
                    <form class="modal-form crear-formulario formulario" method="POST">
                        <?php if ($sePuedeRevisar) : ?>
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
                <div class="modal-top">
                    <h1>Revisiones</h1>
                    <button class="btn-w-icon" id="modalClose" data-close-target="ver-revisiones">
                        <span class="btn-icon">
                            <?= getAsset('/svg/cerrar.svg') ?>
                        </span>
                    </button>
                </div>
                <div class="modal-content">
                    <div class="revisiones-container">
                        <div class="formularios-container">
                            <!-- aqui van los formularios -->
                        </div>
                    </div>
                    <script src=<?php getJs("articuloFormularios");?>></script>
                </div>
            </div>
            <div class="modal-overlay" data-overlay-target="ver-revisiones"></div>

            <div class="modal" id="consultar-form">
                <div class="modal-top">
                    <h1>Revision</h1>
                    <button class="btn-w-icon" id="modalClose" data-close-target="consultar-form">
                        <span class="btn-icon">
                            <?= getAsset('/svg/cerrar.svg') ?>
                        </span>
                    </button>
                </div>
                <div class="modal-content"></div>
            </div>
            <div class="modal-overlay" data-overlay-target="consultar-form"></div>

            
            <div class="modal" id="editar-form">
                <div class="modal-top">
                    <h1>Editar formulario</h1>
                </div>
                <div class="modal-content">
                    <form class="modal-form editar-formulario formulario" method="POST">
                        <div>
                            <input type="hidden" id="hidden-id_formulario" name="id_formulario">
                            <input type="hidden" id="hidden-rut_revisor" name="rut_revisor">
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
                        <div class="btns-container">
                            <button type="submit">Guardar cambios</button>
                            <button class="btn-rojo" type="button" id="modalBtn" data-target="editar-form">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-overlay" data-overlay-target="editar-form"></div>
        <?php endif ?>
    </div>
<?php endif ?>

<div class="nav-flotante-container">
    <div class="nav-flotante">
        <?php if ($esAutor && $sePuedeEditar) : ?>
            <button type="button" class="nav-flotante-btn" id="editar" onClick="window.location.href='/editar/<?=$id_articulo?>'">         
                <div class="nav-flotante-btn-content">
                    <span class="nav-flotante-icon"><?= getAsset("/svg/editar.svg") ?></span>
                    <span class="nav-option-name">Editar</span>
                </div>
            </button>
        <?php endif; ?>
        <?php if ($esRevisor || $esAutor || ($user ? $user['id_rol'] === 3 : false)) : ?>
            <button type="button" class="nav-flotante-btn" id="modalBtn" data-target="ver-revisiones">
                <div class="nav-flotante-btn-content">
                    <span class="nav-flotante-icon"><?= getAsset("/svg/revisiones.svg") ?></span>
                    <span class="nav-option-name">Revisiones</span>
                </div>
            </button>
        <?php endif; ?>
        <?php if ($esRevisor) : ?>
            <button type="button" class="nav-flotante-btn" id="modalBtn" data-target="crear-form" <?= $yaReviso?>>
                <div class="nav-flotante-btn-content">
                    <span class="nav-flotante-icon"><?= getAsset("/svg/formulario.svg") ?></span>
                    <span class="nav-option-name">Hacer revision</span>
                </div>
            </button>
        <?php endif; ?>
    </div>
</div>

<script src=<?php getJs("navFlotante");?>></script>
<script src=<?php getJs("modal");?>></script>