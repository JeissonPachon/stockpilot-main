<?php 
require_once('controllers/cprod.php')
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="fa fa-box"></i> Gestión de Productos</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
            <i class="fa fa-plus"></i> Nuevo Producto
        </button>
    </div>

    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <div class="table-responsive">
            <table id="productosTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Foto</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Unidad</th>
                        <th>costouni</th>
                        <th>precioven</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Actualizado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($datAll)): ?>
                        <?php foreach ($datAll as $dt): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($dt['imgprod'])): ?>
                                        <img src="img/logos/<?= htmlspecialchars($dt['imgprod']) ?>" 
                                            alt="Imagen de producto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <i class="fa fa-image text-muted" title="Sin imagen" style="font-size: 24px;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($dt['codprod']) ?></td>
                                <td><?= htmlspecialchars($dt['nomprod']) ?></td>
                                <td><?= htmlspecialchars($dt['desprod']) ?></td>
                                <td><?= htmlspecialchars($dt['nomcat']) ?></td>
                                <td><?= htmlspecialchars($dt['unimed']) ?></td>
                                <td><?= number_format($dt['costouni'], 2, ',', '.') ?></td>
                                <td><?= number_format($dt['precioven'], 2, ',', '.') ?></td>
                                <td><?= $dt['act'] ? "Activo" : "Inactivo" ?></td>
                                <td><?= htmlspecialchars($dt['fec_crea']) ?></td>
                                <td><?= htmlspecialchars($dt['fec_actu']) ?></td>
                                <td>
                                    <a href="home.php?pg=<?= $pg; ?>&idprod=<?= $dt['idprod']; ?>&ope=edi" 
                                        class="btn btn-sm btn-outline-warning me-2" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="javascript:void(0);"
                                        onclick="confirmarEliminacion('home.php?pg=<?= $pg; ?>&idprod=<?= $dt['idprod']; ?>&ope=eli')"
                                        class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center text-muted">No hay productos registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-box"></i> Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form action="home.php?pg=<?= $pg ;?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="idprod" id="idprod" 
                        value="<?= !empty($datOne[0]['idprod']) ? $datOne[0]['idprod'] : '' ?>">
                    
                    <input type="hidden" name="ope" value="save">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Código</label>
                            <input type="text" name="codprod" id="codprod" class="form-control" 
                                value="<?= !empty($datOne[0]['codprod']) ? htmlspecialchars($datOne[0]['codprod']) : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nomprod" id="nomprod" class="form-control" 
                                value="<?= !empty($datOne[0]['nomprod']) ? htmlspecialchars($datOne[0]['nomprod']) : '' ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="desprod" id="desprod" class="form-control"><?= !empty($datOne[0]['desprod']) ? htmlspecialchars($datOne[0]['desprod']) : '' ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoría</label>
                            <select name="idcat" id="idcat" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datCat as $c): ?>
                                    <option value="<?= $c['idcat'] ?>" 
                                        <?= (!empty($datOne[0]['idcat']) && $datOne[0]['idcat'] == $c['idcat']) ? 'selected' : '' ?> >
                                        <?= htmlspecialchars($c['nomcat']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stock Mín.</label>
                            <input type="number" name="stkmin" id="stkmin" class="form-control"
                                value="<?= !empty($datOne[0]['stkmin']) ? $datOne[0]['stkmin'] : '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stock Máx.</label>
                            <input type="number" name="stkmax" id="stkmax" class="form-control"
                                value="<?= !empty($datOne[0]['stkmax']) ? $datOne[0]['stkmax'] : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unidad de Medida</label>
                            <input type="text" name="unimed" id="unimed" class="form-control"
                                value="<?= !empty($datOne[0]['unimed']) ? htmlspecialchars($datOne[0]['unimed']) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Imagen</label>
                            <input type="file" name="imgprod" id="imgprod" class="form-control">
                            <?php if (!empty($datOne[0]['imgprod'])): ?>
                                <div class="mt-2 d-flex align-items-center">
                                    <img src="img/logos/<?= htmlspecialchars($datOne[0]['imgprod']) ?>" 
                                         alt="Imagen actual del producto" 
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 10px; border: 1px solid #ddd;">
                                    <small class="text-muted">Imagen actual: **<?= htmlspecialchars($datOne[0]['imgprod']) ?>**</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Costo Unidad</label>
                            <input type="number" step="0.01" name="costouni" id="costouni" class="form-control"
                                value="<?= !empty($datOne[0]['costouni']) ? $datOne[0]['costouni'] : '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Precio Venta</label>
                            <input type="number" step="0.01" name="precioven" id="precioven" class="form-control"
                                value="<?= !empty($datOne[0]['precioven']) ? $datOne[0]['precioven'] : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select name="act" id="act" class="form-select">
                                <option value="1" <?= (!empty($datOne[0]['act']) && $datOne[0]['act'] == 1) ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= (!empty($datOne[0]['act']) && $datOne[0]['act'] == 0) ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tipo de Inventario</label>
                            <select name="tipo_inventario" id="tipo_inventario" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="1" <?= (!empty($datOne[0]['tipo_inventario']) && $datOne[0]['tipo_inventario'] == 1) ? 'selected' : '' ?>>Mercancías</option>
                                <option value="2" <?= (!empty($datOne[0]['tipo_inventario']) && $datOne[0]['tipo_inventario'] == 2) ? 'selected' : '' ?>>Materia Prima</option>
                                <option value="3" <?= (!empty($datOne[0]['tipo_inventario']) && $datOne[0]['tipo_inventario'] == 3) ? 'selected' : '' ?>>En Proceso</option>
                                <option value="4" <?= (!empty($datOne[0]['tipo_inventario']) && $datOne[0]['tipo_inventario'] == 4) ? 'selected' : '' ?>>Terminados</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Inicialización de DataTables (CORREGIDO)
$(document).ready(function() {
    $('#productosTable').DataTable({
        // 1. Traducción al español (Usamos un objeto JSON en lugar de la URL para evitar el error de carga)
        "language": {
            "decimal":        "",
            "emptyTable":     "No hay datos disponibles en la tabla",
            "info":           "Mostrando _START_ a _END_ de _TOTAL_ productos",
            "infoEmpty":      "Mostrando 0 a 0 de 0 productos",
            "infoFiltered":   "(filtrado de _MAX_ productos totales)",
            "infoPostFix":    "",
            "thousands":      ".",
            "lengthMenu":     "Mostrar _MENU_ productos",
            "loadingRecords": "Cargando...",
            "processing":     "Procesando...",
            "search":         "Buscar:",
            "zeroRecords":    "No se encontraron productos coincidentes",
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
        // 2. Aplicar clases de Bootstrap a los elementos de DataTables para un diseño moderno
        "dom": '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "pagingType": "full_numbers",
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Todos"] ],
        
        // 3. Clases de DataTables para Bootstrap 5
        "oClasses": {
            "sFilterInput": "form-control form-control-sm",
            "sLengthSelect": "form-select form-select-sm"
        },
    });
    
    // 4. Mejorar el estilo del campo de búsqueda
    $('div.dataTables_filter input').attr('placeholder', 'Buscar producto...');
    $('div.dataTables_filter label').contents().filter(function(){
        return this.nodeType === 3; // Elimina el texto "Buscar:"
    }).remove();
});

    // Lógica de SweetAlert2 (sin cambios)
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const msg = urlParams.get('msg');

        if (msg === 'saved') {
            Swal.fire({ icon: 'success', title: '¡Guardado exitosamente!', text: 'El nuevo producto se ha registrado correctamente.', confirmButtonColor: '#198754', confirmButtonText: 'Aceptar' });
        }
        if (msg === 'updated') {
            Swal.fire({ icon: 'info', title: '¡Actualización exitosa!', text: 'Los datos del producto se han actualizado correctamente.', confirmButtonColor: '#0d6efd', confirmButtonText: 'Aceptar' });
        }
        if (msg === 'deleted') {
            Swal.fire({ icon: 'warning', title: '¡Eliminación exitosa!', text: 'El producto ha sido eliminado correctamente.', confirmButtonColor: '#dc3545', confirmButtonText: 'Aceptar' });
        }
    });

    // Confirmación antes de eliminar
    function confirmarEliminacion(url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el producto y no se puede deshacer.',
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

    // Mostrar modal en edición
    document.addEventListener('DOMContentLoaded', function() {
      const params = new URLSearchParams(window.location.search);
      const ope = params.get('ope');

      if (ope === 'edi') {
        const modalEl = document.getElementById('modalProducto');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          const modal = new bootstrap.Modal(modalEl);
          const title = modalEl.querySelector('.modal-title');
          if (title) title.innerHTML = '<i class="fa fa-box"></i> Editar Producto';
          const submitBtn = modalEl.querySelector('button[type="submit"]');
          if (submitBtn) submitBtn.textContent = 'Actualizar';
          modal.show();
        }
      }
    });
</script>