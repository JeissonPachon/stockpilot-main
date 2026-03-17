<?php
class Mprov {
    private $idprov;
    private $idubi;
    private $tipoprov;
    private $nomprov;
    private $docprov;
    private $telprov;
    private $emaprov;
    private $dirprov;
    private $idemp;
    private $fec_crea;
    private $fec_actu;
    private $act;

    // GETTERS
    function getIdprov(){ 
        return $this->idprov; 
    }
    function getIdubi(){ 
        return $this->idubi; 
    }
    function getTipoprov(){ 
        return $this->tipoprov; 
    }
    function getNomprov(){ 
        return $this->nomprov; 
    }
    function getDocprov(){ 
        return $this->docprov; 
    }
    function getTelprov(){ 
        return $this->telprov; 
    }
    function getEmaprov(){ 
        return $this->emaprov; 
    }
    function getDirprov(){ 
        return $this->dirprov; 
    }
    function getIdemp(){ 
        return $this->idemp; 
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

    // SETTERS
    function setIdprov($idprov){ 
        $this->idprov = $idprov; 
    }
    function setIdubi($idubi){ 
        $this->idubi = $idubi; 
    }
    function setTipoprov($tipoprov){ 
        $this->tipoprov = $tipoprov; 
    }
    function setNomprov($nomprov){ 
        $this->nomprov = $nomprov; 
    }
    function setDocprov($docprov){ 
        $this->docprov = $docprov; 
    }
    function setTelprov($telprov){ 
        $this->telprov = $telprov; 
    }
    function setEmaprov($emaprov){ 
        $this->emaprov = $emaprov; 
    }
    function setDirprov($dirprov){ 
        $this->dirprov = $dirprov; 
    }
    function setIdemp($idemp){ 
        $this->idemp = $idemp; 
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

    // Obtener todos los proveedores con info de ubicación y empresa
    // Obtener todos los proveedores con info de ubicación y empresa
public function getAll($idemp = null, $idper = null, $idusu = null){
    try {
        $sql = "SELECT p.idprov, p.tipoprov, p.nomprov, p.docprov, p.telprov, p.emaprov, p.dirprov, p.fec_crea, p.fec_actu, p.act,
                   u.nomubi, u.dirubi, u.depubi, u.ciuubi,
                   e.nomemp, e.telemp, e.emaemp, e.diremp
            FROM proveedor AS p
            LEFT JOIN ubicacion AS u ON p.idubi = u.idubi
            LEFT JOIN empresa AS e ON p.idemp = e.idemp";

        if($idper != 1) { // Si no es SuperAdmin, filtrar por empresa o por empresas vinculadas al usuario
            if (!empty($idemp)) {
                $sql .= " WHERE p.idemp = :idemp";
            } elseif (!empty($idusu)) {
                $sql .= " WHERE p.idemp IN (SELECT idemp FROM usuario_empresa WHERE idusu = :idusu)";
            } else {
                // No tenemos idemp ni idusu: devolver vacío para seguridad
                return [];
            }
        }

        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $result = $conexion->prepare($sql);

        if($idper != 1) {
            if (!empty($idemp)) $result->bindParam(':idemp', $idemp);
            elseif (!empty($idusu)) $result->bindParam(':idusu', $idusu);
        }

        $result->execute();
        $res = $result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    } catch(Exception $e){
        echo "Error: ".$e."<br><br>";
    }
}

// Obtener un proveedor específico
public function getOne($idemp = null, $idper = null, $idusu = null){
    try {
        $sql = "SELECT p.idprov, p.tipoprov, p.nomprov, p.docprov, p.telprov, p.emaprov, p.dirprov, p.fec_crea, p.fec_actu, p.act,
                   u.idubi, u.nomubi, u.dirubi, u.depubi, u.ciuubi,
                   e.idemp, e.nomemp, e.telemp, e.emaemp, e.diremp
            FROM proveedor AS p
            LEFT JOIN ubicacion AS u ON p.idubi = u.idubi
            LEFT JOIN empresa AS e ON p.idemp = e.idemp
            WHERE p.idprov = :idprov";

        if($idper != 1) { // Si no es SuperAdmin, agregar filtro de empresa o empresas vinculadas al usuario
            if (!empty($idemp)) {
                $sql .= " AND p.idemp = :idemp";
            } elseif (!empty($idusu)) {
                $sql .= " AND p.idemp IN (SELECT idemp FROM usuario_empresa WHERE idusu = :idusu)";
            } else {
                return [];
            }
        }

        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $result = $conexion->prepare($sql);

        $idprov = $this->getIdprov();
        $result->bindParam(':idprov', $idprov);
        if($idper != 1) {
            if (!empty($idemp)) $result->bindParam(':idemp', $idemp);
            elseif (!empty($idusu)) $result->bindParam(':idusu', $idusu);
        }

        $result->execute();
        $res = $result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    } catch(Exception $e){
        echo "Error: ".$e."<br><br>";
    }
}


    // Guardar proveedor
    public function save(){
        try{
            $sql = "INSERT INTO proveedor 
                    (idubi, tipoprov, nomprov, docprov, telprov, emaprov, dirprov, idemp, fec_crea, fec_actu, act) 
                    VALUES 
                    (:idubi, :tipoprov, :nomprov, :docprov, :telprov, :emaprov, :dirprov, :idemp, :fec_crea, :fec_actu, :act)";
            
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idubi = $this->getIdubi();
            $result->bindParam(':idubi', $idubi);
            $tipoprov = $this->getTipoprov();
            $result->bindParam(':tipoprov', $tipoprov);
            $nomprov = $this->getNomprov();
            $result->bindParam(':nomprov', $nomprov);
            $docprov = $this->getDocprov();
            $result->bindParam(':docprov', $docprov);
            $telprov = $this->getTelprov();
            $result->bindParam(':telprov', $telprov);
            $emaprov = $this->getEmaprov();
            $result->bindParam(':emaprov', $emaprov);
            $dirprov = $this->getDirprov();
            $result->bindParam(':dirprov', $dirprov);
            $idemp = $this->getIdemp();
            $result->bindParam(':idemp', $idemp);
            $fec_crea = $this->getFec_crea();
            $result->bindParam(':fec_crea', $fec_crea);
            $fec_actu = $this->getFec_actu();
            $result->bindParam(':fec_actu', $fec_actu);
            $act = $this->getAct();
            $result->bindParam(':act', $act);
            $result->execute();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // Editar proveedor
    public function edit(){
        try{
            $sql = "UPDATE proveedor SET 
                        idubi=:idubi, tipoprov=:tipoprov, nomprov=:nomprov, docprov=:docprov, telprov=:telprov,
                        emaprov=:emaprov, dirprov=:dirprov, idemp=:idemp, fec_crea=:fec_crea, fec_actu=:fec_actu, act=:act
                    WHERE idprov=:idprov";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idprov = $this->getIdprov();
            $result->bindParam(':idprov', $idprov);
            $idubi = $this->getIdubi();
            $result->bindParam(':idubi', $idubi);
            $tipoprov = $this->getTipoprov();
            $result->bindParam(':tipoprov', $tipoprov);
            $nomprov = $this->getNomprov();
            $result->bindParam(':nomprov', $nomprov);
            $docprov = $this->getDocprov();
            $result->bindParam(':docprov', $docprov);
            $telprov = $this->getTelprov();
            $result->bindParam(':telprov', $telprov);
            $emaprov = $this->getEmaprov();
            $result->bindParam(':emaprov', $emaprov);
            $dirprov = $this->getDirprov();
            $result->bindParam(':dirprov', $dirprov);
            $idemp = $this->getIdemp();
            $result->bindParam(':idemp', $idemp);
            $fec_crea = $this->getFec_crea();
            $result->bindParam(':fec_crea', $fec_crea);
            $fec_actu = $this->getFec_actu();
            $result->bindParam(':fec_actu', $fec_actu);
            $act = $this->getAct();
            $result->bindParam(':act', $act);
            $result->execute();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // Eliminar proveedor
    public function del(){
        try{
            $sql = "DELETE FROM proveedor WHERE idprov=:idprov";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idprov = $this->getIdprov();
            $result->bindParam(':idprov',$idprov);
            $result->execute();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
}
?>
