<?php
// =========================================================
// Archivo: controllers/cstatus.php
// Objetivo: Centralizar la Activación/Desactivación (Soft Delete)
// =========================================================

// 🚀 PASO 1: Iniciar la sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('../models/conexion.php'); 
require_once('../models/mstatus.php'); 

// 🎯 Instanciar la clase y obtener el objeto PDO (Solución al error de conexión)
$conexionObj = new Conexion();
$pdo = $conexionObj->get_conexion();

// Validar que $pdo es una conexión válida
if (!$pdo instanceof PDO) {
    header("Location: ../home.php?error=" . urlencode("Error de configuración: No se pudo obtener la conexión PDO."));
    exit;
}

// 🚨 2. PARÁMETROS REQUERIDOS
$action = $_GET['action'] ?? null; // 'user' o 'empresa'
$id = $_GET['id'] ?? null; // idusu o idemp
$estado = $_GET['estado'] ?? null; // 0 o 1
$current_idper = $_SESSION['idper'] ?? 0; // Perfil del usuario actual
$result = ['success' => false, 'msg' => 'Operación no ejecutada.'];

// Definimos la página de retorno
$pagina_retorno = ($action == 'user') ? 1018 : 1001; 

// 3. VALIDACIONES: Solo el Superadmin (idper=1) puede hacer esto
if ($current_idper != 1) {
    $result['msg'] = "Acceso Denegado. Solo Superadmin puede cambiar el estado.";
} elseif ($action === null || $id === null || !is_numeric($id) || $id <= 0 || !in_array($estado, [0, 1])) {
    $result['msg'] = "Parámetros de estado inválidos.";
} else {
    // 4. PROCESO DE CAMBIO DE ESTADO
    // Convertimos el estado a entero para la lógica de BD
    $estado_int = (int)$estado;
    
    switch ($action) {
        case 'user':
            $result = statusUserLogic($pdo, $id, $estado_int);
            $pagina_retorno = 1018; 
            break;

        case 'empresa':
            $result = statusEmpresaLogic($pdo, $id, $estado_int);
            $pagina_retorno = 1001;
            break;
        default:
            $result['msg'] = "Acción de estado no reconocida.";
    }
}

// 5. REDIRECCIÓN FINAL (Regresamos al HOME con el mensaje)
$url_params = "pg={$pagina_retorno}";

if ($result['success']) {
    $url_params .= "&message=" . urlencode($result['msg']); 
} else {
    $url_params .= "&error=" . urlencode($result['msg']);
}

header("Location: ../home.php?{$url_params}");
exit;
?>