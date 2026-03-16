<?php
require_once __DIR__ . '/../models/mlote.php';
require_once __DIR__ . '/../models/mprod.php';
require_once __DIR__ . '/../models/minv.php';
require_once __DIR__ . '/../models/conexion.php';

$mlote = new Mlote();
$mprod = new Mprod();
$minv  = new MInv();

$idper = $_SESSION['idper'] ?? NULL;
$idemp = $_SESSION['idemp'] ?? NULL;

$idlote  = isset($_REQUEST['idlote'])  ? (int)$_REQUEST['idlote']  : NULL;
$idprod  = isset($_POST['idprod'])     ? (int)$_POST['idprod']     : NULL;
$idubi   = isset($_POST['idubi'])      ? (int)$_POST['idubi']      : NULL;
$codlot  = isset($_POST['codlot'])     ? trim($_POST['codlot'])    : NULL;
$fecing  = isset($_POST['fecing'])     ? $_POST['fecing']           : date('Y-m-d H:i:s');
$fecven  = isset($_POST['fecven'])     ? $_POST['fecven']           : NULL;
$cantini = isset($_POST['cantini'])    ? $_POST['cantini']          : NULL;
$cantact = isset($_POST['cantact'])    ? $_POST['cantact']          : NULL;
$costuni = isset($_POST['costuni'])    ? $_POST['costuni']          : 0;
$ope     = isset($_REQUEST['ope'])     ? $_REQUEST['ope']           : NULL;
$dtOne   = NULL;

$mlote->setIdlote($idlote);

// ── GUARDAR / EDITAR ──────────────────────────────────────────
if ($ope == "save") {
    $fecvenLimpia = (!empty($fecven) && $fecven !== '0000-00-00') ? $fecven : null;
    $idubiLimpio  = (!empty($idubi) && $idubi > 0) ? $idubi : null;

    $mlote->setIdprod($idprod);
    $mlote->setCodlot($codlot);
    $mlote->setFecing($fecing);
    $mlote->setFecven($fecvenLimpia);
    $mlote->setCantini($cantini);
    $mlote->setCantact($cantact ?? $cantini);
    $mlote->setCostuni($costuni);
    $mlote->setIdubi($idubiLimpio);
    $mlote->setIddent(0);  // Sin entrada asociada (manual)

    if ($idlote) {
        // Edición: obtener lote anterior para sync del idubi viejo
        $loteAnterior = $mlote->getOne();
        $ok = $mlote->edit();
        if ($ok && $idubiLimpio) {
            $minv->syncInventarioFromLotes($idemp, $idprod, $idubiLimpio);
            // Si cambió ubicación, sync de la ubicación anterior también
            if ($loteAnterior && (int)$loteAnterior['idubi'] !== (int)$idubiLimpio) {
                $minv->syncInventarioFromLotes($idemp, $idprod, (int)$loteAnterior['idubi']);
            }
        }
        $_SESSION['mensaje']      = $ok ? "Lote actualizado correctamente." : "Error al actualizar el lote.";
        $_SESSION['tipo_mensaje'] = $ok ? "success" : "danger";
    } else {
        $ok = $mlote->save();
        if ($ok && $idubiLimpio) {
            $minv->syncInventarioFromLotes($idemp, $idprod, $idubiLimpio);
        }
        if (!$ok) {
            $detalle = isset($_SESSION['error_detalle']) ? ' | SQL: ' . $_SESSION['error_detalle'] : '';
            unset($_SESSION['error_detalle']);
            $_SESSION['mensaje']      = "Error al registrar el lote." . $detalle;
        } else {
            $_SESSION['mensaje']      = "Lote registrado correctamente.";
        }
        $_SESSION['tipo_mensaje'] = $ok ? "success" : "danger";
    }
    echo "<script>window.location.href='home.php?pg=".($pg ?? 1008)."';</script>";
    exit;
}

// ── ELIMINAR ──────────────────────────────────────────────────
if ($ope == "eli" && $idlote) {
    $lotePrevio = $mlote->getOne();
    $ok = $mlote->del();
    if ($ok && $lotePrevio) {
        $minv->syncInventarioFromLotes($idemp, (int)$lotePrevio['idprod'], (int)($lotePrevio['idubi'] ?? 0));
    }
    $_SESSION['mensaje']      = $ok ? "Lote eliminado correctamente." : "Error al eliminar el lote.";
    $_SESSION['tipo_mensaje'] = $ok ? "success" : "danger";
    echo "<script>window.location.href='home.php?pg=".($pg ?? 1008)."';</script>";
    exit;
}

// ── EDITAR (cargar datos) ──────────────────────────────────────
if ($ope == "edi" && $idlote) {
    $dtOne = $mlote->getOne();
}

// ── DATOS PARA LA VISTA ───────────────────────────────────────
$dtAll     = $mlote->getAll($idper == 1 ? null : $idemp, $idper);
$productos = $mprod->getAll($idper == 1 ? null : $idemp, $idper);
$ubicaciones = $mlote->getAllUbi($idemp, $idper);
?>
