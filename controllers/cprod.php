<?php
require_once('models/mprod.php');
require_once('models/mcat.php');

$mprod = new Mprod();
$mcat = new Mcat();

$idprod = isset($_REQUEST['idprod']) ? $_REQUEST['idprod'] : NULL;
$codprod = isset($_POST['codprod']) ? $_POST['codprod'] : NULL;
$nomprod = isset($_POST['nomprod']) ? $_POST['nomprod'] : NULL;
$desprod = isset($_POST['desprod']) ? $_POST['desprod'] : NULL;
$idcat = isset($_POST['idcat']) ? $_POST['idcat'] : NULL;
$unimed = isset($_POST['unimed']) ? $_POST['unimed'] : NULL;
$stkmin = isset($_POST['stkmin']) ? $_POST['stkmin'] : NULL;
$stkmax = isset($_POST['stkmax']) ? $_POST['stkmax'] : NULL;
$imgprod = isset($_FILES['imgprod']) ? $_FILES['imgprod'] : NULL;
$tipo_inventario = isset($_POST['tipo_inventario']) ? $_POST['tipo_inventario'] : NULL;
$act = isset($_POST['act']) ? $_POST['act'] : 1;
$costouni = isset($_POST['costouni']) ? $_POST['costouni'] : NULL;
$precioven = isset($_POST['precioven']) ? $_POST['precioven'] : NULL;

$ope = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : NULL;
$datOne = NULL;

$mprod->setIdprod($idprod);

if ($ope == "save") {
    $mprod->setCodprod($codprod);
    $mprod->setNomprod($nomprod);
    $mprod->setDesprod($desprod);
    $mprod->setIdcat($idcat);

    $idemp_a_usar = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : 1;
    $mprod->setIdemp($idemp_a_usar);

    $mprod->setUnimed($unimed);
    $mprod->setStkmin($stkmin);
    $mprod->setStkmax($stkmax);

    // ********** LÓGICA DE SUBIDA Y MANEJO DE IMAGEN **********
    $nombre_imagen = NULL;
    $upload_dir = 'img/logos/'; // Directorio de destino
    
    // Si es edición, obtener el nombre de la imagen actual antes de cualquier cambio
    if ($idprod) {
        $datProdActual = $mprod->getOne(isset($_SESSION['idemp']) ? $_SESSION['idemp'] : null, isset($_SESSION['idper']) ? $_SESSION['idper'] : null);
        $nombre_imagen_actual = (!empty($datProdActual) && !empty($datProdActual[0]['imgprod'])) ? $datProdActual[0]['imgprod'] : NULL;
    } else {
        $nombre_imagen_actual = NULL;
    }
    
    // 1. Manejo de la subida de un nuevo archivo
    if ($imgprod && isset($imgprod['error']) && $imgprod['error'] === 0) {
        $ext = pathinfo($imgprod['name'], PATHINFO_EXTENSION);
        $nombre_imagen = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
        
        // Crear el directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($imgprod['tmp_name'], $upload_dir . $nombre_imagen)) {
            // Si la subida es exitosa:
            $mprod->setImgprod($nombre_imagen);
            
            // Si existía una imagen anterior y se subió una nueva, eliminar la antigua (Opcional, pero recomendado)
            if ($idprod && $nombre_imagen_actual && file_exists($upload_dir . $nombre_imagen_actual)) {
                 // ** Descomenta la siguiente línea si deseas eliminar la imagen anterior del servidor.
                 // unlink($upload_dir . $nombre_imagen_actual);
            }
            
        } else {
            // Error en la subida, usar la imagen anterior si existe o NULL
            $mprod->setImgprod($nombre_imagen_actual); 
        }
    } else {
        // 2. Si es una edición y NO se subió un nuevo archivo, mantener el nombre actual (NULL si es nuevo producto)
        $mprod->setImgprod($nombre_imagen_actual);
    }
    // ********** FIN LÓGICA DE SUBIDA Y MANEJO DE IMAGEN **********


    $mprod->setTipo_inventario($tipo_inventario);
    $mprod->setAct($act);
    $mprod->setCostouni($costouni);
    $mprod->setPrecioven($precioven);

    $fec_actu = date("Y-m-d H:i:s");

    // Guardar o actualizar
    if (!$idprod) {
        $mprod->setFec_crea($fec_actu);
        $mprod->setFec_actu($fec_actu);
        $mprod->save();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=saved';</script>";
        exit;
    } else {
        $mprod->setFec_actu($fec_actu);
        $mprod->edit();
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=updated';</script>";
        exit;
    }
}

if ($ope == "eli" && $idprod) {
    // Lógica para obtener el nombre de la imagen antes de eliminar el producto
    $datProd = $mprod->getOne(isset($_SESSION['idemp']) ? $_SESSION['idemp'] : null, isset($_SESSION['idper']) ? $_SESSION['idper'] : null);
    $nombre_imagen_a_eliminar = (!empty($datProd) && !empty($datProd[0]['imgprod'])) ? $datProd[0]['imgprod'] : NULL;
    
    if ($mprod->del()) {
        // Si la eliminación de la DB es exitosa, elimina el archivo del servidor
        if ($nombre_imagen_a_eliminar && file_exists('img/logos/' . $nombre_imagen_a_eliminar)) {
            // ** Descomenta la siguiente línea si deseas eliminar la imagen del servidor al eliminar el producto.
            // unlink('img/logos/' . $nombre_imagen_a_eliminar);
        }
        echo "<script>window.location.href = 'home.php?pg=$pg&msg=deleted';</script>";
        exit;
    } else {
        // Manejo de error si la eliminación de la DB falla
        echo "<script>alert('Error al eliminar el producto.'); window.location.href = 'home.php?pg=$pg';</script>";
        exit;
    }
}

if ($ope == "edi" && $idprod) {
    $idemp_usuario = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : null;
    $idper_usuario = isset($_SESSION['idper']) ? $_SESSION['idper'] : null;
    $datOne = $mprod->getOne($idemp_usuario, $idper_usuario);
}


// Obtener datos según perfil y empresa
$idemp_usuario = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : null;
$idper_usuario = isset($_SESSION['idper']) ? $_SESSION['idper'] : null;

// Llamada a getAll() pasando idemp y idper
$datAll = $mprod->getAll($idemp_usuario, $idper_usuario);

// Categorías igual
$datCat = $mcat->getAll();

?>