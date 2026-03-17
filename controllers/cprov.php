<?php
require_once('models/mprov.php');
require_once('models/mubi.php'); 
require_once('models/memp.php');

// Asegurar sesión (puede venir desde home.php pero toleramos acceso directo)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Página actual (para redirecciones desde aquí)
$pg = isset($_REQUEST['pg']) ? $_REQUEST['pg'] : 1003;

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
 $idusu_usuario = isset($_SESSION['idusu']) ? $_SESSION['idusu'] : null;

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

    // Si no se envió idemp en el formulario, usar la empresa del usuario (salvo Superadmin)
    if (empty($idemp)) {
        if (!empty($idemp_usuario)) {
            $mprov->setIdemp($idemp_usuario);
        } else {
            // Intentar obtener la primera empresa vinculada al usuario desde la BD
            if (!empty($idusu_usuario)) {
                require_once('models/conexion.php');
                $model = new conexion();
                $conn = $model->get_conexion();
                $stmt = $conn->prepare('SELECT idemp FROM usuario_empresa WHERE idusu = :idusu LIMIT 1');
                $stmt->bindParam(':idusu', $idusu_usuario);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && isset($row['idemp'])) {
                    $mprov->setIdemp($row['idemp']);
                }
            }
        }
    }

    // Si el usuario no es Superadmin, forzar la empresa desde la sesión (evita que se guarde sin empresa correcta)
    if ($idper_usuario != 1 && !empty($idemp_usuario)) {
        $mprov->setIdemp($idemp_usuario);
    }

    // Si no se envió act, por defecto activo (1)
    if ($act === NULL || $act === '') {
        $mprov->setAct(1);
    }

    // Guardar o actualizar
    if (empty($idprov)) {
        $ok = $mprov->save();
        $msg = $ok ? 'saved' : 'err';
        echo "<script>window.location.href = 'home.php?pg={$pg}&msg={$msg}';</script>";
        exit;
    } else {
        $ok = $mprov->edit();
        $msg = $ok ? 'updated' : 'err';
        echo "<script>window.location.href = 'home.php?pg={$pg}&msg={$msg}';</script>";
        exit;
    }
}

if ($ope == "eli" && $idprov) {
    $mprov->del();
    echo "<script>window.location.href = 'home.php?pg=$pg&msg=deleted';</script>";
    exit;
}

// Editar: llamar getOne con filtro de empresa y perfil
// Obtener todos los proveedores según empresa/perfil

if ($ope == "edi" && $idprov) $datOne = $mprov->getOne($idemp_usuario, $idper_usuario, $idusu_usuario);

// Obtener todos los proveedores según empresa/perfil (si idemp no está en sesión, se usa usuario)
$datAll = $mprov->getAll($idemp_usuario, $idper_usuario, $idusu_usuario);

$datUbi = $mubi->getAll();
$datEmp = $memp->getAll();
?>
