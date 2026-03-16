<?php

require_once('models/memp.php');
require_once('models/conexion.php');

$memp = new Memp();

// ===== Variables del Formulario =====
$idemp     = isset($_REQUEST['idemp']) ? $_REQUEST['idemp'] : NULL;
$nomemp    = isset($_POST['nomemp']) ? $_POST['nomemp'] : NULL;
$razemp    = isset($_POST['razemp']) ? $_POST['razemp'] : NULL;
$diremp    = isset($_POST['diremp']) ? $_POST['diremp'] : NULL;
$telemp    = isset($_POST['telemp']) ? $_POST['telemp'] : NULL;
$emaemp    = isset($_POST['emaemp']) ? $_POST['emaemp'] : NULL;
$nitemp    = isset($_POST['nitemp']) ? $_POST['nitemp'] : NULL;

$estado    = NULL;
$act       = NULL;

$ope       = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : NULL;
$datOne    = NULL;

$pg = isset($_REQUEST['pg']) ? $_REQUEST['pg'] : 1001;

$perfil = $_SESSION['idper'] ?? 0;
$idusu_sesion = $_SESSION['idusu'] ?? 0;

// 🔑 CAMBIO 1: Capturar la variable 'year' de la solicitud (puede ser NULL)
$year = isset($_REQUEST['year']) && is_numeric($_REQUEST['year']) ? (int)$_REQUEST['year'] : NULL;


// ===== Asignar ID =====
$memp->setIdemp($idemp);

// Manejo del logo, estado y act
$logo_nombre_final = 'logo.png';
$error_subida = null;
$act_actual = 1;
$estado_actual = 'Activa';

if ($idemp) {
    $empresa_actual_raw = $memp->getOne();
    $empresa_actual = $empresa_actual_raw[0] ?? [];

    if (!empty($empresa_actual['logo'])) {
        $logo_nombre_final = $empresa_actual['logo'];
    }
    $act_actual = $empresa_actual['act'] ?? 1;
    $estado_actual = $empresa_actual['estado'] ?? 'Activa';
}

// ==========================================================
// CREACIÓN (ope = save_reg)
// ==========================================================
if ($ope == "save_reg" && !$idemp) {

    $memp->setNomemp($nomemp);
    $memp->setRazemp($razemp);
    $memp->setNitemp($nitemp);
    $memp->setDiremp($diremp);
    $memp->setTelemp($telemp);
    $memp->setEmaemp($emaemp);

    $memp->setAct(1);
    $memp->setEstado('Activa');
    $memp->setIdusu($idusu_sesion);
    $memp->setFec_crea(date('Y-m-d H:i:s'));
    $memp->setFec_actu(date('Y-m-d H:i:s'));

    $logo_nombre_inicial = 'logo.png';
    $new_idemp = 0;

    try {
        $memp->setLogo($logo_nombre_inicial);
        $new_idemp = $memp->insertNewEmpresa();

        if ($new_idemp > 0) {

            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == 0) {
                $directorio_subida = "img/logos/";
                $extension = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
                $extensiones_permitidas = ['jpg','jpeg','png','gif','webp','avif','svg'];

                if (in_array($extension, $extensiones_permitidas)) {
                    $nuevo_logo_nombre = $new_idemp . '_' . time() . '.' . $extension;
                    $ruta_destino = $directorio_subida . $nuevo_logo_nombre;

                    if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $ruta_destino)) {

                        $sql_update_logo = "UPDATE empresa SET logo = :logo WHERE idemp = :idemp";
                        $modelo = new conexion();
                        $conexion = $modelo->get_conexion();
                        $result_logo = $conexion->prepare($sql_update_logo);
                        $result_logo->bindParam(':logo', $nuevo_logo_nombre);
                        $result_logo->bindParam(':idemp', $new_idemp);
                        $result_logo->execute();
                    }
                }
            }

            $success_link = $memp->linkUsuEmp($idusu_sesion, $new_idemp);

            if ($success_link) {
                header("Location: home.php?pg=1001&msg=created");
                exit;
            } else {
                $error_db = urlencode('Empresa creada pero fallo el link con el usuario.');
                header("Location: home.php?pg=1001&error=$error_db");
                exit;
            }

        } else {
            $error_db = urlencode('No se pudo crear la empresa.');
            header("Location: home.php?pg=1001&error=$error_db");
            exit;
        }

    } catch(Exception $e) {
        $error_db = urlencode('Error fatal: ' . $e->getMessage());
        header("Location: home.php?pg=1001&error=$error_db");
        exit;
    }
}

// ==========================================================
// EDICION (ope = save)
// ==========================================================
if ($ope == "save" && $idemp) {

    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == 0) {

        $directorio_subida = "img/logos/";
        $extension = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
        $extensiones_permitidas = ['jpg','jpeg','png','gif','webp','avif','svg'];

        if (!in_array($extension, $extensiones_permitidas)) {
            $error_subida = 'Formato no permitido.';
        } else {
            $nuevo_logo_nombre = $idemp . '_' . time() . '.' . $extension;
            $ruta_destino = $directorio_subida . $nuevo_logo_nombre;

            if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $ruta_destino)) {
                $logo_nombre_final = $nuevo_logo_nombre;
            } else {
                $error_subida = 'Error al subir el logo.';
            }
        }
    }

    if ($error_subida) {
        $encoded_error = urlencode($error_subida);
        header("Location: home.php?pg=1001&error=$encoded_error&idemp=$idemp&ope=edi");
        exit;
    }

    $memp->setNomemp($nomemp);
    $memp->setRazemp($razemp);
    $memp->setDiremp($diremp);
    $memp->setTelemp($telemp);
    $memp->setEmaemp($emaemp);
    $memp->setLogo($logo_nombre_final);

    if ($perfil == 1) {
        $memp->setNitemp($nitemp);
        $memp->setAct($act_actual);
        $memp->setEstado($estado_actual);
        $memp->setIdusu($empresa_actual['idusu'] ?? $idusu_sesion);
        $memp->setFec_crea($empresa_actual['fec_crea'] ?? date('Y-m-d H:i:s'));
        $memp->setFec_actu(date('Y-m-d H:i:s'));

        $success = $memp->edit();
    } else {
        $success = $memp->editByEmpresa();
    }

    if ($success) {
        header("Location: home.php?pg=1001&msg=updated");
        exit;
    } else {
        $error_db = urlencode('No se pudo actualizar.');
        header("Location: home.php?pg=1001&error=$error_db&idemp=$idemp&ope=edi");
        exit;
    }
}

if ($ope == "edi" && $idemp) {
    $datOne = $memp->getOne();
}

$datAll = $memp->getAll();


// =========================================================================
// !!! LÓGICA: CARGA DE DATOS PARA GRÁFICO DE CRECIMIENTO HISTÓRICO !!!
// =========================================================================

// 🔑 CAMBIO 2: Pasamos la variable $year (que puede ser NULL o un número) al modelo.
// El modelo aplicará el filtro de 12 meses si $year es NULL.
$crecimientoData = $memp->getCrecimientoHistorico($year); 

$meses = [];
$acumulado = [];

if (is_array($crecimientoData) && !empty($crecimientoData)) {
    // Recorrer los datos obtenidos de la BD
    foreach ($crecimientoData as $row) {
        // Asegurarse de que las claves del array coincidan con lo que devuelve el modelo.
        $meses[] = $row['etiqueta_mes']; 
        $acumulado[] = (int) $row['conteo_acumulado']; // Castear a entero
    }
}

// Convertir los datos a formato JSON para que JavaScript los pueda leer
$crecimientoHistorico = [
    'meses' => $meses,
    'acumulado' => $acumulado
];

// Variable final que se pasa a la vista (usada en el script del punto 2 anterior)
$jsonCrecimiento = json_encode($crecimientoHistorico);

// =========================================================================

?>