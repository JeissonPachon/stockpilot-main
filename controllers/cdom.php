<?php
require_once('models/mdom.php');

$mdom = new MDom();

// ✅ Obtener datos de la sesión
$idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
$idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

// Validar permisos básicos
if(!$idper || ($idper != 1 && !$idemp)){
    echo "<script>alert('No tienes permisos para acceder'); window.location.href='home.php';</script>";
    exit;
}

$iddom = isset($_REQUEST['iddom']) ? $_REQUEST['iddom']:NULL;
$nomdom = isset($_POST['nomdom']) ? $_POST['nomdom']:NULL;
$desdom = isset($_POST['desdom']) ? $_POST['desdom']:NULL;
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

$mdom->setIddom($iddom);

if($ope == "save"){
    $mdom->setNomdom($nomdom);
    $mdom->setDesdom($desdom);
    $mdom->setFec_crea($fec_crea);
    $mdom->setFec_actu($fec_actu);
    $mdom->setAct($act);
    
    // ✅ ASIGNAR AUTOMÁTICAMENTE EL idemp DEL USUARIO LOGUEADO
    $mdom->setIdemp($idemp);
    
    // Guardar o actualizar
    if ($iddom) {
        $mdom->edit();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=updated';</script>";
        exit;
    } else {
        $mdom->save();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=saved';</script>";
        exit;
    }
}

if ($ope == "eli" && $iddom) {
    $mdom->setIddom($iddom);
    $mdom->del();
    echo "<script>window.location.href = 'home.php?pg=$pg&msg=deleted';</script>";
    exit;
}

if($ope =="edi" && $iddom) $datOne = $mdom->getOne();

$datAll = $mdom->getAll();
?>
