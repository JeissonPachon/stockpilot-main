<?php require_once("controllers/cprov.php"); ?>



<form action="home.php?pg=<?=$pg;?>" method="POST">
<div class="row">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="fa-solid fa-truck-field"></i>Gestion Proveedores</h2>
    </div>
    <div class="form-group col-md-6">
        <label for="tipoprov">Tipo Proveedor</label>
        <input type="text" name="tipoprov" id="tipoprov" class="form-control" value="<?php if($datOne && $datOne[0]['tipoprov']) echo $datOne[0]['tipoprov']; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="nomprov">Nombre Proveedor</label>
        <input type="text" name="nomprov" id="nomprov" class="form-control" value="<?php if($datOne && $datOne[0]['nomprov']) echo $datOne[0]['nomprov']; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="docprov">Documento</label>
        <input type="text" name="docprov" id="docprov" class="form-control" value="<?php if($datOne && $datOne[0]['docprov']) echo $datOne[0]['docprov']; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="telprov">Teléfono</label>
        <input type="text" name="telprov" id="telprov" class="form-control" value="<?php if($datOne && $datOne[0]['telprov']) echo $datOne[0]['telprov']; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="emaprov">Email</label>
        <input type="email" name="emaprov" id="emaprov" class="form-control" value="<?php if($datOne && $datOne[0]['emaprov']) echo $datOne[0]['emaprov']; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="dirprov">Dirección</label>
        <input type="text" name="dirprov" id="dirprov" class="form-control" value="<?php if($datOne && $datOne[0]['dirprov']) echo $datOne[0]['dirprov']; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="idubi">Ubicación</label>
        <select name="idubi" id="idubi" class="form-control">
            <?php foreach($datUbi as $ubi) { ?>
                <option value="<?=$ubi['idubi'];?>" <?php if($datOne && $datOne[0]['idubi'] == $ubi['idubi']) echo "selected"; ?>>
                    <?=$ubi['nomubi'];?> - <?=$ubi['ciuubi'];?>, <?=$ubi['depubi'];?>
                </option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="idemp">Empresa</label>
        <select name="idemp" id="idemp" class="form-control">
            <?php foreach($datEmp as $emp) { ?>
                <option value="<?=$emp['idemp'];?>" <?php if($datOne && $datOne[0]['idemp'] == $emp['idemp']) echo "selected"; ?>>
                    <?=$emp['nomemp'];?> - <?=$emp['diremp'];?> - <?=$emp['emaemp'];?>
                </option>
            <?php } ?>
        </select>
    </div>

    <input type="hidden" name="idprov" value="<?php if($datOne && $datOne[0]['idprov']) echo $datOne[0]['idprov']; ?>">
    <input type="hidden" name="ope" value="save">

    <div class="form-group col-md-12 mt-3">
        <input type="submit" class="btn btn-primary" value="Guardar">
    </div>
</div>
</form>

<hr><br>
<div class="table-responsive">
<table id="example" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Documento</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Ubicación</th>
            <th>Empresa</th>
            <th></th>
        </tr>   
    </thead>
    <tbody>
        <?php if($datAll){ foreach($datAll as $dt){ ?>
        <tr>
            <td><?=$dt['idprov'];?></td>
            <td><?=$dt['nomprov'];?></td>
            <td><?=$dt['tipoprov'];?></td>
            <td><?=$dt['docprov'];?></td>
            <td><?=$dt['telprov'];?></td>
            <td><?=$dt['emaprov'];?></td>
            <td><?=$dt['nomubi'];?> - <?=$dt['ciuubi'];?>, <?=$dt['depubi'];?></td>
            <td><?=$dt['nomemp'];?> - <?=$dt['diremp'];?></td>
            <td style="text-align: right;">
                <a href="home.php?pg=<?= $pg; ?>&idprov=<?= $dt['idprov']; ?>&ope=edi" 
                                   class="btn btn-sm btn-outline-warning me-2" title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                <a href="javascript:void(0);"
                                   onclick="confirmarEliminacion('home.php?pg=<?= $pg; ?>&idprov=<?= $dt['idprov']; ?>&ope=eli')"
                                  class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
            </td>
        </tr>
        <?php }} ?>
    </tbody>
</table>
<div>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg === 'saved') {
        Swal.fire({
            icon: 'success',
            title: '¡Guardado exitosamente!',
            text: 'El nuevo Dominio se ha registrado correctamente.',
            confirmButtonColor: '#198754',
            confirmButtonText: 'Aceptar'
        });
    }

    if (msg === 'updated') {
        Swal.fire({
            icon: 'info',
            title: '¡Actualización exitosa!',
            text: 'Los datos se han actualizado correctamente.',
            confirmButtonColor: '#0d6efd',
            confirmButtonText: 'Aceptar'
        });
    }

    if (msg === 'deleted') {
        Swal.fire({
            icon: 'warning',
            title: '¡Eliminación exitosa!',
            text: 'El Dominio ha sido eliminado correctamente.',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Aceptar'
        });
    }
});

// Confirmación antes de eliminar
function confirmarEliminacion(url) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
// ============== CÓDIGO DATATABLES CORREGIDO (Incluyendo destrucción) =================
$(document).ready(function() {
    // Verificar si ya existe una instancia de DataTables en #example y destruirla
    if ($.fn.DataTable.isDataTable('#example')) {
        $('#example').DataTable().destroy();
    }

    // Inicializar DataTables con tus opciones de diseño y traducción
    $('#example').DataTable({
        // Ya no necesitamos "retrieve: true" porque la destruimos antes.
        
        // 1. Traducción al español
        "language": {
            "decimal":        "",
            "emptyTable":     "No hay datos disponibles en la tabla",
            "info":           "Mostrando _START_ a _END_ de _TOTAL_ proveedores",
            "infoEmpty":      "Mostrando 0 a 0 de 0 proveedores",
            "infoFiltered":   "(filtrado de _MAX_ proveedores totales)",
            "infoPostFix":    "",
            "thousands":      ".",
            "lengthMenu":     "Mostrar _MENU_ proveedores",
            "loadingRecords": "Cargando...",
            "processing":     "Procesando...",
            "search":         "Buscar:",
            "zeroRecords":    "No se encontraron proveedores coincidentes",
            "paginate": {
                "first":      "Primero",
                "last":       "Último",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
            "aria": {
                "sortAscending":  ": activar para ordenar la columna de forma ascendente",
                "sortDescending": ": activar para ordenar la columna de forma descendente"
            }
        },
        // 2. Aplicar clases de Bootstrap al diseño de DataTables
        "dom": '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "pagingType": "full_numbers",
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Todos"] ],
        
        // 3. Clases de Bootstrap
        "oClasses": {
            "sFilterInput": "form-control form-control-sm",
            "sLengthSelect": "form-select form-select-sm"
        },
    });
    
    // 4. Mejorar el estilo del campo de búsqueda
    $('div.dataTables_filter input').attr('placeholder', 'Buscar proveedor...');
    $('div.dataTables_filter label').contents().filter(function(){
        return this.nodeType === 3; // Elimina el texto "Search:"
    }).remove();
});
// ===================================================================================
</script>
