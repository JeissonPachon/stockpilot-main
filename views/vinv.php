<?php require_once('controllers/cinv.php');

$idper         = isset($_SESSION['idper']) ? $_SESSION['idper'] : 0;
$puedeCrear    = ($idper == 1 || $idper == 2);
$puedeEditar   = ($idper == 1 || $idper == 2);
$puedeEliminar = ($idper == 1 || $idper == 2);

// Calcular stats con stkmin
$totalReg    = count($datAll ?? []);
$totalUnid   = 0;
$sinStock    = 0;
$stockBajo   = 0;
$totalSinLot = 0;
foreach (($datAll ?: []) as $r) {
    $c = (float)$r['cant'];
    $totalUnid += $c;
    if ($c <= 0) $sinStock++;
    elseif (isset($r['stkmin']) && $c <= (float)$r['stkmin']) $stockBajo++;
    $k = $r['idprod'] . '_' . (int)($r['idubi'] ?? 0);
    if (empty($lotesIndexados[$k])) $totalSinLot++;
}

$meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
           'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$kardexJSON = json_encode($datKardex ?? []);
?>

<style>
/* ── Encabezado ───────────────────────────────────────────── */
.inv-topbar{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;margin-bottom:20px}
.inv-topbar h2{margin:0;font-weight:700;color:#1e3a5f}
.inv-topbar h2 small{font-size:.7em;color:#6b7280;font-weight:400}

/* ── Stat cards ──────────────────────────────────────────── */
.inv-stat{border-radius:10px;border:1px solid #e5e7eb;background:#fff;padding:14px 18px;text-align:center;transition:box-shadow .2s}
.inv-stat:hover{box-shadow:0 4px 14px rgba(0,0,0,.08)}
.inv-stat .num{font-size:2rem;font-weight:800;line-height:1}
.inv-stat .lbl{font-size:.78rem;color:#6b7280;margin-top:4px}

/* ── Periodos Kardex chips ───────────────────────────────── */
.kchip{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:.78rem;font-weight:600;border:2px solid transparent}
.kchip.abierto{background:#dcfce7;color:#166534;border-color:#86efac}
.kchip.cerrado{background:#f3f4f6;color:#6b7280;border-color:#d1d5db}

/* ── Header tabla ────────────────────────────────────────── */
.inv-card-header{background:linear-gradient(135deg,#1e3a5f,#2563eb);color:#fff;padding:12px 20px;display:flex;justify-content:space-between;align-items:center;border-radius:10px 10px 0 0}

/* ── Badge categoría ─────────────────────────────────────── */
.badge-cat{background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;border-radius:20px;padding:3px 10px;font-size:.75rem;font-weight:500}

/* ── Badges estado stock ─────────────────────────────────── */
.badge-ok {background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:20px;padding:3px 11px;font-size:.76rem;font-weight:600}
.badge-low{background:#fef9c3;color:#854d0e;border:1px solid #fde047;border-radius:20px;padding:3px 11px;font-size:.76rem;font-weight:600}
.badge-out{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:20px;padding:3px 11px;font-size:.76rem;font-weight:600}

/* ── Botones acción ──────────────────────────────────────── */
.btn-movs{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:.76rem;border-radius:20px;padding:3px 10px}
.btn-movs:hover{background:#dbeafe}
.btn-ent{background:#dcfce7;color:#166534;border:1px solid #86efac;font-size:.76rem;border-radius:20px;padding:3px 10px}
.btn-ent:hover{background:#bbf7d0}
.btn-sal{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;font-size:.76rem;border-radius:20px;padding:3px 10px}
.btn-sal:hover{background:#fecaca}

/* ── Modal ───────────────────────────────────────────────── */
.inv-modal-hd{background:linear-gradient(135deg,#1e3a5f,#2563eb)}
.kardex-aviso{font-size:.82rem;padding:7px 12px;border-radius:8px;margin-top:6px}
.kardex-aviso.cerrado{background:#fef2f2;color:#991b1b;border:1px solid #fca5a5}
.kardex-aviso.abierto{background:#f0fdf4;color:#166534;border:1px solid #86efac}

/* ── Tabla hover ─────────────────────────────────────────── */
#tableInv tbody tr[data-inv-row]:hover{background:#f0f7ff}
</style>

<div class="px-1 py-2">

  <!-- Encabezado + Botón Nuevo -->
  <div class="inv-topbar">
    <div>
      <h2><i class="fa-solid fa-boxes-stacked text-primary me-2"></i>Inventario
        <small>Control de stock • vinculado a Kardex</small>
      </h2>
    </div>
    <?php if($puedeCrear): ?>
    <button class="btn btn-primary fw-semibold shadow-sm"
            style="font-size:1.05rem;padding:10px 32px;border-radius:10px;min-width:200px"
            data-bs-toggle="modal" data-bs-target="#modalNuevo">
      <i class="fa-solid fa-plus me-2"></i>Nuevo Registro
    </button>
    <?php endif; ?>
  </div>

  <!-- Info -->
  <div class="alert alert-info py-2 mb-3">
    <i class="fa-solid fa-circle-info me-1"></i>
    El inventario se actualiza automáticamente con entradas y salidas.
    Use el chevron <i class="fa-solid fa-chevron-right fa-xs"></i> para ver los lotes.
    Los movimientos rápidos requieren un periodo de Kardex abierto.
  </div>

  <?php if(isset($_SESSION['mensaje'])): ?>
  <div class="alert alert-<?= htmlspecialchars($_SESSION['tipo_mensaje'] ?? 'info') ?> alert-dismissible fade show">
    <?= htmlspecialchars($_SESSION['mensaje']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
  <?php endif; ?>

  <!-- Stats: 4 tarjetas (como imagen 2) -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="inv-stat">
        <div class="num text-primary"><?= $totalReg ?></div>
        <div class="lbl">Registros</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="inv-stat">
        <div class="num text-success"><?= number_format($totalUnid, 0, ',', '.') ?></div>
        <div class="lbl">Unidades</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="inv-stat">
        <div class="num text-warning"><?= $stockBajo ?></div>
        <div class="lbl">Stock bajo mín.</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="inv-stat">
        <div class="num text-danger"><?= $sinStock ?></div>
        <div class="lbl">Sin stock</div>
      </div>
    </div>
  </div>

  <!-- Periodos Kardex -->
  <?php if(!empty($datKardex)): ?>
  <div class="card shadow-sm mb-4 border-0" style="border-radius:10px">
    <div class="card-body py-2 px-3">
      <div class="d-flex align-items-center gap-2 mb-2">
        <i class="fa-solid fa-calendar-days text-primary"></i>
        <span class="fw-semibold small text-uppercase text-secondary">Periodos Kardex</span>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <?php foreach($datKardex as $k): ?>
        <span class="kchip <?= $k['cerrado'] ? 'cerrado' : 'abierto' ?>">
          <i class="fa-solid fa-<?= $k['cerrado'] ? 'lock' : 'unlock' ?> fa-xs"></i>
          <?= $meses[(int)$k['mes']] ?> <?= $k['anio'] ?>
          <span class="opacity-75 fw-normal"><?= $k['cerrado'] ? '(cerrado)' : '(abierto)' ?></span>
        </span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Formulario ajuste manual -->
  <?php if($puedeEditar && isset($datOne)): ?>
  <div class="card shadow-sm mb-4 border-warning">
    <div class="card-header bg-warning text-dark d-flex align-items-center gap-2">
      <i class="fa-solid fa-triangle-exclamation"></i> Ajuste Manual de Inventario
      <small class="ms-auto">El inventario normalmente se actualiza desde las entradas y salidas.</small>
    </div>
    <div class="card-body">
      <form action="home.php?pg=<?= $pg ?>" method="POST" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Producto</label>
          <select name="idprod" class="form-select" required>
            <option value="">Seleccione un producto</option>
            <?php foreach(($datProd ?: []) as $row): ?>
            <option value="<?= $row['idprod'] ?>" <?= ($datOne[0]['idprod']==$row['idprod'])?'selected':'' ?>>
              <?= htmlspecialchars($row['nomprod']) ?> (<?= htmlspecialchars($row['nomcat']) ?>)
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Ubicación</label>
          <select name="idubi" class="form-select" required>
            <option value="">Seleccione una ubicación</option>
            <?php foreach(($datUbi ?: []) as $row): ?>
            <option value="<?= $row['idubi'] ?>" <?= ($datOne[0]['idubi']==$row['idubi'])?'selected':'' ?>>
              <?= htmlspecialchars($row['nomubi']) ?> (<?= htmlspecialchars($row['codubi']) ?>)
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Cantidad</label>
          <input type="number" name="cant" class="form-control"
                 value="<?= htmlspecialchars($datOne[0]['cant'] ?? '') ?>" required min="0" step="0.01">
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="home.php?pg=<?= $pg ?>" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-xmark"></i> Cancelar
          </a>
          <input type="hidden" name="idinv" value="<?= $datOne[0]['idinv'] ?? '' ?>">
          <input type="hidden" name="ope" value="save">
          <button type="submit" class="btn btn-warning rounded-pill px-4">
            <i class="fa-solid fa-floppy-disk"></i> Guardar ajuste
          </button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- Tabla de inventario — búsqueda manual (compatible con filas de lotes expandibles) -->
  <div class="card shadow-sm border-0" style="border-radius:10px;overflow:hidden">
    <div class="inv-card-header">
      <span class="fw-semibold"><i class="fa-solid fa-table-list me-2"></i>Listado de Inventario</span>
      <span class="badge bg-white bg-opacity-25 text-white"><?= $totalReg ?> registros</span>
    </div>
    <div class="card-body p-0">
      <!-- Barra de búsqueda + contador -->
      <div class="p-3 border-bottom bg-light d-flex flex-wrap gap-2 align-items-center">
        <div class="input-group" style="max-width:420px">
          <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
          <input type="text" id="inv_search" class="form-control border-start-0"
                 placeholder="Buscar producto, categoría, ubicación...">
        </div>
        <span id="inv_counter" class="text-muted small ms-2">Mostrando <?= $totalReg ?>/<?= $totalReg ?></span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tableInv">
          <thead class="table-light small text-uppercase text-secondary fw-semibold">
            <tr>
              <th style="width:2.5rem" class="ps-3"><!-- lotes --></th>
              <th>Producto</th>
              <th>Categoría</th>
              <th>Ubicación</th>
              <th class="text-center">Cantidad</th>
              <th class="text-center">Lotes</th>
              <th class="text-center">Estado</th>
              <?php if($idper==1): ?><th>Empresa</th><?php endif; ?>
              <th class="text-center pe-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php if($datAll): foreach($datAll as $row):
            $idubiRow = (int)($row['idubi'] ?? 0);
            $k        = $row['idprod'] . '_' . $idubiRow;
            $lotes    = $lotesIndexados[$k] ?? [];
            $cant     = (float)$row['cant'];
            $stkmin   = (float)($row['stkmin'] ?? 0);
            $nLotes   = count($lotes);
            $lotesVenc = $lotesPV = 0;
            foreach($lotes as $l){
              if($l['estado_lote']==='Vencido')    $lotesVenc++;
              if($l['estado_lote']==='Por vencer') $lotesPV++;
            }
            // Estado stock
            if($cant<=0)            { $bc='badge-out'; $lbl='Sin stock';  $ic='fa-circle-xmark'; }
            elseif($stkmin>0 && $cant<=$stkmin){ $bc='badge-low'; $lbl='Stock bajo'; $ic='fa-triangle-exclamation'; }
            else                    { $bc='badge-ok';  $lbl='Normal';     $ic='fa-circle-check'; }
            $rowId = 'lotes-'.$row['idprod'].'-'.$idubiRow;
          ?>
            <tr class="<?= $cant<=0?'table-danger':'' ?>"
                data-inv-row="1" data-rowid="<?= $rowId ?>"
                data-search="<?= htmlspecialchars(strtolower(
                  ($row['nomprod']??'').' '.($row['codprod']??'').' '.
                  ($row['nomcat']??'').' '.($row['nomubi']??'').' '.($row['nomemp']??'')
                )) ?>">

              <!-- Chevron lotes -->
              <td class="text-center ps-3">
                <?php if($nLotes>0): ?>
                <button class="btn btn-sm btn-outline-secondary py-0 px-1 toggle-lotes"
                        data-target="#<?= $rowId ?>" title="Ver lotes">
                  <i class="fa-solid fa-chevron-right fa-xs"></i>
                </button>
                <?php endif; ?>
              </td>

              <td>
                <div class="fw-semibold text-dark"><?= htmlspecialchars($row['nomprod']) ?></div>
                <div class="small text-muted font-monospace"><?= htmlspecialchars($row['codprod']??'') ?></div>
              </td>
              <td><span class="badge-cat"><?= htmlspecialchars($row['nomcat']) ?></span></td>
              <td><i class="fa-solid fa-location-dot text-muted me-1"></i><?= htmlspecialchars($row['nomubi']??'Sin ubicación') ?></td>
              <td class="text-center fw-bold fs-5 <?= $cant<=0?'text-danger':'' ?>"><?= number_format($cant,2,',','.') ?></td>
              <td class="text-center">
                <?php if($nLotes>0): ?>
                  <span class="badge bg-primary"><?= $nLotes ?></span>
                  <?php if($lotesVenc>0): ?>
                    <span class="badge bg-danger ms-1"><?= $lotesVenc ?> venc.</span>
                  <?php elseif($lotesPV>0): ?>
                    <span class="badge bg-warning text-dark ms-1"><?= $lotesPV ?> x/venc.</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-muted small">—</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <span class="<?= $bc ?>"><i class="fa-solid <?= $ic ?> me-1"></i><?= $lbl ?></span>
              </td>
              <?php if($idper==1): ?>
              <td><small class="text-muted"><?= htmlspecialchars($row['nomemp']??'') ?></small></td>
              <?php endif; ?>

              <!-- ACCIONES -->
              <td class="text-center pe-3">
                <div class="d-flex gap-1 justify-content-center flex-wrap">

                  <!-- Link a Movimientos pg=1010 -->
                  <a href="home.php?pg=1010" class="btn btn-sm btn-movs" title="Ver movimientos (Kardex)">
                    <i class="fa-solid fa-chart-line me-1"></i>Movs
                  </a>

                  <?php if($puedeEditar && !empty($row['idinv'])): ?>
                  <!-- Entrada rápida -->
                  <button class="btn btn-sm btn-ent"
                    onclick="abrirMovimiento(<?= $row['idinv'] ?>, '<?= addslashes($row['nomprod']) ?>', <?= $cant ?>, 'entrada')"
                    title="Entrada rápida">
                    <i class="fa-solid fa-arrow-down me-1"></i>Entrada
                  </button>
                  <!-- Salida rápida -->
                  <button class="btn btn-sm btn-sal"
                    onclick="abrirMovimiento(<?= $row['idinv'] ?>, '<?= addslashes($row['nomprod']) ?>', <?= $cant ?>, 'salida')"
                    title="Salida rápida">
                    <i class="fa-solid fa-arrow-up me-1"></i>Salida
                  </button>
                  <!-- Editar -->
                  <a href="home.php?pg=<?= $pg ?>&idinv=<?= $row['idinv'] ?>&ope=edi"
                     class="btn btn-sm btn-outline-warning rounded-pill" title="Ajuste manual">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <?php elseif($puedeEditar): ?>
                  <button class="btn btn-sm btn-outline-warning rounded-pill" disabled title="Sin registro">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <?php endif; ?>

                  <?php if($puedeEliminar): ?>
                    <?php if(empty($row['idinv']) || $nLotes>0): ?>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" disabled title="No se puede eliminar: hay lotes">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                    <?php else: ?>
                    <a href="javascript:void(0);"
                       onclick="confirmarEli('home.php?pg=<?= $pg ?>&idinv=<?= $row['idinv'] ?>&ope=eli')"
                       class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar">
                      <i class="fa-solid fa-trash-can"></i>
                    </a>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </td>
            </tr>

            <?php if($nLotes>0): ?>
            <!-- Fila expandible: detalle de lotes -->
            <tr id="<?= $rowId ?>" class="d-none bg-light" data-child-row="1">
              <td colspan="<?= 8+($idper==1?1:0) ?>" class="ps-5 py-0">
                <div class="py-2">
                  <small class="text-muted fw-semibold mb-1 d-block">
                    <i class="fa-solid fa-tags me-1"></i>
                    Lotes de <strong><?= htmlspecialchars($row['nomprod']) ?></strong>
                    en <?= htmlspecialchars($row['nomubi']??'') ?>
                  </small>
                  <table class="table table-sm table-bordered mb-0" style="font-size:.83rem">
                    <thead class="table-secondary">
                      <tr>
                        <th>Código lote</th>
                        <th class="text-end">Cant. ini.</th>
                        <th class="text-end">Cant. act.</th>
                        <th class="text-end">Costo unit.</th>
                        <th>F. ingreso</th>
                        <th>F. vencimiento</th>
                        <th class="text-center">Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach($lotes as $l):
                      $elb = match($l['estado_lote']){
                        'Vencido'         => '<span class="badge bg-danger">Vencido</span>',
                        'Por vencer'      => '<span class="badge bg-warning text-dark">Por vencer</span>',
                        'Sin vencimiento' => '<span class="badge bg-secondary">Sin venc.</span>',
                        default           => '<span class="badge bg-success">Vigente</span>',
                      };
                    ?>
                    <tr class="<?= $l['estado_lote']==='Vencido'?'table-danger':($l['estado_lote']==='Por vencer'?'table-warning':'') ?>">
                      <td><code><?= htmlspecialchars($l['codlot']) ?></code></td>
                      <td class="text-end"><?= number_format((float)$l['cantini'],2,',','.') ?></td>
                      <td class="text-end fw-bold"><?= number_format((float)$l['cantact'],2,',','.') ?></td>
                      <td class="text-end"><?= $l['costuni']!==null?'$'.number_format((float)$l['costuni'],2,',','.'):'—' ?></td>
                      <td><?= $l['fecing']??'—' ?></td>
                      <td><?= $l['fecven']??'<span class="text-muted">Sin vencimiento</span>' ?></td>
                      <td class="text-center"><?= $elb ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
            <?php endif; ?>

          <?php endforeach; else: ?>
          <tr>
            <td colspan="<?= 8+($idper==1?1:0) ?>" class="text-center text-muted py-5">
              <i class="fa-solid fa-box-open fa-2x mb-2 d-block opacity-25"></i>
              No hay registros de inventario
            </td>
          </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ==========================================================
     MODAL: NUEVO REGISTRO
     ========================================================== -->
<div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:460px">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header inv-modal-hd text-white border-0 py-3">
        <h5 class="modal-title fw-bold">
          <i class="fa-solid fa-<?= isset($datOne)?'pen-to-square':'plus-circle' ?> me-2"></i>
          <?= isset($datOne)?'Editar Inventario':'Nuevo Registro' ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="home.php?pg=<?= $pg ?>" method="POST">
        <div class="modal-body p-4">
          <input type="hidden" name="ope"   value="save">
          <input type="hidden" name="idinv" value="<?= isset($datOne)?($datOne[0]['idinv']??''):'' ?>">
          <div class="mb-3">
            <label class="form-label fw-semibold">Producto <span class="text-danger">*</span></label>
            <select name="idprod" class="form-select" required>
              <option value="">— Seleccione —</option>
              <?php foreach(($datProd??[]) as $p): ?>
              <option value="<?= $p['idprod'] ?>" <?= (isset($datOne)&&$datOne[0]['idprod']==$p['idprod'])?'selected':'' ?>>
                <?= htmlspecialchars($p['nomprod']) ?> (<?= htmlspecialchars($p['nomcat']) ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Ubicación <span class="text-danger">*</span></label>
            <select name="idubi" class="form-select" required>
              <option value="">— Seleccione —</option>
              <?php foreach(($datUbi??[]) as $u): ?>
              <option value="<?= $u['idubi'] ?>" <?= (isset($datOne)&&$datOne[0]['idubi']==$u['idubi'])?'selected':'' ?>>
                <?= htmlspecialchars($u['nomubi']) ?> (<?= htmlspecialchars($u['codubi']) ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Cantidad inicial <span class="text-danger">*</span></label>
            <input type="number" name="cant" class="form-control"
                   value="<?= isset($datOne)?htmlspecialchars($datOne[0]['cant']??0):0 ?>"
                   min="0" step="0.01" required>
            <div class="form-text">Saldo inicial. Las entradas y salidas lo ajustan automáticamente.</div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 pb-3 px-4 gap-2">
          <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
            <i class="fa-solid fa-floppy-disk me-2"></i>Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==========================================================
     MODAL: MOVIMIENTO RÁPIDO con Kardex
     ========================================================== -->
<div class="modal fade" id="modalMovimiento" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:460px">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header inv-modal-hd text-white border-0 py-3">
        <h5 class="modal-title fw-bold" id="movTitulo">Movimiento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="home.php?pg=<?= $pg ?>" method="POST">
        <div class="modal-body p-4">
          <input type="hidden" name="ope"      value="save">
          <input type="hidden" name="idinv"    id="mov_idinv">
          <input type="hidden" name="mov_tipo" id="mov_tipo">
          <!-- Info producto -->
          <div class="alert border mb-3 py-2 px-3" id="mov_info_box">
            <div class="fw-semibold" id="mov_nomprod"></div>
            <div class="small text-muted">Stock actual: <strong id="mov_stock"></strong> unidades</div>
          </div>
          <!-- Selector Kardex -->
          <div class="mb-3">
            <label class="form-label fw-semibold">Periodo Kardex <span class="text-danger">*</span></label>
            <select name="idkar" id="mov_idkar" class="form-select" required onchange="onKardexChange(this)">
              <option value="">— Seleccione un periodo —</option>
              <?php foreach(($datKardex??[]) as $k): ?>
              <option value="<?= $k['idkar'] ?>"
                      data-cerrado="<?= $k['cerrado'] ?>"
                      data-label="<?= $meses[(int)$k['mes']].' '.$k['anio'] ?>">
                <?= $meses[(int)$k['mes']] ?> <?= $k['anio'] ?>
                <?= $k['cerrado'] ? ' 🔒 (Cerrado)' : ' ✅ (Abierto)' ?>
              </option>
              <?php endforeach; ?>
            </select>
            <div id="kardex_aviso" class="kardex-aviso d-none"></div>
          </div>
          <!-- Cantidad -->
          <div class="mb-3">
            <label class="form-label fw-semibold">Cantidad <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="fa-solid fa-hashtag text-muted"></i></span>
              <input type="number" name="cant_movimiento" id="mov_cantidad"
                     class="form-control form-control-lg" min="0.01" step="0.01" required placeholder="0">
            </div>
            <div id="mov_aviso" class="form-text text-danger d-none">
              <i class="fa-solid fa-triangle-exclamation me-1"></i>Supera el stock disponible
            </div>
          </div>
          <!-- Precio unitario -->
          <div class="mb-3">
            <label class="form-label fw-semibold">
              Precio unitario
              <span class="text-muted fw-normal small">(opcional — se guarda en Valor del movimiento)</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-light">$</span>
              <input type="number" name="precio_movimiento" id="mov_precio"
                     class="form-control" min="0" step="0.01" placeholder="0.00" value="0">
            </div>
          </div>
          <!-- Resultado -->
          <div class="row g-2">
            <div class="col-7">
              <div class="p-3 rounded-3 d-flex flex-column bg-light border">
                <span class="text-muted small">Stock resultante estimado:</span>
                <span class="fw-bold fs-5" id="mov_resultado">—</span>
              </div>
            </div>
            <div class="col-5">
              <div class="p-3 rounded-3 d-flex flex-column bg-light border">
                <span class="text-muted small">Valor total:</span>
                <span class="fw-bold fs-5 text-primary" id="mov_valor_total">$ 0.00</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 pb-3 px-4 gap-2">
          <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn rounded-pill px-4 fw-semibold" id="mov_btn">
            <i class="fa-solid fa-floppy-disk me-2"></i>Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Solo SweetAlert2 — sin DataTables -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const MESES = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
               'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

document.addEventListener('DOMContentLoaded', function () {

    // ── Toggle lotes (chevron) ────────────────────────────────
    document.querySelectorAll('.toggle-lotes').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const target = document.querySelector(btn.dataset.target);
            if (!target) return;
            const open = !target.classList.contains('d-none');
            target.classList.toggle('d-none', open);
            btn.querySelector('i').style.transform = open ? '' : 'rotate(90deg)';
        });
    });

    // ── Búsqueda manual nativa ────────────────────────────────
    const searchInput = document.getElementById('inv_search');
    const counterEl   = document.getElementById('inv_counter');
    const rows        = Array.from(document.querySelectorAll('tr[data-inv-row="1"]'));
    const total       = rows.length;

    function updateCounter(visible) {
        if (counterEl) counterEl.textContent = `Mostrando ${visible}/${total}`;
    }
    function applyFilter() {
        const q = searchInput ? searchInput.value.toLowerCase().trim() : '';
        let visible = 0;
        rows.forEach(function (row) {
            const text  = (row.getAttribute('data-search') || '');
            const match = !q || text.includes(q);
            row.style.display = match ? '' : 'none';
            // Ocultar también la fila de lotes si el padre se oculta
            const childId = row.getAttribute('data-rowid');
            const child = childId ? document.getElementById(childId) : null;
            if (child && !match) child.style.display = 'none';
            if (match) visible++;
        });
        updateCounter(visible);
    }
    if (searchInput) searchInput.addEventListener('input', applyFilter);
    updateCounter(total);

    // ── Abrir modal Nuevo si venimos de ope=edi ───────────────
    <?php if(isset($datOne) && $datOne): ?>
    new bootstrap.Modal(document.getElementById('modalNuevo')).show();
    <?php endif; ?>

    // ── Alertas URL msg ───────────────────────────────────────
    const msg = new URLSearchParams(window.location.search).get('msg');
    const msgs = {
        saved:          { icon:'success', title:'¡Guardado!',           text:'Registro creado correctamente.' },
        updated:        { icon:'info',    title:'¡Actualizado!',        text:'Inventario actualizado.' },
        deleted:        { icon:'warning', title:'¡Eliminado!',          text:'Registro eliminado.' },
        entrada:        { icon:'success', title:'✅ Entrada registrada', text:'Stock incrementado y movimiento guardado en el Kardex.' },
        salida:         { icon:'success', title:'✅ Salida registrada',  text:'Stock decrementado y movimiento guardado en el Kardex.' },
        sin_stock:      { icon:'error',   title:'Sin stock suficiente', text:'No hay unidades disponibles para esta salida.' },
        sin_kardex:     { icon:'warning', title:'Kardex requerido',     text:'Selecciona un periodo de Kardex para registrar el movimiento.' },
        kardex_cerrado: { icon:'error',   title:'Periodo cerrado 🔒',   text:'El Kardex del periodo seleccionado está cerrado. No se permiten movimientos.' }
    };
    if (msg && msgs[msg]) {
        Swal.fire({ ...msgs[msg], confirmButtonColor: '#2563eb', confirmButtonText: 'Aceptar' });
    }
});

// ── Confirmar eliminación ─────────────────────────────────────
function confirmarEli(url) {
    Swal.fire({
        title: '¿Eliminar registro?',
        text: 'Esta acción solo elimina el registro de inventario, no los movimientos ni lotes.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(r => { if (r.isConfirmed) window.location.href = url; });
}

// ── Modal movimiento rápido ───────────────────────────────────
function abrirMovimiento(idinv, nomprod, stockActual, tipo) {
    const esEnt = tipo === 'entrada';
    document.getElementById('mov_idinv').value             = idinv;
    document.getElementById('mov_tipo').value              = tipo;
    document.getElementById('mov_nomprod').textContent     = nomprod;
    document.getElementById('mov_stock').textContent       = stockActual;
    document.getElementById('mov_cantidad').value          = '';
    document.getElementById('mov_precio').value            = '0';
    document.getElementById('mov_resultado').textContent   = '—';
    document.getElementById('mov_valor_total').textContent = '$ 0.00';
    document.getElementById('mov_aviso').classList.add('d-none');
    document.getElementById('mov_idkar').value             = '';
    const aviso = document.getElementById('kardex_aviso');
    aviso.className = 'kardex-aviso d-none';
    aviso.textContent = '';

    document.getElementById('movTitulo').innerHTML = esEnt
        ? '<i class="fa-solid fa-arrow-down me-2"></i>Registrar Entrada'
        : '<i class="fa-solid fa-arrow-up me-2"></i>Registrar Salida';

    const btn = document.getElementById('mov_btn');
    btn.className = `btn rounded-pill px-4 fw-semibold btn-${esEnt ? 'success' : 'danger'}`;
    btn.innerHTML = `<i class="fa-solid fa-${esEnt ? 'arrow-down' : 'arrow-up'} me-2"></i>Confirmar ${esEnt ? 'Entrada' : 'Salida'}`;
    btn.disabled  = false;
    delete btn.dataset.kardexCerrado;

    document.getElementById('mov_info_box').className =
        `alert border mb-3 py-2 px-3 alert-${esEnt ? 'success' : 'danger'}`;

    function recalcular() {
        const c      = parseFloat(document.getElementById('mov_cantidad').value) || 0;
        const p      = parseFloat(document.getElementById('mov_precio').value)   || 0;
        const res    = esEnt ? stockActual + c : stockActual - c;
        const valTot = c * p;

        const elRes = document.getElementById('mov_resultado');
        elRes.textContent = c > 0 ? res.toFixed(2) + ' unidades' : '—';
        elRes.style.color = res < 0 ? '#dc2626' : '#16a34a';

        document.getElementById('mov_valor_total').textContent =
            '$ ' + valTot.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        const avisoEl = document.getElementById('mov_aviso');
        if (!esEnt && c > stockActual) {
            avisoEl.classList.remove('d-none');
            btn.disabled = true;
        } else {
            avisoEl.classList.add('d-none');
            if (!btn.dataset.kardexCerrado) btn.disabled = false;
        }
    }

    document.getElementById('mov_cantidad').oninput = recalcular;
    document.getElementById('mov_precio').oninput   = recalcular;

    new bootstrap.Modal(document.getElementById('modalMovimiento')).show();
    setTimeout(() => document.getElementById('mov_idkar').focus(), 400);
}

function onKardexChange(sel) {
    const opt    = sel.options[sel.selectedIndex];
    const aviso  = document.getElementById('kardex_aviso');
    const btn    = document.getElementById('mov_btn');
    if (!opt.value) { aviso.className = 'kardex-aviso d-none'; return; }
    const cerrado = opt.dataset.cerrado === '1';
    const label   = opt.dataset.label;
    if (cerrado) {
        aviso.className = 'kardex-aviso cerrado';
        aviso.innerHTML = `<i class="fa-solid fa-lock me-1"></i><strong>Periodo cerrado:</strong> ${label}. No se permiten movimientos.`;
        btn.disabled    = true;
        btn.dataset.kardexCerrado = '1';
    } else {
        aviso.className = 'kardex-aviso abierto';
        aviso.innerHTML = `<i class="fa-solid fa-unlock me-1"></i><strong>Periodo abierto:</strong> ${label}. Listo para registrar.`;
        btn.disabled    = false;
        delete btn.dataset.kardexCerrado;
    }
}
</script>
