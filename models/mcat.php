<?php

class Mcat{
    private $idcat;
    private $nomcat;
    private $descat;
    private $idemp;
    private $fec_crea;
    private $fec_actu;
    private $act;
    
    function getIdcat(){
        return $this->idcat;
    }
    function getNomcat(){
        return $this->nomcat;
    }
    function getDescat(){
        return $this->descat;
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

    function setIdcat($idcat){
        $this->idcat = $idcat;
    }
    function setNomcat($nomcat){
        $this->nomcat = $nomcat;
    }
    function setDescat($descat){
        $this->descat = $descat;
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

    // ✅ MODIFICADO: Filtra por empresa y trae nombre de empresa para SuperAdmin
    public function getAll(){
        try{
            $idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
            $idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

            // Si es SuperAdmin (idper=1), ve TODO con nombre de empresa
            if($idper == 1){
                $sql = "SELECT c.idcat, c.nomcat, c.descat, c.idemp, c.fec_crea, c.fec_actu, c.act,
                               e.nomemp
                        FROM categoria c
                        LEFT JOIN empresa e ON c.idemp = e.idemp
                        ORDER BY c.idcat DESC";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
            } else {
                // Admin/Empresa o Empleado: Solo ve los de SU empresa
                $sql = "SELECT c.idcat, c.nomcat, c.descat, c.idemp, c.fec_crea, c.fec_actu, c.act,
                               e.nomemp
                        FROM categoria c
                        LEFT JOIN empresa e ON c.idemp = e.idemp
                        WHERE c.idemp = :idemp
                        ORDER BY c.idcat DESC";
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
                // SuperAdmin puede ver cualquier categoría
                $sql = "SELECT idcat, nomcat, descat, idemp, fec_crea, fec_actu, act 
                        FROM categoria 
                        WHERE idcat=:idcat";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $idcat = $this->getIdcat();
                $result->bindParam(':idcat', $idcat);
            } else {
                // Admin/Empleado: Solo puede ver los de su empresa
                $sql = "SELECT idcat, nomcat, descat, idemp, fec_crea, fec_actu, act 
                        FROM categoria 
                        WHERE idcat=:idcat AND idemp=:idemp";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $idcat = $this->getIdcat();
                $result->bindParam(':idcat', $idcat);
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
            $sql = "INSERT INTO categoria (nomcat, descat, idemp, fec_crea, fec_actu, act) 
                    VALUES (:nomcat, :descat, :idemp, :fec_crea, :fec_actu, :act)";
            $modelo = new Conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $nomcat = $this->getNomcat();
            $result->bindParam(':nomcat', $nomcat);
            $descat = $this->getDescat();
            $result->bindParam(':descat', $descat);
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
    public function upd(){
        try{
            $idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
            $idemp_session = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

            if($idper == 1){
                // SuperAdmin puede editar cualquier categoría
                $sql = "UPDATE categoria 
                        SET nomcat=:nomcat, descat=:descat, idemp=:idemp, 
                            fec_crea=:fec_crea, fec_actu=:fec_actu, act=:act 
                        WHERE idcat=:idcat";
            } else {
                // Admin/Empleado: Solo puede editar los de su empresa
                $sql = "UPDATE categoria 
                        SET nomcat=:nomcat, descat=:descat, fec_crea=:fec_crea, 
                            fec_actu=:fec_actu, act=:act 
                        WHERE idcat=:idcat AND idemp=:idemp_session";
            }
            
            $modelo = new Conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $idcat = $this->getIdcat();
            $result->bindParam(':idcat', $idcat);
            $nomcat = $this->getNomcat();
            $result->bindParam(':nomcat', $nomcat);
            $descat = $this->getDescat();
            $result->bindParam(':descat', $descat);
            
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
                // SuperAdmin puede eliminar cualquier categoría
                $sql = "DELETE FROM categoria WHERE idcat=:idcat";
                $modelo = new Conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $idcat = $this->getIdcat();
                $result->bindParam(':idcat', $idcat);
            } else {
                // Admin/Empleado: Solo puede eliminar los de su empresa
                $sql = "DELETE FROM categoria WHERE idcat=:idcat AND idemp=:idemp";
                $modelo = new Conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $idcat = $this->getIdcat();
                $result->bindParam(':idcat', $idcat);
                $result->bindParam(':idemp', $idemp);
            }
            
            $result->execute();
            return true;
        }catch(Exception $e){
            echo "Error: ".$e->getMessage()."<br><br>";
            return false;
        }
    }
}
?>
