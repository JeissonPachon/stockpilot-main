<?php
// Evitar que ckard.php procese la operación, ya que lo haremos aquí para AJAX
$ope_temp = isset($_POST['ope']) ? $_POST['ope'] : null;
unset($_POST['ope']);

require_once __DIR__ . '/../controllers/ckard.php';
require_once __DIR__ . '/../models/mubi.php';

// Restaurar ope
if($ope_temp) $_POST['ope'] = $ope_temp;

$mubi = new Mubi();
$ubicaciones = $mubi->getAll($_SESSION['idemp']);

$idkar = isset($_GET['idkar']) ? intval($_GET['idkar']) : (isset($_POST['idkar']) ? intval($_POST['idkar']) : 0);
$ope = isset($_POST['ope']) ? $_POST['ope'] : null;
$msg = '';

if ($ope === 'addmov' && $idkar) {
    // Guardar movimiento
    $data = [
        ':idkar'   => $idkar,
        ':idprod'  => intval($_POST['idprod']),
        ':idubi'   => intval($_POST['idubi']),
        ':tipmov'  => intval($_POST['tipmov']),
        ':cantmov' => floatval($_POST['cantmov']),
        ':valmov'  => floatval($_POST['valmov']),
        ':docref'  => $_POST['docref'],
        ':obs'     => $_POST['obs'],
        ':idusu'   => $_SESSION['idusu']
    ];
    
    $idmov = $mkard->saveMovimiento($data);
    if($idmov){
        // Registrar en auditoría
        require_once __DIR__ . '/../models/maud.php';
        $maud = new MAud();
        $maud->setIdemp($_SESSION['idemp']);
        $maud->setIdusu($_SESSION['idusu']);
        $maud->setTabla('movim');
        $maud->setAccion(1); // 1=INSERT
        $maud->setIdreg($idmov); // ID del movimiento
        $maud->setDatos_ant(null);
        $maud->setDatos_nue(json_encode($data));
        $maud->setFecha(date('Y-m-d H:i:s'));
        $maud->setIp($_SERVER['REMOTE_ADDR']);
        $maud->save();

        $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-check-circle"></i> Movimiento guardado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    } else {
        $msg = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-exclamation-circle"></i> Error al guardar el movimiento. Verifique los datos.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }
}

$movs = $mkard->getMovimientos($idkar);
$productos = $mkard->getProductos();
?>

<div class="container-fluid">
    <?php if($msg) echo $msg; ?>
    
    <div class="row">
        <!-- Columna Izquierda: Formulario -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fa-solid fa-plus-circle"></i> Agregar Movimiento</h6>
                </div>
                <div class="card-body">
                    <form id="formAddMov" method="POST">
                        <input type="hidden" name="ope" value="addmov">
                        <input type="hidden" name="idkar" value="<?= $idkar; ?>">
                        
                        <div class="mb-3">
                            <label for="idprod" class="form-label"><i class="fa-solid fa-box"></i> Producto <span class="text-danger">*</span></label>
                            <select name="idprod" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($productos as $prod) { ?>
                                    <option value="<?= $prod['idprod']; ?>"><?= $prod['nomprod']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="idubi" class="form-label"><i class="fa-solid fa-map-marker-alt"></i> Ubicación <span class="text-danger">*</span></label>
                            <select name="idubi" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($ubicaciones as $ubi) { ?>
                                    <option value="<?= $ubi['idubi']; ?>"><?= $ubi['nomubi']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label for="tipmov" class="form-label"><i class="fa-solid fa-exchange-alt"></i> Tipo</label>
                                <select name="tipmov" class="form-select">
                                    <option value="1">Entrada</option>
                                    <option value="2">Salida</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cantmov" class="form-label"><i class="fa-solid fa-hashtag"></i> Cantidad <span class="text-danger">*</span></label>
                                <input type="number" name="cantmov" class="form-control" required min="1">
                            </div>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label for="valmov" class="form-label"><i class="fa-solid fa-dollar-sign"></i> Valor <span class="text-danger">*</span></label>
                                <input type="number" name="valmov" class="form-control" required step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="docref" class="form-label"><i class="fa-solid fa-file-alt"></i> Doc. Ref.</label>
                                <input type="text" name="docref" class="form-control" placeholder="Ej. FAC-123">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="obs" class="form-label"><i class="fa-solid fa-comment"></i> Observaciones</label>
                            <textarea name="obs" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-save"></i> Guardar Movimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Columna Derecha: Tabla -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-secondary"><i class="fa-solid fa-list-ol"></i> Historial de Movimientos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cant.</th>
                                    <th>Valor</th>
                                    <th>Doc.</th>
                                    <th>Obs.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($movs): ?>
                                    <?php foreach ($movs as $mov) { ?>
                                    <tr>
                                        <td><small><?= date('d/m/Y H:i', strtotime($mov['fecha'])); ?></small></td>
                                        <td class="fw-bold"><?= $mov['producto']; ?></td>
                                        <td>
                                            <?php if($mov['tipmov'] == 1): ?>
                                                <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-arrow-down"></i> Ent</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-arrow-up"></i> Sal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center fw-bold"><?= $mov['cantmov']; ?></td>
                                        <td class="text-end">$<?= number_format($mov['valmov'], 2, ',', '.'); ?></td>
                                        <td><small><?= $mov['docref']; ?></small></td>
                                        <td><small class="text-muted"><?= $mov['obs']; ?></small></td>
                                    </tr>
                                    <?php } ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-box-open fa-3x mb-3"></i><br>
                                            No hay movimientos registrados en este periodo.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
