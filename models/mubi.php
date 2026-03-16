<?php

class MUbi{
    private $idubi;
    private $nomubi;
    private $codubi;
    private $dirubi;
    private $depubi;
    private $ciuubi;
    private $idemp;
    private $idresp;
    private $fec_crea;
    private $fec_actu;
    private $act;

    function getIdubi(){
        return $this->idubi;
    }
    function getNomubi(){
        return $this->nomubi;
    }
    function getCodubi(){
        return $this->codubi;
    }
    function getDirubi(){
        return $this->dirubi;
    }
    function getDepubi(){
        return $this->depubi;
    }
    function getCiuubi(){
        return $this->ciuubi;
    }
    function getIdemp(){
        return $this->idemp;
    }
    function getIdresp(){
        return $this->idresp;
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
    function setIdubi($idubi){
        $this->idubi = $idubi;
    }
    function setNomubi($nomubi){
        $this->nomubi = $nomubi;
    }
    function setCodubi($codubi){
        $this->codubi = $codubi;
    }
    function setDirubi($dirubi){
        $this->dirubi = $dirubi;
    }
    function setDepubi($depubi){
        $this->depubi = $depubi;
    }
    function setCiuubi($ciuubi){
        $this->ciuubi = $ciuubi;
    }
    function setIdemp($idemp){
        $this->idemp = $idemp;
    }
    function setIdresp($idresp){
        $this->idresp = $idresp;
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

    public function getAll($idemp = null){
        try{
            $sql = "SELECT idubi, nomubi, codubi, dirubi, depubi, ciuubi, idemp, idresp, fec_crea, fec_actu, act FROM ubicacion";
            if($idemp){
                $sql .= " WHERE idemp = :idemp";
            }
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            if($idemp){
                $result->bindParam(':idemp', $idemp);
            }
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error".$e."<br><br>";
        }
    }

    public function getOne(){
        try{
            $sql = "SELECT idubi, nomubi, codubi, dirubi, depubi, ciuubi, idemp, idresp, fec_crea, fec_actu, act FROM ubicacion WHERE idubi=:idubi";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idubi = $this->getIdubi();
            $result->bindParam(':idubi', $idubi);
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error".$e."<br><br>";
        }
    }

    public function save(){
        try{
            $sql = "INSERT INTO ubicacion(nomubi, codubi, dirubi, depubi, ciuubi, idemp, idresp, fec_crea, fec_actu, act) VALUES (:nomubi, :codubi, :dirubi, :depubi, :ciuubi, :idemp, :idresp, :fec_crea, :fec_actu, :act) ";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $nomubi = $this->getNomubi();
            $result->bindParam(':nomubi', $nomubi);
            $codubi = $this->getCodubi();
            $result->bindParam(':codubi', $codubi);
            $dirubi = $this->getDirubi();
            $result->bindParam(':dirubi', $dirubi);
            $depubi = $this->getDepubi();
            $result->bindParam(':depubi', $depubi);
            $ciuubi = $this->getCiuubi();
            $result->bindParam(':ciuubi', $ciuubi);
            $idemp = $this->getIdemp();
            $result->bindParam(':idemp', $idemp);
            $idresp = $this->getIdresp();
            $result->bindParam(':idresp', $idresp);
            $fec_crea = $this->getFec_crea();
            $result->bindParam(':fec_crea', $fec_crea);
            $fec_actu = $this->getFec_actu();
            $result->bindParam(':fec_actu', $fec_actu);
            $act = $this->getAct();
            $result->bindParam(':act', $act);
            $result->execute();
            
            // Retornar el ID del registro insertado
            return $conexion->lastInsertId();
        }catch(Exception $e){
            error_log("Error en MUbi::save() - " . $e->getMessage());
            return false;
        }
    }

    public function edit(){
        try{
            $sql = "UPDATE ubicacion SET nomubi=:nomubi, codubi=:codubi, dirubi=:dirubi, depubi=:depubi, ciuubi=:ciuubi, idemp=:idemp, idresp=:idresp, fec_crea=:fec_crea, fec_actu=:fec_actu, act=:act WHERE idubi=:idubi";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idubi = $this->getIdubi();
            $result->bindParam(':idubi', $idubi);
            $nomubi = $this->getNomubi();
            $result->bindParam(':nomubi', $nomubi);
            $codubi = $this->getCodubi();
            $result->bindParam(':codubi', $codubi);
            $dirubi = $this->getDirubi();
            $result->bindParam(':dirubi', $dirubi);
            $depubi = $this->getDepubi();
            $result->bindParam(':depubi', $depubi);
            $ciuubi = $this->getCiuubi();
            $result->bindParam(':ciuubi', $ciuubi);
            $idemp = $this->getIdemp();
            $result->bindParam(':idemp', $idemp);
            $idresp = $this->getIdresp();
            $result->bindParam(':idresp', $idresp);
            $fec_crea = $this->getFec_crea();
            $result->bindParam(':fec_crea', $fec_crea);
            $fec_actu = $this->getFec_actu();
            $result->bindParam(':fec_actu', $fec_actu);
            $act = $this->getAct();
            $result->bindParam(':act', $act);
            $result->execute();
            
            return true;
        }catch(Exception $e){
            error_log("Error en MUbi::edit() - " . $e->getMessage());
            return false;
        }
    }

    public function del(){
        try{
            $sql = "DELETE FROM ubicacion WHERE idubi=:idubi";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idubi = $this->getIdubi();
            $result->bindParam(':idubi', $idubi);
            $result->execute();
            
            return true;
        }catch(Exception $e){
            error_log("Error en MUbi::del() - " . $e->getMessage());
            return false;
        }
    }
}
?>