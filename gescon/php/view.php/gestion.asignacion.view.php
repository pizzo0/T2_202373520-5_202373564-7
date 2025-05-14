<?php
$titulo = "Asignación de articulos";
$filtro_extra = true;
include "buscar.componente.view.php";
?>
<div class="modal-overlay" data-overlay-target="asignar-articulo"> </div>
<div class="modal" id="asignar-articulo"></div>

<script src=<?php getJs("asignarRevisores");?>></script>