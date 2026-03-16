<?php
require_once('models/mmod.php');
$mmod = new Mmod();
$idmod = isset($_REQUEST['idmod']) ? $_REQUEST['idmod']:NULL;
$nommod = isset($_POST['nommod']) ? $_POST['nommod']:NULL;
$desmod = isset($_POST['desmod']) ? $_POST['desmod']:NULL;
$fec_crea = isset($_POST['fec_crea']) ? $_POST['fec_crea']:NULL;
$fec_actu = isset($_POST['fec_actu']) ? $_POST['fec_actu']:NULL;
$act = isset($_POST['act']) ? $_POST['act']:NULL;
$ope = isset($_REQUEST['ope']) ? $_REQUEST['ope']:NULL;
$datOne = NULL;
$mmod->setIdmod($idmod);
if($ope == "save"){
    $mmod->setNommod($nommod);
    $mmod->setDesmod($desmod);
    $mmod->setFec_crea($fec_crea);
    $mmod->setFec_actu($fec_actu);
    $mmod->setAct($act);
    if(!$idmod) $mmod->save(); else $mmod->edit();
}
if($ope =="eli" && $idmod) $mmod->del();
if($ope =="edi" && $idmod) $datOne = $mmod->getOne();
$datAll = $mmod->getAll();

?>


