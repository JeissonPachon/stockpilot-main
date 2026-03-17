<?php
// cusemp.php - controlador de usuarios de empresa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('models/musemp.php');
require_once('models/musu.php');
require_once('misfun.php'); // ⬅️ Paso 1: Incluir el archivo de funciones

$usemp = new Musemp();
$musu  = new Musu();

// Leer idusu/idemp de forma segura
$idusu = $_REQUEST['idusu'] ?? null;
$idemp = $_SESSION['idemp'] ?? ($_REQUEST['idemp'] ?? null);

$ope = $_REQUEST['ope'] ?? null;
$datOne = null;
$fec_crea = date("Y-m-d H:i:s");

// Configurar modelo usuario_empresa
$usemp->setIdusu($idusu);
$usemp->setIdemp($idemp);

// Guardar o actualizar usuario
if ($ope == "save") {

    if (!$idusu) {
        $existe = $musu->getByEmail($_POST['emausu']);

        if ($existe) {
            echo "<script>alert('El correo ya está registrado en el sistema.');</script>";

            $idusu = $existe['idusu'];
            $usemp->setIdusu($idusu);
            $usemp->setFec_crea($fec_crea);
            $usemp->save();

        } else {
            $musu->setNomusu($_POST['nomusu']);
            $musu->setApeusu($_POST['apeusu']);
            $musu->setTdousu($_POST['tdousu']);
            $musu->setNdousu($_POST['ndousu']);
            $musu->setEmausu($_POST['emausu']);
            $musu->setCelusu($_POST['celusu']);

            // 🛑 CORRECCIÓN: Usar el hash unificado (creación)
            if (isset($_POST['pasusu']) && !empty($_POST['pasusu'])) {
                $hash_pas = generar_hash_contrasena($_POST['pasusu']);
                $musu->setPasusu($hash_pas);
            }

            $musu->setIdper(3);
            $musu->setFec_crea($fec_crea);
            $musu->setFec_actu($fec_crea);
            $musu->setAct(1);

            $idusu = $musu->save();

            if ($idusu) {
                $usemp->setIdusu($idusu);
                $usemp->setFec_crea($fec_crea);
                $usemp->save();
            }
        }

    } else {
        $musu->setIdusu($idusu);
        $musu->setNomusu($_POST['nomusu']);
        $musu->setApeusu($_POST['apeusu']);
        $musu->setTdousu($_POST['tdousu']);
        $musu->setNdousu($_POST['ndousu']);
        $musu->setEmausu($_POST['emausu']);
        $musu->setCelusu($_POST['celusu']);

        // 🛑 CORRECCIÓN: Usar el hash unificado (edición)
        if (isset($_POST['pasusu']) && !empty($_POST['pasusu'])) {
            $hash_pas = generar_hash_contrasena($_POST['pasusu']);
            $musu->setPasusu($hash_pas);
        } else {
            // Si no se envió nueva contraseña, conservar la existente en la BD
            $current = $musu->getOne();
            if ($current && isset($current['pasusu'])) {
                $musu->setPasusu($current['pasusu']);
            }
        }

        $musu->edit();
    }

    // 🔹 Redirección con mensajes al estilo del ejemplo
    if($idusu) {
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=updated';</script>";
    } else {
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=saved';</script>";
    }
    exit;
}

// Eliminar vínculo usuario-empresa
if ($ope == "eli" && $idusu && $idemp) {
    $usemp->del();
    echo "<script>window.location.href = 'home.php?pg=$pg&msg=deleted';</script>";
    exit;
}

// Obtener datos de un usuario para editar
if ($ope == "edi" && $idusu && $idemp) {
    $datOne = $usemp->getOne();
}

// Obtener todos los usuarios vinculados a la empresa
$usemp->setIdemp($idemp);
$datAll = $usemp->getAll();
?>