<?php

require_once("DbConexion.php");

class DbUsuariosPerfiles extends DbConexion {
    public function getUsuarios($perfil) {
        try {
            $sql = "SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
                    FROM usuarios U
                    INNER JOIN usuarios_perfiles UP ON UP.id_usuario = U.id_usuario
                    INNER JOIN perfiles P ON P.id_perfil = UP.id_perfil
                    WHERE P.id_perfil = $perfil AND U.ind_activo = 1
                    ORDER BY U.nombre_usuario, U.apellido_usuario";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function getListaUsuariosIndAtiende($ind_atiende) {
        try {
            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo ".
				   "FROM usuarios U ".
				   "INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario ".
				   "INNER JOIN perfiles P ON UP.id_perfil=P.id_perfil ".
				   "WHERE U.ind_activo=1 ".
				   "AND P.ind_atiende=".$ind_atiende." ".
				   "ORDER BY U.nombre_usuario, U.apellido_usuario";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    //Lista de departamentos
    public function getDepartamentos() {
        try {
            $sql = "SELECT * FROM departamentos WHERE activo = 1 ORDER BY cod_dep";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    
}

?>