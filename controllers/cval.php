<?php
require_once('models/mval.php');

$mval = new MVal();

// ✅ Obtener datos de la sesión
$idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
$idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

// Validar permisos básicos
if(!$idper || ($idper != 1 && !$idemp)){
    echo "<script>alert('No tienes permisos para acceder'); window.location.href='home.php';</script>";
    exit;
}

$idval = isset($_REQUEST['idval']) ? $_REQUEST['idval']:NULL;
$nomval = isset($_POST['nomval']) ? $_POST['nomval']:NULL;
$iddom = isset($_POST['iddom']) ? $_POST['iddom']:NULL;
$codval = isset($_POST['codval']) ? $_POST['codval']:NULL;
$desval = isset($_POST['desval']) ? $_POST['desval']:NULL;
$fec_crea = isset($_POST['fec_crea']) ? $_POST['fec_crea']:NULL;
$fec_actu = date('Y-m-d H:i:s');
$act = 1;

$ope = isset($_REQUEST['ope']) ? $_REQUEST['ope']:NULL;
$datOne = NULL;

// ✅ RESTRICCIÓN POR PERFIL
// Perfil 3 (Empleado) = SOLO LECTURA
if($idper == 3 && in_array($ope, ['save', 'eli'])){
    echo "<script>alert('No tienes permisos para realizar esta acción'); window.location.href='home.php?pg=$pg';</script>";
    exit;
}

$mval->setIdval($idval);

if($ope == "save"){
    $mval->setNomval($nomval);
    $mval->setIddom($iddom);
    $mval->setCodval($codval);
    $mval->setDesval($desval);
    $mval->setFec_crea($fec_crea);
    $mval->setFec_actu($fec_actu);
    $mval->setAct($act);
    
    // ✅ ASIGNAR AUTOMÁTICAMENTE EL idemp DEL USUARIO LOGUEADO
    $mval->setIdemp($idemp);
    
    if($idval){
        $mval->edit();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=updated';</script>";
        exit;
    } else {
        $mval->save();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=saved';</script>";
        exit;
    }
}

if ($ope == "eli" && $idval) {
    $mval->setIdval($idval);
    $mval->del();
    echo "<script>window.location.href = 'home.php?pg=$pg&msg=deleted';</script>";
    exit;
}

if ($ope == "edi" && $idval) {
    $mval->setIdval($idval);
    $datOne = $mval->getOne();
}

$datAll = $mval->getAll();
$datDom = $mval->getAllDom();
?>
