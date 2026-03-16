<?php 
class Msolsal {
    // Atributos
    private $idsol;
    private $idemp;
    private $idubi;
    private $fecsol;
    private $estsol;
    private $totsol;
    private $obssol;
    private $idusu;
    private $idusu_apr;
    private $fec_crea;
    private $fec_actu;

    // Getters
    function getIdsol(){ return $this->idsol; }
    function getIdemp(){ return $this->idemp; }
    function getIdubi(){ return $this->idubi; }
    function getFecsol(){ return $this->fecsol; }
    function getEstsol(){ return $this->estsol; }
    function getTotsol(){ return $this->totsol; }
    function getObssol(){ return $this->obssol; }
    function getIdusu(){ return $this->idusu; }
    function getIdusu_apr(){ return $this->idusu_apr; }
    function getFec_crea(){ return $this->fec_crea; }
    function getFec_actu(){ return $this->fec_actu; }

    // Setters
    function setIdsol($idsol){ $this->idsol = $idsol; }
    function setIdemp($idemp){ $this->idemp = $idemp; }
    function setIdubi($idubi){ $this->idubi = $idubi; }
    function setFecsol($fecsol){ $this->fecsol = $fecsol; }
    function setEstsol($estsol){ $this->estsol = $estsol; }
    function setTotsol($totsol){ $this->totsol = $totsol; }
    function setObssol($obssol){ $this->obssol = $obssol; }
    function setIdusu($idusu){ $this->idusu = $idusu; }
    function setIdusu_apr($idusu_apr){ $this->idusu_apr = $idusu_apr; }
    function setFec_crea($fec_crea){ $this->fec_crea = $fec_crea; }
    function setFec_actu($fec_actu){ $this->fec_actu = $fec_actu; }

    // Obtener todos
    public function getAll(){
        try{
            $sql = "SELECT * FROM solsalida";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $res = $conexion->prepare($sql);
            $res->execute();
            return $res->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            men($e);
        }
    }

    // Obtener uno
    public function getOne(){
        try{
            $sql = "SELECT * FROM solsalida WHERE idsol=:idsol";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $res = $conexion->prepare($sql);
            $idsol = $this->getIdsol();
            $res->bindParam(":idsol", $idsol);
            $res->execute();
            return $res->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            men($e);
        }
    }

    // Guardar
    public function save(){
        try{
            $sql = "INSERT INTO solsalida(idemp, idubi, fecsol, estsol, totsol, obssol, idusu, idusu_apr, fec_crea, fec_actu)
                    VALUES(:idemp, :idubi, :fecsol, :estsol, :totsol, :obssol, :idusu, :idusu_apr, :fec_crea, :fec_actu)";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $res = $conexion->prepare($sql);

            $res->bindParam(":idemp", $this->idemp);
            $res->bindParam(":idubi", $this->idubi);
            $res->bindParam(":fecsol", $this->fecsol);
            $res->bindParam(":estsol", $this->estsol);
            $res->bindParam(":totsol", $this->totsol);
            $res->bindParam(":obssol", $this->obssol);
            $res->bindParam(":idusu", $this->idusu);
            $res->bindParam(":idusu_apr", $this->idusu_apr);
            $res->bindParam(":fec_crea", $this->fec_crea);
            $res->bindParam(":fec_actu", $this->fec_actu);

            $res->execute();
            men("save");
        }catch(Exception $e){
            men($e);
        }
    }

    // Actualizar
    public function upd(){
        try{
            $sql = "UPDATE solsalida SET idemp=:idemp, idubi=:idubi, fecsol=:fecsol, estsol=:estsol, 
                    totsol=:totsol, obssol=:obssol, idusu=:idusu, idusu_apr=:idusu_apr, 
                    fec_crea=:fec_crea, fec_actu=:fec_actu 
                    WHERE idsol=:idsol";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $res = $conexion->prepare($sql);

            $res->bindParam(":idsol", $this->idsol);
            $res->bindParam(":idemp", $this->idemp);
            $res->bindParam(":idubi", $this->idubi);
            $res->bindParam(":fecsol", $this->fecsol);
            $res->bindParam(":estsol", $this->estsol);
            $res->bindParam(":totsol", $this->totsol);
            $res->bindParam(":obssol", $this->obssol);
            $res->bindParam(":idusu", $this->idusu);
            $res->bindParam(":idusu_apr", $this->idusu_apr);
            $res->bindParam(":fec_crea", $this->fec_crea);
            $res->bindParam(":fec_actu", $this->fec_actu);

            $res->execute();
            men("upd");
        }catch(Exception $e){
            men($e);
        }
    }

    // Eliminar
    public function del(){
        try{
            $sql = "DELETE FROM solsalida WHERE idsol=:idsol";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $res = $conexion->prepare($sql);
            $idsol = $this->getIdsol();
            $res->bindParam(":idsol", $idsol);
            $res->execute();
            men("del");
        }catch(Exception $e){
            men($e);
        }
    }
}
?>
