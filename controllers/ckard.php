<?php
require_once __DIR__ . '/../models/mkard.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mkard = new Mkard();

// Obtener datos generales
$idkar    = isset($_REQUEST['idkar']) ? $_REQUEST['idkar'] : null;
$anio     = isset($_POST['anio']) ? $_POST['anio'] : null;
$mes      = isset($_POST['mes']) ? $_POST['mes'] : null;
$cerrado  = isset($_POST['cerrado']) ? $_POST['cerrado'] : 0;
$ope      = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : null;
$datOne   = null;
$dtMovimientos = [];
$productos = [];

// Asignar datos comunes al modelo
$mkard->setIdkar($idkar);
$mkard->setAnio($anio);
$mkard->setMes($mes);
$mkard->setCerrado($cerrado);

// ✅ Tomar idemp de la sesión
if (isset($_SESSION['idemp'])) {
    $mkard->setIdemp($_SESSION['idemp']);
} else if (isset($_SESSION['usuario_id'])) {
    // Obtener idemp desde usuario_empresa
    require_once __DIR__ . '/../models/conexion.php';
    $modelo = new conexion();
    $conexion = $modelo->get_conexion();
    $stmt = $conexion->prepare("SELECT idemp FROM usuario_empresa WHERE idusu = ? LIMIT 1");
    $stmt->execute([$_SESSION['usuario_id']]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($empresa && isset($empresa['idemp'])) {
        $mkard->setIdemp($empresa['idemp']);
        $_SESSION['idemp'] = $empresa['idemp']; // Opcional: guardar en sesión para futuras consultas
    } else {
        die("Error: No se encontró la empresa asociada al usuario.");
    }
} else {
    die("Error: No se encontró la empresa en sesión ni usuario logueado.");
}

// Guardar/editar kardex
if ($ope == "save") {
    require_once __DIR__ . '/../models/maud.php';
    $maud = new MAud();
    $accion = $idkar ? 2 : 1; // 1=INSERT, 2=UPDATE
    $datos_ant = $idkar ? json_encode($mkard->getOne()) : null;
    
    $resultado = false;
    if (!$idkar) {
        $idreg = $mkard->save();
        if($idreg) $resultado = true;
    } else {
        $resultado = $mkard->edit();
        $idreg = $idkar;
    }

    if($resultado){
        $maud->setIdemp($_SESSION['idemp']);
        $maud->setIdusu($_SESSION['idusu']);
        $maud->setTabla('kardex');
        $maud->setAccion($accion);
        $maud->setIdreg($idreg);
        $maud->setDatos_ant($datos_ant);
        $maud->setDatos_nue(json_encode($_POST));
        $maud->setFecha(date('Y-m-d H:i:s'));
        $maud->setIp($_SERVER['REMOTE_ADDR']);
        $maud->save();
        
        $_SESSION['mensaje'] = "Kardex guardado exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al guardar el Kardex";
        $_SESSION['tipo_mensaje'] = "danger";
    }
    
    // Redireccionar con JavaScript
    echo "<script>window.location.href='home.php?pg=1007';</script>";
    exit();
}

// Eliminar kardex
if ($ope == "eli" && $idkar) {
    require_once __DIR__ . '/../models/maud.php';
    $maud = new MAud();
    $datos_ant = json_encode($mkard->getOne());
    
    if($mkard->del()){
        $maud->setIdemp($_SESSION['idemp']);
        $maud->setIdusu($_SESSION['idusu']);
        $maud->setTabla('kardex');
        $maud->setAccion(3); // 3=DELETE
        $maud->setIdreg($idkar);
        $maud->setDatos_ant($datos_ant);
        $maud->setDatos_nue(null);
        $maud->setFecha(date('Y-m-d H:i:s'));
        $maud->setIp($_SERVER['REMOTE_ADDR']);
        $maud->save();
        
        $_SESSION['mensaje'] = "Kardex eliminado exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar el Kardex";
        $_SESSION['tipo_mensaje'] = "danger";
    }
    
    echo "<script>window.location.href='home.php?pg=1007';</script>";
    exit();
}

// Editar kardex
if ($ope == "edi" && $idkar) {
    $datOne = $mkard->getOne();
}

// ✅ Guardar movimiento
if ($ope == "addmov" && $idkar) {
    $data = [
        ':idkar'   => $idkar,
        ':idprod'  => $_POST['idprod'],
        ':idubi'   => $_POST['idubi'], // added location
        ':tipmov'  => $_POST['tipmov'],
        ':cantmov' => $_POST['cantmov'],
        ':valmov'  => $_POST['valmov'],
        ':docref'  => $_POST['docref'],
        ':obs'     => $_POST['obs'],
        ':idusu'   => $_SESSION['idusu'] // added user for audit
    ];
    
    $idmov = $mkard->saveMovimiento($data);
    if($idmov){
        require_once __DIR__ . '/../models/maud.php';
        $maud = new MAud();
        $maud->setIdemp($_SESSION['idemp']);
        $maud->setIdusu($_SESSION['idusu']);
        $maud->setTabla('movim');
        $maud->setAccion(1); // 1=INSERT
        $maud->setIdreg($idmov); // ID del movimiento
        $maud->setDatos_ant(null);
        $maud->setDatos_nue(json_encode($data));
        $maud->setFecha(date('Y-m-d H:i:s'));
        $maud->setIp($_SERVER['REMOTE_ADDR']);
        $maud->save();
        
        $_SESSION['mensaje'] = "Movimiento registrado exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al registrar el movimiento";
        $_SESSION['tipo_mensaje'] = "danger";
    }
    
    echo "<script>window.location.href='home.php?pg=1007';</script>";
    exit();
}

// ✅ Consultar movimientos si hay kardex seleccionado
if ($idkar) {
    $dtMovimientos = $mkard->getMovimientos($idkar);
}

// ✅ Consultar productos para el select
$productos = $mkard->getProductos();

// Listar todos los kardex
$dtKardex = $mkard->getAll();
