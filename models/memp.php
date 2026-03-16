<?php
// ===============================================
// Archivo: models/memp.php
// Objetivo: Manejar las operaciones de la tabla de empresas.
// ===============================================

// 🚨 CORRECCIÓN CLAVE: Incluir la definición de la clase 'conexion'
require_once('conexion.php'); 

class Memp {
    private $idemp;
    private $nomemp;
    private $razemp;
    private $nitemp;
    private $diremp;
    private $telemp;
    private $emaemp;
    private $logo; // 🔑 YA EXISTE - Propiedad para el nombre del logo
    private $idusu;
    private $fec_crea;
    private $fec_actu;
    private $act;
    private $estado;

    // GETTERS
    function getIdemp(){ 
        return $this->idemp; 
    }
    function getNomemp(){ 
        return $this->nomemp; 
    }
    function getRazemp(){ 
        return $this->razemp; 
    }
    function getNitemp(){ 
        return $this->nitemp; 
    }
    function getDiremp(){ 
        return $this->diremp; 
    }
    function getTelemp(){ 
        return $this->telemp; 
    }
    function getEmaemp(){ 
        return $this->emaemp; 
    }
    function getLogo(){ 
        return $this->logo; 
    }
    function getIdusu(){ 
        return $this->idusu; 
    }
    function getFec_crea(){ 
        return $this->fec_crea; 
    }
    function getFec_actu(){ 
        return $this->fec_actu; 
    }
    function getAct(){ 
        return $this->act; 
    }
    function getEstado(){ 
        return $this->estado; 
    }

    // SETTERS
    function setIdemp($idemp){ 
        $this->idemp = $idemp; 
    }
    function setNomemp($nomemp){ 
        $this->nomemp = $nomemp; 
    }
    function setRazemp($razemp){ 
        $this->razemp = $razemp; 
    }
    function setNitemp($nitemp){ 
        $this->nitemp = $nitemp; 
    }
    function setDiremp($diremp){ 
        $this->diremp = $diremp; 
    }
    function setTelemp($telemp){ 
        $this->telemp = $telemp; 
    }
    function setEmaemp($emaemp){ 
        $this->emaemp = $emaemp; 
    }
    function setLogo($logo){ 
        $this->logo = $logo; 
    }
    function setIdusu($idusu){ 
        $this->idusu = $idusu; 
    }
    function setFec_crea($fec_crea){ 
        $this->fec_crea = $fec_crea; 
    }
    function setFec_actu($fec_actu){ 
        $this->fec_actu = $fec_actu; 
    }
    function setAct($act){ 
        $this->act = $act; 
    }
    function setEstado($estado){ 
        $this->estado = $estado; 
    }

    // CRUD - (Se mantienen sin cambios)
    public function getAll(){
        try {
            $sql = "SELECT * FROM empresa";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        } catch(Exception $e){
            echo "Error: ".$e."<br><br>";
        }
    }

    public function getOne(){
        try {
            $sql = "SELECT * FROM empresa WHERE idemp=:idemp";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idemp = $this->getIdemp();
            $result->bindParam(':idemp',$idemp);
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        } catch(Exception $e){
            echo "Error".$e."<br><br>";
        }
    }

    public function save(){
    try{
        $sql = "INSERT INTO empresa
                (nomemp, razemp, nitemp, diremp, telemp, emaemp, logo, idusu, fec_crea, fec_actu, act, estado) 
                VALUES 
                (:nomemp, :razemp, :nitemp, :diremp, :telemp, :emaemp, :logo, :idusu, :fec_crea, :fec_actu, :act, :estado)";
        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $result = $conexion->prepare($sql);
        $nomemp   = $this->getNomemp();
        $result->bindParam(':nomemp', $nomemp);
        $razemp   = $this->getRazemp();
        $result->bindParam(':razemp', $razemp);
        $nitemp   = $this->getNitemp();
        $result->bindParam(':nitemp', $nitemp);
        $diremp   = $this->getDiremp();
        $result->bindParam(':diremp', $diremp);
        $telemp   = $this->getTelemp();
        $result->bindParam(':telemp', $telemp);
        $emaemp   = $this->getEmaemp();
        $result->bindParam(':emaemp', $emaemp);
        $logo     = $this->getLogo();
        $result->bindParam(':logo', $logo);
        $idusu    = $this->getIdusu();
        $result->bindParam(':idusu', $idusu);
        $fec_crea = $this->getFec_crea();
        $result->bindParam(':fec_crea', $fec_crea);
        $fec_actu = $this->getFec_actu();
        $result->bindParam(':fec_actu', $fec_actu);
        $act      = $this->getAct();
        $result->bindParam(':act', $act);
        $estado   = $this->getEstado();
        $result->bindParam(':estado', $estado);
        $result->execute();
        return true;
    }catch(Exception $e){
        return false;
    }
}

    public function edit() {
    try {
        $sql = "UPDATE empresa SET 
                    nomemp = :nomemp, 
                    razemp = :razemp, 
                    nitemp = :nitemp, 
                    diremp = :diremp, 
                    telemp = :telemp, 
                    emaemp = :emaemp, 
                    logo = :logo, 
                    idusu = :idusu, 
                    fec_crea = :fec_crea, 
                    fec_actu = :fec_actu, 
                    act = :act, 
                    estado = :estado 
                WHERE idemp = :idemp";

        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $result   = $conexion->prepare($sql);

        // ===== BIND DE PARÁMETROS =====
        $idemp = $this->getIdemp();
        $result->bindParam(':idemp', $idemp);

        $nomemp = $this->getNomemp();
        $result->bindParam(':nomemp', $nomemp);

        $razemp = $this->getRazemp();
        $result->bindParam(':razemp', $razemp);

        $nitemp = $this->getNitemp();
        $result->bindParam(':nitemp', $nitemp);

        $diremp = $this->getDiremp();
        $result->bindParam(':diremp', $diremp);

        $telemp = $this->getTelemp();
        $result->bindParam(':telemp', $telemp);

        $emaemp = $this->getEmaemp();
        $result->bindParam(':emaemp', $emaemp);

        $logo = $this->getLogo();
        $result->bindParam(':logo', $logo);

        $idusu = $this->getIdusu();
        $result->bindParam(':idusu', $idusu);

        $fec_crea = $this->getFec_crea();
        $result->bindParam(':fec_crea', $fec_crea);

        $fec_actu = $this->getFec_actu();
        $result->bindParam(':fec_actu', $fec_actu);

        $act = $this->getAct();
        $result->bindParam(':act', $act);

        $estado = $this->getEstado();
        $result->bindParam(':estado', $estado);

        // Ejecutar
        $result->execute();
        return true;

    } catch (Exception $e) {
        return false;
    }
}


    public function del(){
        try {
            $sql = "DELETE FROM empresa WHERE idemp=:idemp";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idemp = $this->getIdemp();
            $result->bindParam(':idemp',$idemp);
            $result->execute();
            return true;
        } catch(Exception $e){
            return false;
        }
    }

    // 🔑 MÉTODOS CLAVE PARA EL REGISTRO DE EMPRESA (con ajustes)
    
    public function insertNewEmpresa() {
    try {
        $sql = "INSERT INTO empresa
                (nomemp, razemp, nitemp, diremp, telemp, emaemp, logo, idusu, fec_crea, fec_actu, act, estado) 
                VALUES 
                (:nomemp, :razemp, :nitemp, :diremp, :telemp, :emaemp, :logo, :idusu, :fec_crea, :fec_actu, :act, :estado)";
        
        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $result = $conexion->prepare($sql);

        // ====== BIND DE PARÁMETROS ======
        $nomemp = $this->getNomemp();
        $result->bindParam(':nomemp', $nomemp);

        $razemp = $this->getRazemp();
        $result->bindParam(':razemp', $razemp);

        $nitemp = $this->getNitemp();
        $result->bindParam(':nitemp', $nitemp);

        $diremp = $this->getDiremp();
        $result->bindParam(':diremp', $diremp);

        $telemp = $this->getTelemp();
        $result->bindParam(':telemp', $telemp);

        $emaemp = $this->getEmaemp();
        $result->bindParam(':emaemp', $emaemp);

        $logo = $this->getLogo();
        $result->bindParam(':logo', $logo); // Logo correcto

        $idusu = $this->getIdusu();
        $result->bindParam(':idusu', $idusu);

        $fec_crea = $this->getFec_crea();
        $result->bindParam(':fec_crea', $fec_crea);

        $fec_actu = $this->getFec_actu();
        $result->bindParam(':fec_actu', $fec_actu);

        $act = $this->getAct();
        $result->bindParam(':act', $act);

        $estado = $this->getEstado();
        $result->bindParam(':estado', $estado);

        // Ejecuta la inserción
        $result->execute();

        // Retorna ID de la nueva empresa
        return $conexion->lastInsertId();

    } catch (Exception $e) {
        return 0; 
    }
}

public function linkUsuEmp($idusu, $idemp) {
    try {
        $sql = "INSERT INTO usuario_empresa (idusu, idemp, fec_crea) 
                VALUES (:idusu, :idemp, :fec_crea)";
        
        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $result = $conexion->prepare($sql);
        
        $fec_crea = date('Y-m-d H:i:s');

        $result->bindParam(':idusu', $idusu);
        $result->bindParam(':idemp', $idemp);
        $result->bindParam(':fec_crea', $fec_crea);

        return $result->execute();

    } catch(Exception $e) {
        // error_log("Error en Memp->linkUsuEmp: ".$e->getMessage());
        return false;
    }
}

// En tu archivo models/memp.php, dentro de la clase Memp

// En tu archivo models/memp.php, dentro de la clase Memp

public function editByEmpresa(){
    try{
        // 🔑 Consulta limitada: Admin/Empresa NO puede modificar nitemp, act ni estado.
        $sql = "UPDATE empresa SET 
                    nomemp=:nomemp, 
                    razemp=:razemp, 
                    diremp=:diremp, 
                    telemp=:telemp, 
                    emaemp=:emaemp, 
                    logo=:logo, 
                    fec_actu=NOW() 
                WHERE idemp=:idemp";

        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $result = $conexion->prepare($sql);

        // 🔽 Bindeo de parámetros
        $idemp = $this->getIdemp();
        $result->bindParam(':idemp', $idemp);

        $nomemp = $this->getNomemp();
        $result->bindParam(':nomemp', $nomemp);

        $razemp = $this->getRazemp();
        $result->bindParam(':razemp', $razemp);

        $diremp = $this->getDiremp();
        $result->bindParam(':diremp', $diremp);

        $telemp = $this->getTelemp();
        $result->bindParam(':telemp', $telemp);

        $emaemp = $this->getEmaemp();
        $result->bindParam(':emaemp', $emaemp);

        $logo = $this->getLogo();
        $result->bindParam(':logo', $logo); // ✅ Logo bindeado correctamente

        return $result->execute();
    }catch(Exception $e){
        // error_log("Error en Memp->editByEmpresa: " . $e->getMessage());
        return false;
    }
}


public function getCrecimientoHistorico($year = null) {
    try {
        $modelo = new conexion();
        $conexion = $modelo->get_conexion();

        // 1. CONDICIÓN BASE: Siempre excluimos registros sin fecha
        $whereClause = "WHERE e1.fec_crea IS NOT NULL";

        // 2. LÓGICA DE FILTRADO
        if ($year !== null && is_numeric($year)) {
            // A) FILTRO ESPECÍFICO POR AÑO: Anulamos el límite de 12 meses.
            // Esto se activa cuando el usuario selecciona un año (ej: 2025).
            $whereClause .= " AND YEAR(e1.fec_crea) = :year";
        } else {
            // B) FILTRO POR DEFECTO (AÑO NULL): Limitamos a los últimos 12 meses.
            // Esto se activa al cargar la página por primera vez o si eligen "Todo el Histórico".
            $whereClause .= " AND e1.fec_crea >= DATE_SUB(NOW(), INTERVAL 12 MONTH)";
        }
        
        $sql = "
            SELECT 
                DATE_FORMAT(e1.fec_crea, '%b %Y') AS etiqueta_mes,
                (
                    SELECT COUNT(e2.idemp) 
                    FROM empresa e2
                    WHERE DATE_FORMAT(e2.fec_crea, '%Y%m') <= DATE_FORMAT(e1.fec_crea, '%Y%m')
                ) AS conteo_acumulado
            FROM empresa e1
            " . $whereClause . "
            GROUP BY etiqueta_mes, DATE_FORMAT(e1.fec_crea, '%Y%m')
            ORDER BY DATE_FORMAT(e1.fec_crea, '%Y%m') ASC
        ";

        $result = $conexion->prepare($sql);

        // Ligamos el parámetro del año si existe
        if ($year !== null && is_numeric($year)) {
            $result->bindParam(':year', $year, PDO::PARAM_INT);
        }

        $result->execute();
        
        return $result->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error en getCrecimientoHistorico: " . $e->getMessage());
        return [];
    }
}
}
?>