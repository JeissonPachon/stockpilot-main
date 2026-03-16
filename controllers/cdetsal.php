<?php
include("models/mdetsal.php");

$mdet = new Mdetsal();

// Variables recibidas
$iddet    = isset($_REQUEST['iddet']) ? intval($_REQUEST['iddet']) : null;
$idemp    = isset($_POST['idemp']) ? intval($_POST['idemp']) : null;
$idsol    = isset($_REQUEST['idsol']) ? intval($_REQUEST['idsol']) : null;
$idprod   = isset($_POST['idprod']) ? intval($_POST['idprod']) : null;
$cantdet  = isset($_POST['cantdet']) ? intval($_POST['cantdet']) : null;
$vundet   = isset($_POST['vundet']) ? $_POST['vundet'] : null;
$fec_crea = date("Y-m-d H:i:s");
$fec_actu = date("Y-m-d H:i:s");
$ope      = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : null;

// Inicializar variables usadas por la vista (evita "undefined variable")
$dtOne = null;
$dtAll = [];

// Set IDs
$mdet->setIddet($iddet);
$mdet->setIdsol($idsol);

// Guardar (crear/editar)
if($ope === "save" && $idemp && $idsol && $idprod && $cantdet){
    $mdet->setIdemp($idemp);
    $mdet->setIdsol($idsol);
    $mdet->setIdprod($idprod);
    $mdet->setCantdet($cantdet);
    $mdet->setVundet($vundet);
    $mdet->setFec_crea($fec_crea);
    $mdet->setFec_actu($fec_actu);

    if($iddet){
        $mdet->upd();
    } else {
        $mdet->save();
    }
}

// Eliminar (por parámetro delete)
if(isset($_GET['delete']) && intval($_GET['delete'])){
    $mdet->setIddet(intval($_GET['delete']));
    $mdet->del();
}

// Editar (traer uno para llenar el formulario)
if($ope === "eDi" && $iddet){
    $mdet->setIddet($iddet);
   $dtOne = $mdet->getOne(); // retorna array asociativo o null
}

// Listar: si se pasó idsol listamos por solicitud, si no intentamos getAll() si existe
if($idsol){
    $mdet->setIdsol($idsol);
    $dtAll = $mdet->getIdsol(); // listado de detalles para esa solicitud
} else {
    // Si tu modelo tiene getAll() (recomendado), lo usamos; si no, dejamos vacío
    if(method_exists($mdet, 'getAll')){
        $dtAll = $mdet->getAll();
    } else {
        $dtAll = [];
    }
}
?>
