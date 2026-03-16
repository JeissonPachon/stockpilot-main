<?php
require_once('models/mcat.php');

$mcat = new Mcat();

// ✅ Obtener datos de la sesión
$idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
$idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

// Validar permisos básicos
if(!$idper || ($idper != 1 && !$idemp)){
    echo "<script>alert('No tienes permisos para acceder'); window.location.href='home.php';</script>";
    exit;
}

$idcat = isset($_REQUEST['idcat']) ? $_REQUEST['idcat'] : NULL;
$nomcat = isset($_POST['nomcat']) ? $_POST['nomcat'] : NULL;
$descat = isset($_POST['descat']) ? $_POST['descat'] : NULL;
$fec_crea = isset($_POST['fec_crea']) ? $_POST['fec_crea'] : NULL;
$fec_actu = date("Y-m-d H:i:s");
$act = 1;

$ope = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : NULL;

$datOne = NULL;

// ✅ RESTRICCIÓN POR PERFIL
// Perfil 3 (Empleado) = SOLO LECTURA
if($idper == 3 && in_array($ope, ['save', 'eLi'])){
    echo "<script>alert('No tienes permisos para realizar esta acción'); window.location.href='home.php?pg=$pg';</script>";
    exit;
}

if ($ope == "save") {
    $mcat->setNomcat($nomcat);
    $mcat->setDescat($descat);
    $mcat->setFec_crea($fec_crea);
    $mcat->setFec_actu($fec_actu);
    $mcat->setAct($act);
    
    // ✅ ASIGNAR AUTOMÁTICAMENTE EL idemp DEL USUARIO LOGUEADO
    $mcat->setIdemp($idemp);

    // Guardar o actualizar
    if ($idcat) {
        $mcat->setIdcat($idcat);
        $mcat->upd();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=updated';</script>";
        exit;
    } else {
        $mcat->save();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=saved';</script>";
        exit;
    }
}

if ($ope == "eLi" && $idcat) {
    $mcat->setIdcat($idcat);
    $mcat->del();
    echo "<script>window.location.href = 'home.php?pg=$pg&msg=deleted';</script>";
    exit;
}

if ($ope == "eDi" && $idcat) {
    $mcat->setIdcat($idcat);
    $datOne = $mcat->getOne();
}

$dtAll = $mcat->getAll();
?>
