<?php

require_once('../models/minv.php');

// Intentar cargar TCPDF (Composer autoload o rutas comunes). En hosting la ruta puede variar.
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];
$found = false;
foreach ($autoloadPaths as $ap) {
    if (file_exists($ap)) {
        require_once $ap;
        $found = true;
        break;
    }
}

// Si aun no existe la clase TCPDF, intentar cargar el archivo tcpdf.php directamente
if (!class_exists('TCPDF')) {
    $tcpdfPaths = [
        __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php',
        __DIR__ . '/../tcpdf/tcpdf.php',
        __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php',
    ];
    foreach ($tcpdfPaths as $tp) {
        if (file_exists($tp)) {
            require_once $tp;
            break;
        }
    }
}

// Si sigue sin existir, mostrar instrucción clara
if (!class_exists('TCPDF')) {
    die("TCPDF no encontrado. Instala la librería con Composer (composer require tecnickcom/tcpdf) o sube la carpeta TCPDF al servidor y ajusta la ruta en 'generar_pdf_inventario.php'.");
}

// Iniciar sesión si no está iniciada y validar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : null;
$idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : null;
$nomemp = isset($_SESSION['nomemp']) ? $_SESSION['nomemp'] : 'Sin Empresa';

// Permitir solo perfiles 1 y 2, o perfiles con empresa asignada
// Si no hay idper, o si idper no es 1 ni 2 y además no hay idemp -> bloquear
if (empty($idper) || ($idper != 1 && $idper != 2 && empty($idemp))) {
    die("No tienes permisos para generar este reporte");
}

// Obtener datos
$minv = new MInv();
$datAll = $minv->getAll();
$datEmp = $minv->getEmpresa();

// Crear PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($nomemp);
$pdf->SetTitle('Reporte de Inventario');
$pdf->SetSubject('Inventario');

// Quitar header y footer por defecto
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Configurar margenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

// Agregar página
$pdf->AddPage();

// ===== ENCABEZADO =====
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'REPORTE DE INVENTARIO', 0, 1, 'C');
$pdf->Ln(2);

// Información de la empresa
if($datEmp && count($datEmp) > 0){
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, $datEmp[0]['nomemp'], 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 5, 'NIT: ' . $datEmp[0]['nitemp'], 0, 1, 'L');
    $pdf->Cell(0, 5, 'Dirección: ' . $datEmp[0]['diremp'], 0, 1, 'L');
    $pdf->Cell(0, 5, 'Teléfono: ' . $datEmp[0]['telemp'], 0, 1, 'L');
}

$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// ===== TABLA =====
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(52, 58, 64); // Gris oscuro
$pdf->SetTextColor(255, 255, 255); // Blanco

// Encabezado de la tabla
$pdf->Cell(15, 7, 'ID', 1, 0, 'C', true);
$pdf->Cell(50, 7, 'Producto', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Categoría', 1, 0, 'C', true);
$pdf->Cell(50, 7, 'Ubicación', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Cantidad', 1, 1, 'C', true);

// Contenido de la tabla
$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(240, 240, 240);

$fill = false;
$totalItems = 0;
$totalCantidad = 0;

if($datAll && count($datAll) > 0){
    foreach($datAll as $row){
        $pdf->Cell(15, 6, $row['idinv'], 1, 0, 'C', $fill);
        $pdf->Cell(50, 6, $row['nomprod'], 1, 0, 'L', $fill);
        $pdf->Cell(40, 6, $row['nomcat'], 1, 0, 'L', $fill);
        $pdf->Cell(50, 6, $row['nomubi'], 1, 0, 'L', $fill);
        $pdf->Cell(25, 6, $row['cant'], 1, 1, 'C', $fill);
        
        $fill = !$fill;
        $totalItems++;
        $totalCantidad += $row['cant'];
    }
} else {
    $pdf->Cell(180, 6, 'No hay registros de inventario', 1, 1, 'C', false);
}

// Totales
$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(155, 6, 'TOTAL DE PRODUCTOS:', 0, 0, 'R');
$pdf->Cell(25, 6, $totalItems, 0, 1, 'C');
$pdf->Cell(155, 6, 'CANTIDAD TOTAL:', 0, 0, 'R');
$pdf->Cell(25, 6, $totalCantidad, 0, 1, 'C');

// Salida del PDF
$pdf->Output('inventario_' . date('Ymd_His') . '.pdf', 'I');
?>