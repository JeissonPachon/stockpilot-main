<?php

class Mmod{
    private $idmod;
    private $nommod;
    private $desmod;
    private $fec_crea;
    private $fec_actu;
    private $act;

    function getIdmod(){
        return $this->idmod;
    }
    function getNommod(){
        return $this->nommod;
    }
    function getDesmod(){
        return $this->desmod;
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
    function setIdmod($idmod){
        $this->idmod = $idmod;
    }
    function setNommod($nommod){
        $this->nommod = $nommod;
    }
    function setDesmod($desmod){
        $this->desmod = $desmod;
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

    public function getAll(){
        try{
            $sql = "SELECT idmod, nommod, desmod, fec_crea, fec_actu, act FROM modulo";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error".$e."<br><br>";
        }
    }

    public function getOne(){
        try{
            $sql = "SELECT idmod, nommod, desmod, fec_crea, fec_actu, act FROM modulo WHERE idmod=:idmod";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idmod = $this->getIdmod();
            $result->bindParam(':idmod', $idmod);
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error".$e."<br><br>";
        }
    }

    public function save(){
        try{
            $sql = "INSERT INTO modulo(nommod, desmod, fec_crea, fec_actu, act) VALUES (:nommod, :desmod, :fec_crea, :fec_actu, :act) ";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $nommod = $this->getNommod();
            $result->bindParam(':nommod', $nommod);
            $desmod = $this->getDesmod();
            $result->bindParam(':desmod', $desmod);
            $fec_crea = $this->getFec_crea();
            $result->bindParam(':fec_crea', $fec_crea);
            $fec_actu = $this->getFec_actu();
            $result->bindParam(':fec_actu', $fec_actu);
            $act = $this->getAct();
            $result->bindParam(':act', $act);
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error".$e."<br><br>";
        }
    }

    public function edit(){
        try{
            $sql = "UPDATE modulo SET nommod=:nommod, desmod=:desmod, fec_crea=:fec_crea, fec_actu=:fec_actu, act=:act WHERE idmod=:idmod";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idmod = $this->getIdmod();
            $result->bindParam(':idmod', $idmod);
            $nommod = $this->getNommod();
            $result->bindParam(':nommod', $nommod);
            $desmod = $this->getDesmod();
            $result->bindParam(':desmod', $desmod);
            $fec_crea = $this->getFec_crea();
            $result->bindParam(':fec_crea', $fec_crea);
            $fec_actu = $this->getFec_actu();
            $result->bindParam(':fec_actu', $fec_actu);
            $act = $this->getAct();
            $result->bindParam(':act', $act);
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error".$e."<br><br>";
        }
    }

    public function del(){
        try{
            $sql = "DELETE FROM modulo WHERE idmod=:idmod";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idmod = $this->getIdmod();
            $result->bindParam(':idmod', $idmod);
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error".$e."<br><br>";
        }
    }
}

?>
