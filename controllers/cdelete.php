<?php
// =========================================================
// Archivo: controllers/cdelete.php
// Objetivo: Centralizar la Eliminación y Redireccionar al home.php
// =========================================================

// 🚀 PASO 1: Iniciar la sesión para acceder a $_SESSION['idper']
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 🚨 1. CONFIGURACIÓN Y MODELOS
// Carga la CLASE Conexion.
require_once('../models/conexion.php'); 

// 🎯 SOLUCIÓN CLAVE: Instanciar la clase y obtener la conexión.
$conexionObj = new Conexion();
$pdo = $conexionObj->get_conexion(); // <-- ¡Aquí se define el objeto $pdo!

// Aseguramos que la conexión se haya obtenido antes de continuar.
if (!$pdo instanceof PDO) {
    header("Location: ../home.php?error=" . urlencode("Error de BD: No se pudo obtener la conexión PDO."));
    exit;
}

require_once('../models/mdelete.php'); // Llamamos al modelo con la lógica de BD


// ID de un usuario "seguro" para reasignar (Superadmin).
$id_seguro_reemplazo = 1; 

// 🚨 2. PARÁMETROS REQUERIDOS
$action = $_GET['action'] ?? null; // 'user' o 'empresa'
$id = $_GET['id'] ?? null; // idusu o idemp a borrar
// ✅ Obtiene el perfil de la sesión.
$current_idper = $_SESSION['idper'] ?? 0; 

// Definimos la página de retorno (pg=XXX)
$pagina_retorno = ($action == 'user') ? 1018 : 1001; 
$result = ['success' => false, 'msg' => 'Operación no ejecutada.'];

// 3. VALIDACIONES BÁSICAS (Verifica si es Superadmin)
if ($current_idper != 1) {
    $result['msg'] = "Acceso Denegado. Solo Superadmin puede ejecutar esta acción.";
} elseif ($action === null || $id === null || !is_numeric($id) || $id <= 0) {
    $result['msg'] = "Parámetros de eliminación inválidos.";
} elseif ($action == 'user' && $id == $id_seguro_reemplazo) {
    $result['msg'] = "No se puede eliminar la cuenta de Superadmin (ID $id_seguro_reemplazo).";
} else {
    // 4. PROCESO DE ELIMINACIÓN
    // $pdo ahora es un objeto PDO válido y se pasa a las funciones de mdelete.php
    switch ($action) {
        case 'user':
            $result = deleteUserLogic($pdo, $id, $id_seguro_reemplazo);
            $pagina_retorno = 1018; 
            break;

        case 'empresa':
            $result = deleteEmpresaLogic($pdo, $id);
            $pagina_retorno = 1001;
            break;
        default:
            $result['msg'] = "Acción de eliminación no reconocida.";
    }
}

// 5. REDIRECCIÓN FINAL (Regresamos al HOME con el mensaje)
$url_params = "pg={$pagina_retorno}";

if ($result['success']) {
    // Éxito: Usamos 'message' para el SweetAlert de éxito.
    $url_params .= "&message=" . urlencode($result['msg']); 
} else {
    // Error: Usamos 'error' para el SweetAlert de error.
    $url_params .= "&error=" . urlencode($result['msg']);
}

// Redireccionamos
header("Location: ../home.php?{$url_params}");
exit;
?>