<?php
require_once('models/minv.php');
require_once('models/mkard.php');

$minv  = new MInv();
$mkard = new Mkard();

// ✅ Obtener datos de la sesión
$idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
$idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;
$idusu = isset($_SESSION['idusu']) ? $_SESSION['idusu'] : NULL;

// Validar permisos básicos
if(!$idper || ($idper != 1 && !$idemp)){
    echo "<script>alert('No tienes permisos para acceder'); window.location.href='home.php';</script>";
    exit;
}

$mkard->setIdemp($idemp);

$idinv    = isset($_REQUEST['idinv'])           ? $_REQUEST['idinv']           : NULL;
$idprod   = isset($_POST['idprod'])             ? $_POST['idprod']             : NULL;
$idubi    = isset($_POST['idubi'])              ? $_POST['idubi']              : NULL;
$cant     = isset($_POST['cant'])               ? $_POST['cant']               : NULL;
$mov_tipo    = isset($_POST['mov_tipo'])              ? $_POST['mov_tipo']              : NULL;
$cant_mov    = (float)(isset($_POST['cant_movimiento']) ? $_POST['cant_movimiento'] : 0);
$precio_mov  = (float)(isset($_POST['precio_movimiento']) ? $_POST['precio_movimiento'] : 0);
$idkar       = isset($_POST['idkar'])                 ? $_POST['idkar']                 : NULL;
$fec_crea = date('Y-m-d H:i:s');
$fec_actu = date('Y-m-d H:i:s');

$ope      = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : NULL;
$datOne   = NULL;

// ✅ RESTRICCIÓN POR PERFIL — Perfil 3 solo lectura
if($idper == 3 && in_array($ope, ['save', 'eli'])){
    echo "<script>alert('No tienes permisos para realizar esta acción'); window.location.href='home.php?pg=$pg';</script>";
    exit;
}

$minv->setIdinv($idinv);

// ============================================================
// MOVIMIENTO RÁPIDO con validación de Kardex
// ============================================================
if($ope == "save" && $idinv && $mov_tipo && $cant_mov > 0) {

    // Obtener el registro de inventario
    $tmp = $minv->getOne();
    if(!$tmp){
        echo "<script>window.location.href='home.php?pg=$pg&msg=error';</script>"; exit;
    }
    $regInv = $tmp[0];
    $tipmov = ($mov_tipo === 'entrada') ? 1 : 2;

    // Kardex requerido
    if(!$idkar){
        echo "<script>window.location.href='home.php?pg=$pg&msg=sin_kardex';</script>"; exit;
    }

    // Verificar que el kardex exista y no esté cerrado
    $todosKardex = $mkard->getAll();
    $kardexInfo  = null;
    foreach($todosKardex as $k){ if($k['idkar'] == $idkar){ $kardexInfo = $k; break; } }

    if(!$kardexInfo){
        echo "<script>window.location.href='home.php?pg=$pg&msg=sin_kardex';</script>"; exit;
    }
    if((int)$kardexInfo['cerrado'] === 1){
        echo "<script>window.location.href='home.php?pg=$pg&msg=kardex_cerrado';</script>"; exit;
    }

    // Verificar stock para salidas
    if($tipmov == 2 && (float)$regInv['cant'] < $cant_mov){
        echo "<script>window.location.href='home.php?pg=$pg&msg=sin_stock';</script>"; exit;
    }

    // Calcular valor total del movimiento
    $valmov = $cant_mov * $precio_mov;

    // Insertar movimiento en Kardex
    $cn    = (new conexion())->get_conexion();
    $stmtM = $cn->prepare(
        "INSERT INTO movim(idkar, idprod, idubi, idusu, tipmov, cantmov, valmov, fecmov, fec_crea, fec_actu)
         VALUES(:idkar, :idprod, :idubi, :idusu, :tipmov, :cantmov, :valmov, NOW(), NOW(), NOW())"
    );
    $stmtM->execute([
        ':idkar'   => $idkar,
        ':idprod'  => $regInv['idprod'],
        ':idubi'   => $regInv['idubi'] ?? 0,
        ':idusu'   => $idusu,
        ':tipmov'  => $tipmov,
        ':cantmov' => $cant_mov,
        ':valmov'  => $valmov
    ]);

    // Actualizar inventario directamente
    $minv->actualizarCantidad(
        $regInv['idprod'], $regInv['idubi'] ?? 0,
        $regInv['idemp'] ?? $idemp, $cant_mov, $tipmov
    );

    $msgRedir = ($tipmov === 1) ? 'entrada' : 'salida';
    echo "<script>window.location.href='home.php?pg=$pg&msg=$msgRedir';</script>"; exit;
}

// ============================================================
// CRUD normal (ajuste manual / nuevo registro)
// ============================================================
if($ope == "save" && !$mov_tipo){
    $minv->setIdprod($idprod);
    $minv->setIdubi($idubi);
    $minv->setCant($cant);
    $minv->setFec_crea($fec_crea);
    $minv->setFec_actu($fec_actu);
    $minv->setIdemp($idemp);

    if($idinv){
        // Edición: tiene idinv
        $minv->upd();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=updated';</script>"; exit;
    } else {
        // Nuevo registro: verificar que no exista ya esa combinación
        $cnChk = (new conexion())->get_conexion();
        $chk   = $cnChk->prepare("SELECT idinv FROM inventario WHERE idemp=:e AND idprod=:p AND idubi=:u LIMIT 1");
        $chk->execute([':e' => $idemp, ':p' => $idprod, ':u' => $idubi]);
        if($chk->fetch()){
            $_SESSION['mensaje']      = "Ya existe un registro de inventario para ese producto y ubicación. Use el botón de edición para ajustar la cantidad.";
            $_SESSION['tipo_mensaje'] = "warning";
            echo "<script>window.location.href = 'home.php?pg=$pg';</script>"; exit;
        }
        $ok = $minv->save();
        if($ok){
            echo "<script>window.location.href = 'home.php?pg=$pg&msg=saved';</script>"; exit;
        } else {
            $_SESSION['mensaje']      = "Error al guardar. Verifique que el producto y ubicación son válidos.";
            $_SESSION['tipo_mensaje'] = "danger";
            echo "<script>window.location.href = 'home.php?pg=$pg';</script>"; exit;
        }
    }
}

if($ope == "eli" && $idinv){
    $minv->setIdinv($idinv);
    $lotesCount = $minv->countLotesByInv($idinv);
    if($lotesCount > 0){
        $_SESSION['mensaje']      = "No se puede eliminar: hay lotes asociados a este producto y ubicación.";
        $_SESSION['tipo_mensaje'] = "warning";
        echo "<script>window.location.href = 'home.php?pg=$pg';</script>"; exit;
    }
    $minv->del();
    echo "<script>window.location.href = 'home.php?pg=$pg&msg=deleted';</script>"; exit;
}

if($ope == "edi" && $idinv){
    $tmp    = $minv->getOne();
    $datOne = $tmp ? $tmp : null;
}

// ============================================================
// Datos para la vista
// ============================================================
$datAll          = $minv->getStockResumen();
$datProd         = $minv->getAllProd();
$datUbi          = $minv->getAllUbi();
$lotesIndexados  = $minv->getLotesPorInventario();
$datKardex       = $minv->getPeriodosKardex($idemp);   // Periodos para selector de movimiento rápido
?>
