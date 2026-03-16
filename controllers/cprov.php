<?php
require_once('models/mprov.php');
require_once('models/mubi.php'); 
require_once('models/memp.php');

$mprov = new Mprov();
$mubi = new Mubi();
$memp = new Memp();

$idprov = isset($_REQUEST['idprov']) ? $_REQUEST['idprov'] : NULL;
$idubi = isset($_POST['idubi']) ? $_POST['idubi'] : NULL;
$tipoprov = isset($_POST['tipoprov']) ? $_POST['tipoprov'] : NULL;
$nomprov = isset($_POST['nomprov']) ? $_POST['nomprov'] : NULL;
$docprov = isset($_POST['docprov']) ? $_POST['docprov'] : NULL;
$telprov = isset($_POST['telprov']) ? $_POST['telprov'] : NULL;
$emaprov = isset($_POST['emaprov']) ? $_POST['emaprov'] : NULL;
$dirprov = isset($_POST['dirprov']) ? $_POST['dirprov'] : NULL;
$idemp = isset($_POST['idemp']) ? $_POST['idemp'] : NULL;
$fec_crea = isset($_POST['fec_crea']) ? $_POST['fec_crea'] : NULL;
$fec_actu = isset($_POST['fec_actu']) ? $_POST['fec_actu'] : NULL;
$act = isset($_POST['act']) ? $_POST['act'] : NULL;

$ope = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : NULL;
$datOne = NULL;

$mprov->setIdprov($idprov);

// Obtener datos de sesión
$idemp_usuario = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : null;
$idper_usuario = isset($_SESSION['idper']) ? $_SESSION['idper'] : null;

if($ope == "save") {
    $mprov->setIdubi($idubi);
    $mprov->setTipoprov($tipoprov);
    $mprov->setNomprov($nomprov);
    $mprov->setDocprov($docprov);
    $mprov->setTelprov($telprov);
    $mprov->setEmaprov($emaprov);
    $mprov->setDirprov($dirprov);
    $mprov->setIdemp($idemp);
    $mprov->setFec_crea($fec_crea);
    $mprov->setFec_actu($fec_actu);
    $mprov->setAct($act);

    // Guardar o actualizar
    if (!$idprov) {
        $mprov->save();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=saved';</script>";
        exit;
    } else {
        $mprov->edit();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=updated';</script>";
        exit;
    }
}

if ($ope == "eli" && $idprov) {
    $mprov->del();
    echo "<script>window.location.href = 'home.php?pg=$pg&msg=deleted';</script>";
    exit;
}

// Editar: llamar getOne con filtro de empresa y perfil
if ($ope == "edi" && $idprov) $datOne = $mprov->getOne($idemp_usuario, $idper_usuario);

// Obtener todos los proveedores según empresa/perfil
$datAll = $mprov->getAll($idemp_usuario, $idper_usuario);

$datUbi = $mubi->getAll();
$datEmp = $memp->getAll();
?>
