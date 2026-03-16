<?php
require_once __DIR__ . '/../controllers/cubi.php';
$idubi = isset($_GET['idubi']) ? intval($_GET['idubi']) : (isset($_POST['idubi']) ? intval($_POST['idubi']) : 0);
$ope = isset($_POST['ope']) ? $_POST['ope'] : null;
$msg = '';

if ($ope === 'save' && $idubi) {
    // Guardar edición
    $_POST['idubi'] = $idubi;
    $_POST['ope'] = 'save';
    ob_start();
    require __DIR__ . '/../controllers/cubi.php';
    ob_end_clean();
    $msg = '<div class="alert alert-success">Ubicación actualizada correctamente.</div>';
}

if ($idubi) {
    $mubi->setIdubi($idubi);
    $datOne = $mubi->getOne();
    $datOne = isset($datOne[0]) ? $datOne[0] : null;
}

// Empresas y responsables ya están disponibles si se incluye cubi.php
?>
<?php if($msg) echo $msg; ?>
<h5>Editar Ubicación</h5>
<form id="formEditUbi" method="POST">
    <input type="hidden" name="ope" value="save">
    <input type="hidden" name="idubi" value="<?= $idubi; ?>">
    <div class="row">
        <div class="form-group col-md-4">
            <label>Nombre</label>
            <input type="text" name="nomubi" class="form-control" required value="<?= htmlspecialchars($datOne['nomubi'] ?? '') ?>">
        </div>
        <div class="form-group col-md-2">
            <label>Código</label>
            <input type="text" name="codubi" class="form-control" required value="<?= htmlspecialchars($datOne['codubi'] ?? '') ?>">
        </div>
        <div class="form-group col-md-3">
            <label>Dirección</label>
            <input type="text" name="dirubi" class="form-control" value="<?= htmlspecialchars($datOne['dirubi'] ?? '') ?>">
        </div>
        <div class="form-group col-md-2">
            <label>Departamento</label>
            <input type="text" name="depubi" class="form-control" value="<?= htmlspecialchars($datOne['depubi'] ?? '') ?>">
        </div>
        <div class="form-group col-md-2">
            <label>Ciudad</label>
            <input type="text" name="ciuubi" class="form-control" value="<?= htmlspecialchars($datOne['ciuubi'] ?? '') ?>">
        </div>
        <div class="form-group col-md-3">
            <label>Empresa</label>
            <select name="idemp" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach($empresas as $emp): ?>
                    <option value="<?= $emp['idemp'] ?>" <?= (isset($datOne['idemp']) && $datOne['idemp']==$emp['idemp']) ? 'selected' : '' ?>>
                        <?= $emp['nomemp'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label>Responsable</label>
            <select name="idresp" class="form-select">
                <option value="">Seleccione...</option>
                <?php foreach($responsables as $resp): ?>
                    <option value="<?= $resp['idusu'] ?>" <?= (isset($datOne['idresp']) && $datOne['idresp']==$resp['idusu']) ? 'selected' : '' ?>>
                        <?= $resp['nomusu'] . ' ' . $resp['apeusu'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-2">
            <label>Activo</label>
            <select name="act" class="form-select">
                <option value="1" <?= (isset($datOne['act']) && $datOne['act']==1) ? 'selected' : '' ?>>Sí</option>
                <option value="0" <?= (isset($datOne['act']) && $datOne['act']==0) ? 'selected' : '' ?>>No</option>
            </select>
        </div>
        <div class="form-group col-md-2 align-self-end">
            <button type="submit" class="btn btn-success w-100">Guardar</button>
        </div>
    </div>
</form>
