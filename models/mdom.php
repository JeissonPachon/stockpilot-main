<?php

class MDom{
    private $iddom;
    private $nomdom;
    private $desdom;
    private $idemp;  // ✅ NUEVO CAMPO
    private $fec_crea;
    private $fec_actu;
    private $act;

    // ✅ Getters y Setters actualizados
    function getIddom(){
        return $this->iddom;
    }
    function getNomdom(){
        return $this->nomdom;
    }
    function getDesdom(){
        return $this->desdom;
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
    
    function setIddom($iddom){
        $this->iddom = $iddom;
    }
    function setNomdom($nomdom){
        $this->nomdom = $nomdom;
    }
    function setDesdom($desdom){
        $this->desdom = $desdom;
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

    // ✅ MODIFICADO: Ahora trae el nombre de la empresa
public function getAll(){
    try{
        $idper = isset($_SESSION['idper']) ? $_SESSION['idper'] : NULL;
        $idemp = isset($_SESSION['idemp']) ? $_SESSION['idemp'] : NULL;

        // Si es SuperAdmin (idper=1), ve TODO con nombre de empresa
        if($idper == 1){
            $sql = "SELECT d.iddom, d.nomdom, d.desdom, d.idemp, d.fec_crea, d.fec_actu, d.act,
                           e.nomemp
                    FROM dominio d
                    LEFT JOIN empresa e ON d.idemp = e.idemp
                    ORDER BY d.iddom DESC";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
        } else {
            // Admin/Empresa o Empleado: Solo ve los de SU empresa
            $sql = "SELECT d.iddom, d.nomdom, d.desdom, d.idemp, d.fec_crea, d.fec_actu, d.act,
                           e.nomemp
                    FROM dominio d
                    LEFT JOIN empresa e ON d.idemp = e.idemp
                    WHERE d.idemp = :idemp
                    ORDER BY d.iddom DESC";
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
                // SuperAdmin puede ver cualquier dominio
                $sql = "SELECT iddom, nomdom, desdom, idemp, fec_crea, fec_actu, act 
                        FROM dominio 
                        WHERE iddom=:iddom";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $iddom = $this->getIddom();
                $result->bindParam(':iddom', $iddom);
            } else {
                // Admin/Empleado: Solo puede ver los de su empresa
                $sql = "SELECT iddom, nomdom, desdom, idemp, fec_crea, fec_actu, act 
                        FROM dominio 
                        WHERE iddom=:iddom AND idemp=:idemp";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $iddom = $this->getIddom();
                $result->bindParam(':iddom', $iddom);
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
            $sql = "INSERT INTO dominio(nomdom, desdom, idemp, fec_crea, fec_actu, act) 
                    VALUES (:nomdom, :desdom, :idemp, :fec_crea, :fec_actu, :act)";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $nomdom = $this->getNomdom();
            $result->bindParam(':nomdom', $nomdom);
            $desdom = $this->getDesdom();
            $result->bindParam(':desdom', $desdom);
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
                // SuperAdmin puede editar cualquier dominio
                $sql = "UPDATE dominio 
                        SET nomdom=:nomdom, desdom=:desdom, idemp=:idemp, 
                            fec_crea=:fec_crea, fec_actu=:fec_actu, act=:act 
                        WHERE iddom=:iddom";
            } else {
                // Admin/Empleado: Solo puede editar los de su empresa
                $sql = "UPDATE dominio 
                        SET nomdom=:nomdom, desdom=:desdom, fec_crea=:fec_crea, 
                            fec_actu=:fec_actu, act=:act 
                        WHERE iddom=:iddom AND idemp=:idemp_session";
            }
            
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $iddom = $this->getIddom();
            $result->bindParam(':iddom', $iddom);
            $nomdom = $this->getNomdom();
            $result->bindParam(':nomdom', $nomdom);
            $desdom = $this->getDesdom();
            $result->bindParam(':desdom', $desdom);
            
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
                // SuperAdmin puede eliminar cualquier dominio
                $sql = "DELETE FROM dominio WHERE iddom=:iddom";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $iddom = $this->getIddom();
                $result->bindParam(':iddom', $iddom);
            } else {
                // Admin/Empleado: Solo puede eliminar los de su empresa
                $sql = "DELETE FROM dominio WHERE iddom=:iddom AND idemp=:idemp";
                $modelo = new conexion();
                $conexion = $modelo->get_conexion();
                $result = $conexion->prepare($sql);
                $iddom = $this->getIddom();
                $result->bindParam(':iddom', $iddom);
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
