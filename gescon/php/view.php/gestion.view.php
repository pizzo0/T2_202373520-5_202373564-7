<?php


$user = getUsuarioData();

$svg_filtro = getAsset("/svg/filtro.svg");
$svg_ordenar = getAsset("/svg/ordenar.svg");
?>
<?php if ($user['id_rol'] < 3) : ?>
    <?php include '404.view.php'; ?>
<?php else : ?>
    <div class="nav-tabs" id="navTabs">
        <button class="tab-btn tab-btn-activo" id="tabBtn" data-target="tabRevisores">Revisores</button>
        <button class="tab-btn" id="tabBtn" data-target="tabTopicos">Topicos</button>
        <button class="tab-btn" id="tabBtn" data-target="tabAsig">Asignacion</button>
    </div>
    <div class="tabs-container" id="tabContent">
        <div class="tab" id="tabRevisores">
            <button id="toggle_crear_revisor">+ Crear revisor</button>
            <div class="revisores-container"> <!-- revisores --> </div>
        </div>
        <div class="tab" id="tabTopicos">
            <button id="modalBtn" data-target="crear-topico-container">+ Crear topico/especialidad</button>
            <div class="topicos-container"> <!-- topicos --> </div>
        </div>
        <div class="tab" id="tabAsig">
            <div class="buscar-container">
                <div class="buscar-filtros-container">
                    <div class="main-buscar-filtros-container">
                        <button id="btn-filtrar" onclick="toggleFC()"><span><?= $svg_filtro ?></span> Filtrar resultados</button>
                        <div class="ordenar-container" id="select">
                            <label for="ordenar_por">Ordenar por:</label>
                            <select class="select-input" style="width:200px;" name="ordenar_por" id="ordenar_por">
                                <option value="fecha_envio_desc">Fecha de publicación (reciente primero)</option>
                                <option value="fecha_envio_asc">Fecha de publicación (antiguo primero)</option>
                                <option value="autor_asc">Contacto [Autor] (A-Z)</option>
                                <option value="autor_desc">Contacto [Autor] (Z-A)</option>
                                <option value="titulo_asc">Título (A-Z)</option>
                                <option value="titulo_desc">Título (Z-A)</option>
                            </select>
                        </div>
                    </div>
                    <div class="paginas-nav">
                        <span id="pagina-info"></span>
                        <button id="btnAnterior"><</button>
                        <button id="btnSiguiente">></button>
                    </div>
                </div>
                <div id="resultados-busqueda">
                    <!-- cargan los resultados -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" data-overlay-target="asignar-articulo"> </div>
    <div class="modal" id="asignar-articulo"></div>
    
    <div class="modal" id="crear-topico-container">
        <div class="modal-content">
            <form class="crear-topico-form formulario" method="POST">
                <p>Crea un topico nuevo</p>
                <div>
                    <label for="topico">Topico</label>
                    <input type="text" id="topico" name="topico" required>
                </div>
                <div class="btns-container">
                    <button type="submit">Crear</button>
                    <button class="btn-rojo" type="button" id="modalBtn" data-target="crear-topico-container">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal-overlay" data-overlay-target="crear-topico-container"> </div>

    <div class="modal" id="modificar-revisor-container"></div>
    <div class="modal-overlay" data-overlay-target="modificar-revisor-container"></div>

    <div class="filtro-container">
        <form method="post" id="filtro-form">
            <span id="filtro-num-resultados"><!-- resultados --></span>
            <span>Información del articulo:</span>
            <div>
                <label for="titulo">Titulo:</label>
                <input type="text" name="titulo" id="titulo">
            </div>
            <div>
                <label for="contacto">Contacto:</label>
                <input type="text" name="contacto" id="contacto">
            </div>
            <div>
                <label for="autor">Autor:</label>
                <input type="text" name="autor" id="autor">
            </div>
            <div>
                <label for="topicos">Topico:</label>
                <select class="select-input select-input-bigger" name="topicos" id="topicos">
                    <option value="">Seleccionar topico</option>
                </select>
            </div>
            <div>
                <label for="revisor">Revisor:</label>
                <input type="text" name="revisor" id="revisor">
            </div>
            <div>
                <span>Fecha de publicación:</span>
                <br>
                <label for="fecha_desde">Desde:</label>
                <input type="date" name="fecha_desde" id="fecha_desde">
                <label for="fecha_hasta">Hasta:</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta">
            </div>
            <span>Extra:</span>
            <div class="checkbox-div">
                <label class="checkbox-label" for="necesita-revisores">
                    <input type="checkbox" class="checkbox" id="necesita-revisores" name="necesita-revisores">
                    <span class="checkbox-slider"></span>
                </label>
                <label class="reset-label" for="necesita-revisores">Necesita revisores</label>
            </div>
            <br>
            <button type="submit" onclick="toggleFC()">Filtrar</button>
        </form>
    </div>
    <div id="filtro-overlay"></div>

    <div class="menu-overlay"></div>
    <div id="crear-revisor-container" class="big-border-radius">
        <h1>Registrar un revisor</h1>
        <form method="post" id="crear-revisor-form" class="formulario">
            <div class="inputs-container">
                <div class="input-container">
                    <label for="rut">Rut</label>
                    <input type="text" id="rut" name="rut" <?php
                        if (isset($_POST['pass'])) echo "value=" . $_POST['rut'];
                    ?> required>
                </div>
                <div class="input-container">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" <?php
                        if (isset($_POST['pass'])) echo "value=" . $_POST['nombre'];
                    ?> required>
                </div>
                <div class="input-container">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" placeholder="revisor@gescon.com" <?php
                        if (isset($_POST['pass'])) echo "value=" . $_POST['correo'];
                    ?> required>
                </div>
                <div class="input-container">
                    <label for="pass">Contraseña</label>
                    <input type="password" id="pass" name="pass" placeholder="********" required>
                </div>
                <div class="input-container">
                    <label for="pass_confirm">Contraseña</label>
                    <input type="password" id="pass_confirm" name="pass_confirm" placeholder="********" required>
                </div>
            </div>
            <div class="btns-container">
                <button type="submit">Registrar</button>
                <button id="toggle_crear_revisor" class="btn-rojo">Cancelar</button>
            </div>
        </form>
    </div>

    <script src=<?php getJs("getTopicos");?>></script>
    <script src=<?php getJs("modificarRevisores");?>></script>
    <script src=<?php getJs("tabs");?>></script>
    <script src=<?php getJs("getArticulosFiltrados");?>></script>
    <script src=<?php getJs("asignarRevisores");?>></script>
    <script src=<?php getJs("modal");?>></script>
<?php endif ?>