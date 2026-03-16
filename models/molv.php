<?php
// Asegúrate de que 'conexion.php' contenga la clase 'conexion'
// requerida por este modelo.

class Molv {
    // Propiedades privadas.
    private $idusu;
    private $emausu;
    private $keyolv;
    private $fecsol;
    private $pasusu; 
    
    // =======================================================
    // GETTERS & SETTERS (Se mantienen como están)
    // =======================================================
    
    function setIdusu($idusu){ 
        $this->idusu = $idusu; 
    }
    function setEmausu($emausu){ 
        $this->emausu = $emausu; 
    }
    function setKeyolv($keyolv){ 
        $this->keyolv = $keyolv; 
    }
    function setFecsol($fecsol){ 
        $this->fecsol = $fecsol; 
    }
    function setPasusu($pasusu){ 
        $this->pasusu = $pasusu; 
    }

    function getIdusu(){ 
        return $this->idusu; 
    }
    function getEmausu(){ 
        return $this->emausu; 
    }
    function getKeyolv(){ 
        return $this->keyolv; 
    }
    function getFecsol(){ 
        return $this->fecsol; 
    }
    function getPasusu(){ 
        return $this->pasusu; 
    }


    // =======================================================
    // MÉTODOS DE CONSULTA Y ACTUALIZACIÓN
    // =======================================================

    /**
     * Busca un usuario por su correo electrónico. (USADO POR colv.php)
     */
    public function getOneEma(){
        try {
            $sql = "SELECT idusu, nomusu, emausu FROM usuario WHERE emausu=:emausu";
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $emausu = $this->getEmausu();
            $result->bindParam(':emausu', $emausu);
            
            $result->execute();
            $res = $result->fetch(PDO::FETCH_ASSOC); 
            return $res;
        } catch(Exception $e){
            error_log("Error al buscar usuario por email: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza el token (keyolv) y fecha (fecsol) en la tabla 'usuario'. (USADO POR colv.php)
     */
    public function updUsu(){
        try{
            $sql = "UPDATE usuario SET 
                        keyolv=:keyolv, 
                        fecsol=:fecsol, 
                        bloqkey=0, 
                        fec_actu=NOW()
                    WHERE idusu=:idusu";
            
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $keyolv = $this->getKeyolv();
            $result->bindParam(':keyolv', $keyolv);
            
            $fecsol = $this->getFecsol();
            $result->bindParam(':fecsol', $fecsol);
            
            $idusu = $this->getIdusu();
            $result->bindParam(':idusu', $idusu);
            
            $result->execute();
            return true;
        }catch(Exception $e){
            error_log("Error al actualizar token de usuario: ".$e->getMessage());
            return false;
        }
    }

    /**
     * 🚨 MEJORA: Busca usuario por token (keyolv HASHEADO) Y correo electrónico. (USADO POR crct.php)
     */
    public function getOneKey(){
        try {
            // Se añaden más campos para la validación de caducidad
            $sql = "SELECT idusu, nomusu, bloqkey, fecsol FROM usuario 
                    WHERE keyolv=:keyolv AND emausu=:emausu"; 
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $keyolv = $this->getKeyolv(); // Ya viene hasheado desde crct.php
            $result->bindParam(':keyolv', $keyolv);
            
            $emausu = $this->getEmausu();
            $result->bindParam(':emausu', $emausu);
            
            $result->execute();
            $res = $result->fetch(PDO::FETCH_ASSOC); 
            return $res;
        } catch(Exception $e){
            error_log("Error al buscar usuario por token y email: ".$e->getMessage());
            return false;
        }
    }

    /**
     * 🚨 MEJORA: Actualiza la contraseña, ANULA el token y limpia la fecha. (USADO POR crct.php)
     */
    public function updPasusu(){
        try{
            $sql = "UPDATE usuario SET 
                        pasusu=:pasusu, 
                        keyolv=NULL,      /* 👈 Se anula el token */
                        fecsol=NULL,      /* 👈 Se anula la fecha */
                        bloqkey=1, 
                        fec_actu=NOW()
                    WHERE idusu=:idusu";
            
            $modelo = new conexion();
            $conexion = $modelo->get_conexion();
            $result = $conexion->prepare($sql);
            
            $pasusu = $this->getPasusu();
            $result->bindParam(':pasusu', $pasusu);
            
            $idusu = $this->getIdusu();
            $result->bindParam(':idusu', $idusu);
            
            $result->execute();
            return true;
        }catch(Exception $e){
            error_log("Error al actualizar la contraseña: ".$e->getMessage());
            return false;
        }
    }
}
?>