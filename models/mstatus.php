<?php
// =========================================================
// Archivo: models/mstatus.php
// Objetivo: Lógica de BD para Activar/Desactivar Usuario y Empresa (usando campo 'act').
// =========================================================

/**
 * Activa o desactiva un usuario individualmente.
 * @param PDO $pdo Objeto de conexión PDO.
 * @param int $id_usu ID del usuario.
 * @param int $estado 0 (Inactivo) o 1 (Activo).
 * @return array Resultado de la operación (success y msg).
 */
function statusUserLogic($pdo, $id_usu, $estado) {
    // Definimos la acción para el mensaje de respuesta
    $accion = $estado == 1 ? 'activado' : 'desactivado';

    if (!is_numeric($id_usu) || $id_usu <= 0) {
        return ['success' => false, 'msg' => 'ID de usuario inválido.'];
    }
    
    // El Superadmin (ID=1) siempre debe estar activo.
    $id_superadmin = 1;
    if ($id_usu == $id_superadmin && $estado == 0) {
        return ['success' => false, 'msg' => 'No se puede desactivar la cuenta de Superadmin.'];
    }

    try {
        // Usamos el campo 'act' para actualizar el estado del usuario
        $stmt = $pdo->prepare("UPDATE usuario SET act = :estado, fec_actu = NOW() WHERE idusu = :id");
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id_usu, PDO::PARAM_INT);
        $stmt->execute();

        return ['success' => true, 'msg' => "Usuario $id_usu $accion exitosamente."];

    } catch (Exception $e) {
        error_log("Error de BD en mstatus (user): " . $e->getMessage());
        return ['success' => false, 'msg' => "Error de BD al cambiar estado del usuario a $accion."];
    }
}


/**
 * Activa o desactiva una empresa y a TODOS sus usuarios asociados.
 * @param PDO $pdo Objeto de conexión PDO.
 * @param int $id_emp ID de la empresa.
 * @param int $estado 0 (Inactivo) o 1 (Activo).
 * @return array Resultado de la operación (success y msg).
 */
function statusEmpresaLogic($pdo, $id_emp, $estado) {
    // Definimos la acción para el mensaje de respuesta
    $accion = $estado == 1 ? 'activada' : 'desactivada';

    if (!is_numeric($id_emp) || $id_emp <= 0) {
        return ['success' => false, 'msg' => 'ID de empresa inválido.'];
    }
    
    try {
        $pdo->beginTransaction();

        // 1. Desactivar/Activar la EMPRESA (usando el campo 'act')
        $stmt_emp = $pdo->prepare("UPDATE empresa SET act = :estado, fec_actu = NOW() WHERE idemp = :id");
        $stmt_emp->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt_emp->bindParam(':id', $id_emp, PDO::PARAM_INT);
        $stmt_emp->execute();

        // 2. Desactivar/Activar TODOS los usuarios de la empresa (Exceptuando Superadmin idper=1)
        // Usamos el campo 'act' para actualizar el estado del usuario
        $stmt_usu = $pdo->prepare("
            UPDATE usuario u
            INNER JOIN usuario_empresa ue ON u.idusu = ue.idusu
            SET u.act = :estado, u.fec_actu = NOW()
            WHERE ue.idemp = :id_emp AND u.idper != 1
        ");
        $stmt_usu->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt_usu->bindParam(':id_emp', $id_emp, PDO::PARAM_INT);
        $stmt_usu->execute();

        $pdo->commit();
        
        return ['success' => true, 'msg' => "Empresa $id_emp $accion exitosamente y sus usuarios han sido actualizados."];

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error de BD en mstatus (empresa): " . $e->getMessage());
        return ['success' => false, 'msg' => "Error de BD al cambiar estado de la empresa a $accion."];
    }
}
?>