<?php 
include("models/msolsal.php"); // modelo de solsalida

$msol = new Msolsal();

// Variables recibidas
$idsol      = isset($_REQUEST['idsol']) ? $_REQUEST['idsol'] : NULL;
$idemp      = isset($_POST['idemp']) ? $_POST['idemp'] : NULL;
$idubi      = isset($_POST['idubi']) ? $_POST['idubi'] : NULL;
$fecsol     = isset($_POST['fecsol']) ? $_POST['fecsol'] : NULL;
$estsol     = isset($_POST['estsol']) ? $_POST['estsol'] : NULL;
$totsol     = isset($_POST['totsol']) ? $_POST['totsol'] : NULL;
$obssol     = isset($_POST['obssol']) ? $_POST['obssol'] : NULL;
$idusu      = isset($_POST['idusu']) ? $_POST['idusu'] : NULL;
$idusu_apr  = isset($_POST['idusu_apr']) ? $_POST['idusu_apr'] : NULL;
$fec_crea   = isset($_POST['fec_crea']) ? $_POST['fec_crea'] : date("Y-m-d H:i:s");
$fec_actu   = date("Y-m-d H:i:s");
$ope        = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : NULL;

$dtOne = NULL;

// Set id
$msol->setIdsol($idsol);

// Guardar o actualizar
if($ope=="SaVe" && $idemp && $idubi){
    $msol->setIdemp($idemp);
    $msol->setIdubi($idubi);
    $msol->setFecsol($fecsol);
    $msol->setEstsol($estsol);
    $msol->setTotsol($totsol);
    $msol->setObssol($obssol);
    $msol->setIdusu($idusu);
    $msol->setIdusu_apr($idusu_apr);
    $msol->setFec_crea($fec_crea);
    $msol->setFec_actu($fec_actu);

    $dtE = $msol->getOne();
    
    if($dtE) {
        // Capturar datos anteriores
        require_once('models/maud.php');
        $datos_ant = json_encode($dtE);
        
        $msol->upd();
        
        // Registrar auditoría - UPDATE
        $maud = new MAud();
        $maud->setIdemp($idemp);
        $maud->setIdusu($_SESSION['idusu']);
        $maud->setTabla('solsalida');
        $maud->setAccion(2); // 2=UPDATE
        $maud->setIdreg($idsol);
        $maud->setDatos_ant($datos_ant);
        $maud->setDatos_nue(json_encode($_POST));
        $maud->setFecha(date('Y-m-d H:i:s'));
        $maud->setIp($_SERVER['REMOTE_ADDR']);
        $maud->save();
    } else {
        $msol->save();
        
        // Registrar auditoría - INSERT
        require_once('models/maud.php');
        $maud = new MAud();
        $maud->setIdemp($idemp);
        $maud->setIdusu($_SESSION['idusu']);
        $maud->setTabla('solsalida');
        $maud->setAccion(1); // 1=INSERT
        $maud->setIdreg($msol->getIdsol());
        $maud->setDatos_ant(null);
        $maud->setDatos_nue(json_encode($_POST));
        $maud->setFecha(date('Y-m-d H:i:s'));
        $maud->setIp($_SERVER['REMOTE_ADDR']);
        $maud->save();
    }
}

// Eliminar
if($ope=="eLi" && $idsol){
    // Capturar datos antes de eliminar
    require_once('models/maud.php');
    $datos_ant = json_encode($msol->getOne());
    
    $msol->del();
    
    // Registrar auditoría - DELETE
    $maud = new MAud();
    $maud->setIdemp($_SESSION['idemp'] ?? null);
    $maud->setIdusu($_SESSION['idusu']);
    $maud->setTabla('solsalida');
    $maud->setAccion(3); // 3=DELETE
    $maud->setIdreg($idsol);
    $maud->setDatos_ant($datos_ant);
    $maud->setDatos_nue(null);
    $maud->setFecha(date('Y-m-d H:i:s'));
    $maud->setIp($_SERVER['REMOTE_ADDR']);
    $maud->save();
}

// Editar (traer uno)
if($ope=="eDi" && $idsol){
   $dtOne = $msol->getOne();
   # var_dump($dtOne);
}

// Listar todos
$dtAll = $msol->getAll();
?>
