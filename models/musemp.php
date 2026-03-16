<?php
class Musemp {
    private $idusu;
    private $idemp;
    private $fec_crea;

    function getIdusu() { 
        return $this->idusu; 
    }
    function getIdemp() { 
        return $this->idemp; 
    }
    function getFec_crea() { 
        return $this->fec_crea; 
    }

    function setIdusu($idusu) { 
        $this->idusu = $idusu; 
    }
    function setIdemp($idemp) { 
        $this->idemp = $idemp; 
    }
    function setFec_crea($fec_crea) { 
        $this->fec_crea = $fec_crea; 
    }

    // Obtener todos los usuarios vinculados a la empresa
    public function getAll() {
        try {
            $sql = "SELECT ue.idusu, ue.idemp, ue.fec_crea,
                           u.nomusu, u.apeusu, u.tdousu, u.ndousu, u.celusu, u.emausu, u.act,
                           e.nomemp
                    FROM usuario_empresa AS ue
                    INNER JOIN usuario AS u ON ue.idusu = u.idusu
                    INNER JOIN empresa AS e ON ue.idemp = e.idemp
                    WHERE ue.idemp = :idemp";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idemp = $this->getIdemp();
            $result->bindParam(':idemp', $idemp);
            $result->execute();
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Error: ".$e."<br>";
        }
    }

    // Obtener un usuario vinculado específico
    public function getOne() {
        try {
            $sql = "SELECT ue.idusu, ue.idemp, ue.fec_crea,
                           u.nomusu, u.apeusu, u.tdousu, u.ndousu, u.celusu, u.emausu, u.act,
                           e.nomemp
                    FROM usuario_empresa AS ue
                    INNER JOIN usuario AS u ON ue.idusu = u.idusu
                    INNER JOIN empresa AS e ON ue.idemp = e.idemp
                    WHERE ue.idusu = :idusu AND ue.idemp = :idemp";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idusu = $this->getIdusu();
            $idemp = $this->getIdemp();
            $result->bindParam(':idusu', $idusu);
            $result->bindParam(':idemp', $idemp);
            $result->execute();
            return $result->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Error: ".$e."<br>";
        }
    }

    // Guardar vínculo usuario-empresa
    public function save() {
        try {
            $sql = "INSERT INTO usuario_empresa (idusu, idemp, fec_crea)
                    VALUES (:idusu, :idemp, :fec_crea)";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idusu = $this->getIdusu();
            $idemp = $this->getIdemp();
            $fec_crea = $this->getFec_crea();
            $result->bindParam(':idusu', $idusu);
            $result->bindParam(':idemp', $idemp);
            $result->bindParam(':fec_crea', $fec_crea);
            $result->execute();
        } catch (Exception $e) {
            echo "Error: ".$e."<br>";
        }
    }

    // Editar vínculo usuario-empresa
    public function edit() {
        try {
            $sql = "UPDATE usuario_empresa 
                    SET fec_crea = :fec_crea
                    WHERE idusu = :idusu AND idemp = :idemp";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idusu = $this->getIdusu();
            $idemp = $this->getIdemp();
            $fec_crea = $this->getFec_crea();
            $result->bindParam(':idusu', $idusu);
            $result->bindParam(':idemp', $idemp);
            $result->bindParam(':fec_crea', $fec_crea);
            $result->execute();
        } catch (Exception $e) {
            echo "Error: ".$e."<br>";
        }
    }

    // Eliminar vínculo usuario-empresa
    public function del() {
        try {
            $sql = "DELETE FROM usuario_empresa 
                    WHERE idusu = :idusu AND idemp = :idemp";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            $idusu = $this->getIdusu();
            $idemp = $this->getIdemp();
            $result->bindParam(':idusu', $idusu);
            $result->bindParam(':idemp', $idemp);
            $result->execute();
        } catch (Exception $e) {
            echo "Error: ".$e."<br>";
        }
    }
}
?>
