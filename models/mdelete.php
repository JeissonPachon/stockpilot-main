<?php
// =========================================================
// Archivo: models/mdelete.php
// Objetivo: Lógica de BD para Eliminación Controlada (Usuario y Empresa).
// =========================================================

/**
 * Ejecuta la eliminación segura de un usuario, reasignando sus dependencias.
 * @param PDO $pdo Objeto de conexión PDO.
 * @param int $id_usu_a_borrar ID del usuario a eliminar.
 * @param int $id_seguro_reemplazo ID de usuario de respaldo (Superadmin).
 * @return array Resultado de la operación (success y msg).
 */
function deleteUserLogic($pdo, $id_usu_a_borrar, $id_seguro_reemplazo = 1) {
    if ($id_usu_a_borrar == $id_seguro_reemplazo) {
        return ['success' => false, 'msg' => 'No se puede eliminar la cuenta de Superadmin (ID ' . $id_seguro_reemplazo . ').'];
    }
    if (!is_numeric($id_usu_a_borrar) || $id_usu_a_borrar <= 0) {
        return ['success' => false, 'msg' => 'ID de usuario inválido.'];
    }

    try {
        $pdo->beginTransaction();
        $params_safe = [':seguro' => $id_seguro_reemplazo, ':id' => $id_usu_a_borrar];

        // 1. REASIGNACIÓN DE EMPRESA y UBICACIÓN (para no eliminarlas)
        $pdo->prepare("UPDATE empresa SET idusu = :seguro WHERE idusu = :id")->execute($params_safe); 
        $pdo->prepare("UPDATE ubicacion SET idresp = :seguro WHERE idresp = :id")->execute($params_safe);
        $pdo->prepare("UPDATE solentrada SET idusu_apr = :seguro WHERE idusu_apr = :id")->execute($params_safe);
        $pdo->prepare("UPDATE solsalida SET idusu_apr = :seguro WHERE idusu_apr = :id")->execute($params_safe);

        // 2. ELIMINACIÓN DE REGISTROS DIRECTAMENTE DEPENDIENTES DEL USUARIO
        $pdo->prepare("DELETE FROM usuario_empresa WHERE idusu = :id")->execute([':id' => $id_usu_a_borrar]);
        $pdo->prepare("DELETE FROM auditoria WHERE idusu = :id")->execute([':id' => $id_usu_a_borrar]);
        $pdo->prepare("DELETE FROM movim WHERE idusu = :id")->execute([':id' => $id_usu_a_borrar]);
        $pdo->prepare("DELETE FROM solentrada WHERE idusu = :id")->execute([':id' => $id_usu_a_borrar]);
        $pdo->prepare("DELETE FROM solsalida WHERE idusu = :id")->execute([':id' => $id_usu_a_borrar]);
        
        // 3. ELIMINACIÓN FINAL DEL USUARIO
        $pdo->prepare("DELETE FROM usuario WHERE idusu = :id")->execute([':id' => $id_usu_a_borrar]);
        
        $pdo->commit();
        // 💡 El mensaje de éxito es el que ahora se pasará al parámetro 'message'
        return ['success' => true, 'msg' => 'Usuario y sus referencias eliminadas/reasignadas exitosamente.', 'action' => 'user'];

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error de BD en mdelete (user): " . $e->getMessage());
        return ['success' => false, 'msg' => 'Error de BD al eliminar usuario.', 'action' => 'user'];
    }
}


/**
 * Ejecuta la eliminación segura de una empresa, borrando todas sus dependencias.
 * @param PDO $pdo Objeto de conexión PDO.
 * @param int $id_emp_a_borrar ID de la empresa a eliminar.
 * @return array Resultado de la operación (success y msg).
 */
function deleteEmpresaLogic($pdo, $id_emp_a_borrar) {
    if (!is_numeric($id_emp_a_borrar) || $id_emp_a_borrar <= 0) {
        return ['success' => false, 'msg' => 'ID de empresa inválido.'];
    }
    
    $id = $id_emp_a_borrar;
    
    try {
        $pdo->beginTransaction();

        // 1. LIMPIEZA DE TABLAS DETALLE (Antes de las solicitudes master)
        $pdo->prepare("DELETE FROM detentrada WHERE idemp = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM detsalida WHERE idemp = :id")->execute([':id' => $id]);
        
        // 2. LIMPIEZA DE INVENTARIO Y TRANSACCIONES
        // Primero eliminar lotes asociados a productos de la empresa
        $pdo->prepare("DELETE FROM lote WHERE idprod IN (SELECT idprod FROM producto WHERE idemp = :id)")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM inventario WHERE idemp = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM movim WHERE idkar IN (SELECT idkar FROM kardex WHERE idemp = :id)")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM kardex WHERE idemp = :id")->execute([':id' => $id]);
        
        // 3. LIMPIEZA DE TABLAS ESTRUCTURALES Y RELACIONES
        $pdo->prepare("DELETE FROM solentrada WHERE idemp = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM solsalida WHERE idemp = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM producto WHERE idemp = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM categoria WHERE idemp = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM ubicacion WHERE idemp = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM proveedor WHERE idemp = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM usuario_empresa WHERE idemp = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM auditoria WHERE idemp = :id")->execute([':id' => $id]);
        
        // 4. ELIMINACIÓN FINAL DE LA EMPRESA
        $pdo->prepare("DELETE FROM empresa WHERE idemp = :id")->execute([':id' => $id]);
        
        $pdo->commit();
        // 💡 El mensaje de éxito es el que ahora se pasará al parámetro 'message'
        return ['success' => true, 'msg' => 'Empresa y todos sus datos han sido eliminados permanentemente.', 'action' => 'empresa'];

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error de BD en mdelete (empresa): " . $e->getMessage());
        return ['success' => false, 'msg' => 'Error de BD al eliminar empresa.', 'action' => 'empresa'];
    }
}
?>