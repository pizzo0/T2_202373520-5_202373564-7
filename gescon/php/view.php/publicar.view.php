<?php
$user = getUsuarioData();
$topicos = getTopicos();
?>
<div class="menu_publicar">
    <div>
        <h1>Crear Articulo</h1>
        <p>Aqui puedes publicar un articulo que te pertenezca o que seas parte de el.</p>
    </div>
    <form method="post" class="form_publicar formulario">
        <div class="titulo_publicar">
            <h1>Titulo</h1>
            <input type="text" id="titulo" name="titulo" maxlength="255" required>
        </div>
        <div class="resumen_publicar">
            <h1>Resumen</h1>
            <textarea id="resumen" name="resumen" class="input" maxlength="150" required></textarea>
        </div>
        <div class="autores_publicar">
            <div>
                <h1>Autores</h1>
                <p>Ingresa los autores del articulo. Uno de los autores debes ser tu, por lo que no puedes eliminarte.
                <br> 
                Debes seleccionar a un autor de contacto, este sera al que contactemos para enviar información importante y futuras actualizaciones sobre el articulo.
                </p>
            </div>
            <table id="tabla-autores">
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td><input type="text" name="nombre[]" value="<?= htmlspecialchars($user['nombre']) ?>" readonly></td>
                    <td><input type="email" name="email[]" value="<?= htmlspecialchars($user['email']) ?>" readonly></td>
                    <td><input type="radio" name="contacto" value="0" checked></td>
                    <td></td>
                </tr>
            </table>
            <div id="agregar-autor-form">
                <button type="button" onclick="buscarYAgregarAutor()">+ Agregar Autor</button>
                <input type="email" id="nuevo-email" placeholder="correo@ejemplo.com">
            </div>
        </div>
        <div class="topicos_publicar">
            <div class="dropdown" id="dropdown-container">
                <button type="button" class="dropdown-button" id="dropdown-button">+ Agregar Tópicos</button>
                <div class="dropdown-menu" id="dropdown-menu">
                    <!-- opciones -->
                </div>
            </div>
            <div id="topicos-container">
                <!-- topicos -->
            </div>
            <input type="hidden" name="topicos" id="hidden-topics"> 
        </div>
        <div class="btn_publicar">
            <button type="submit">Publicar</button>
        </div>
    </form>
</div>

<script src=<?php getJs("inputTopicos");?>></script>
<script src=<?php getJs("inputAutores");?>></script>