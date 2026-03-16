<?php

class MVal{
    private $idval;
    private $nomval;
    private $iddom;
    private $codval;
    private $desval;
    private $idemp;  // ✅ NUEVO CAMPO
    private $fec_crea;
    private $fec_actu;
    private $act;

    function getIdval(){
        return $this->idval;
    }
    function getNomval(){
        return $this->nomval;
    }
    function getIddom(){
        return $this->iddom;
    }
    function getCodval(){
        return $this->codval;
    }
    function getDesval(){
        return $this->desval;
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
    
    function setIdval($idval){
        $this->idval = $idval;
    }
    function setNomval($nomval){
        $this->nomval = $nomval;
    }
    function setIddom($iddom){
        $this->iddom = $iddom;
    }
    function setCodval($codval){
        $this->codval = $codval;
    }
    function setDesval($desval){
        $this->desval = $desval;
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

    // ✅ MODIFICADO: Filtra por empresa y trae nombre de empresa + nombre de dominio
    public function getAll(){
        try{
            $idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
            $idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

            // Si es SuperAdmin (idper=1), ve TODO
            if($idper == 1){
                $sql = "SELECT v.idval, v.nomval, v.iddom, v.codval, v.desval, v.idemp, 
                               v.fec_crea, v.fec_actu, v.act,
                               d.nomdom,
                               e.nomemp
                        FROM valor v
                        LEFT JOIN dominio d ON v.iddom = d.iddom
                        LEFT JOIN empresa e ON v.idemp = e.idemp
                        ORDER BY v.idval DESC";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
            } else {
                // Admin/Empresa o Empleado: Solo ve los de SU empresa
                $sql = "SELECT v.idval, v.nomval, v.iddom, v.codval, v.desval, v.idemp, 
                               v.fec_crea, v.fec_actu, v.act,
                               d.nomdom,
                               e.nomemp
                        FROM valor v
                        LEFT JOIN dominio d ON v.iddom = d.iddom
                        LEFT JOIN empresa e ON v.idemp = e.idemp
                        WHERE v.idemp = :idemp
                        ORDER BY v.idval DESC";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $result->bindParam(':idemp', $idemp);
            }
            
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error: ".$e->getMessage()."<br><br>";
        }
    }

    // ✅ MODIFICADO: Filtra por empresa
    public function getOne(){
        try{
            $idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
            $idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

            if($idper == 1){
                // SuperAdmin puede ver cualquier valor
                $sql = "SELECT idval, nomval, iddom, codval, desval, idemp, fec_crea, fec_actu, act 
                        FROM valor 
                        WHERE idval=:idval";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $idval = $this->getIdval();
                $result->bindParam(':idval', $idval);
            } else {
                // Admin/Empleado: Solo puede ver los de su empresa
                $sql = "SELECT idval, nomval, iddom, codval, desval, idemp, fec_crea, fec_actu, act 
                        FROM valor 
                        WHERE idval=:idval AND idemp=:idemp";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $idval = $this->getIdval();
                $result->bindParam(':idval', $idval);
                $result->bindParam(':idemp', $idemp);
            }
            
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error: ".$e->getMessage()."<br><br>";
        }
    }

    // ✅ MODIFICADO: Guarda automáticamente el idemp
    public function save(){
        try{
            $sql = "INSERT INTO valor(nomval, iddom, codval, desval, idemp, fec_crea, fec_actu, act) 
                    VALUES (:nomval, :iddom, :codval, :desval, :idemp, :fec_crea, :fec_actu, :act)";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $nomval = $this->getNomval();
            $result->bindParam(':nomval', $nomval);
            $iddom = $this->getIddom();
            $result->bindParam(':iddom', $iddom);
            $codval = $this->getCodval();
            $result->bindParam(':codval', $codval);
            $desval = $this->getDesval();
            $result->bindParam(':desval', $desval);
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
            echo "Error: ".$e->getMessage()."<br><br>";
            return false;
        }
    }

    // ✅ MODIFICADO: Solo permite editar los de su empresa
    public function edit(){
        try{
            $idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
            $idemp_session = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

            if($idper == 1){
                // SuperAdmin puede editar cualquier valor
                $sql = "UPDATE valor 
                        SET nomval=:nomval, iddom=:iddom, codval=:codval, desval=:desval, 
                            idemp=:idemp, fec_crea=:fec_crea, fec_actu=:fec_actu, act=:act 
                        WHERE idval=:idval";
            } else {
                // Admin/Empleado: Solo puede editar los de su empresa
                $sql = "UPDATE valor 
                        SET nomval=:nomval, iddom=:iddom, codval=:codval, desval=:desval, 
                            fec_crea=:fec_crea, fec_actu=:fec_actu, act=:act 
                        WHERE idval=:idval AND idemp=:idemp_session";
            }
            
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $idval = $this->getIdval();
            $result->bindParam(':idval', $idval);
            $nomval = $this->getNomval();
            $result->bindParam(':nomval', $nomval);
            $iddom = $this->getIddom();
            $result->bindParam(':iddom', $iddom);
            $codval = $this->getCodval();
            $result->bindParam(':codval', $codval);
            $desval = $this->getDesval();
            $result->bindParam(':desval', $desval);
            
            if($idper == 1){
                $idemp = $this->getIdemp();
                $result->bindParam(':idemp', $idemp);
            } else {
                $result->bindParam(':idemp_session', $idemp_session);
            }
            
            $fec_crea = $this->getFec_crea();
            $result->bindParam(':fec_crea', $fec_crea);
            $fec_actu = $this->getFec_actu();
            $result->bindParam(':fec_actu', $fec_actu);
            $act = $this->getAct();
            $result->bindParam(':act', $act);
            
            $result->execute();
            return true;
        }catch(Exception $e){
            echo "Error: ".$e->getMessage()."<br><br>";
            return false;
        }
    }

    // ✅ MODIFICADO: Solo permite eliminar los de su empresa
    public function del(){
        try{
            $idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
            $idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

            if($idper == 1){
                // SuperAdmin puede eliminar cualquier valor
                $sql = "DELETE FROM valor WHERE idval=:idval";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $idval = $this->getIdval();
                $result->bindParam(':idval', $idval);
            } else {
                // Admin/Empleado: Solo puede eliminar los de su empresa
                $sql = "DELETE FROM valor WHERE idval=:idval AND idemp=:idemp";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $idval = $this->getIdval();
                $result->bindParam(':idval', $idval);
                $result->bindParam(':idemp', $idemp);
            }
            
            $result->execute();
            return true;
        }catch(Exception $e){
            echo "Error: ".$e->getMessage()."<br><br>";
            return false;
        }
    }

    // ✅ MODIFICADO: Solo muestra dominios de la empresa del usuario
    public function getAllDom(){
        try{
            $idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
            $idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

            if($idper == 1){
                // SuperAdmin ve todos los dominios
                $sql = "SELECT iddom, nomdom, desdom, idemp, fec_crea, fec_actu, act 
                        FROM dominio 
                        ORDER BY nomdom ASC";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
            } else {
                // Admin/Empleado: Solo ve los dominios de su empresa
                $sql = "SELECT iddom, nomdom, desdom, idemp, fec_crea, fec_actu, act 
                        FROM dominio 
                        WHERE idemp = :idemp
                        ORDER BY nomdom ASC";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $result->bindParam(':idemp', $idemp);
            }
            
            $result->execute();
            $res = $result->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(Exception $e){
            echo "Error: ".$e->getMessage()."<br><br>";
        }
    }
}
?>
