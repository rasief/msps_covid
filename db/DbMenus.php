<?php
require_once("DbConexion.php");

class DbMenus extends DbConexion {
	/**
	 * Obtener lista de menus
	 */
    public function getListaMenus($idPerfil) {
        try {
            $sql = "SELECT m.id_menu, m.nombre_menu, m.pagina_menu, id_menu_padre
						FROM menus m
						LEFT JOIN permisoss p ON p.id_menu = m.id_menu
						WHERE m.id_menu IN (SELECT DISTINCT m1.id_menu_padre
								    FROM menus m1
								    INNER JOIN permisoss p1 ON p1.id_menu = m1.id_menu
								    WHERE p1.id_perfil = $idPerfil)
						OR p.id_perfil = $idPerfil		    
						ORDER BY m.orden, m.id_menu, m.id_menu_padre";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
	/**
	 * Obtener lista de menu
	 */
    public function getListaMenus2($idUsuario) {
        try {
            $sql = "SELECT DISTINCT m.id_menu, IFNULL(MAX(p.tipo_acceso), 0) AS tipo_acceso, m.nombre_menu, m.pagina_menu, m.id_menu_padre, m.orden
					FROM menus m
					LEFT JOIN permisoss p ON m.id_menu=p.id_menu 
					LEFT JOIN usuarios_perfiles up ON up.id_perfil=p.id_perfil
					WHERE m.id_menu IN (
						SELECT DISTINCT m1.id_menu_padre
						FROM menus m1  
						INNER JOIN permisoss p1 ON p1.id_menu=m1.id_menu
						INNER JOIN usuarios_perfiles up1 ON up1.id_perfil=p1.id_perfil 
						WHERE up1.id_usuario=".$idUsuario."
						AND m1.id_menu_padre<>0
					)
					OR up.id_usuario=".$idUsuario."
					AND m.ind_visible=1
					GROUP BY m.id_menu, m.nombre_menu, m.pagina_menu, m.id_menu_padre, m.orden
					ORDER BY m.orden";
			//echo $sql;
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    /**
	 * Obtener menu padre
	 */
    public function getMenusPadre($id_padre) {
        try {
            $sql = "SELECT * FROM menus WHERE id_menu_padre = $id_padre";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    /**
	 * Obtener menu desde el id
	 */
    public function getMenu($id_menu) {
        try {
            $sql = "SELECT * FROM menus WHERE id_menu = $id_menu";
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    /**
	 * Obtener el listado de menus con su ruta
	 */
    public function getMenusRuta(){
        try {
            $sql = "SELECT M.*, M2.nombre_menu AS nombre_2, M3.nombre_menu AS nombre_3
					FROM menus M
					LEFT JOIN menus M2 ON M.id_menu_padre=M2.id_menu
					LEFT JOIN menus M3 ON M2.id_menu_padre=M3.id_menu
					WHERE M.pagina_menu<>''
					ORDER BY M3.orden, M3.nombre_menu, M2.orden, M2.nombre_menu, M.orden, M.nombre_menu";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    /**
	 * Función que obtiene el menú correspondiente a un tipo de cita en un estado determinado
	 */
    public function getMenuTipoCitaEstado($id_tipo_cita, $id_estado_atencion) {
        try {
			if ($id_estado_atencion == "4" || $id_estado_atencion == "6") {
				$id_estado_atencion--;
			}
            $sql = "SELECT M.*
					FROM tipos_citas_det CD
					INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
					INNER JOIN menus M ON TR.id_menu=M.id_menu
					WHERE CD.id_tipo_cita=".$id_tipo_cita."
					AND CD.id_estado_atencion=".$id_estado_atencion."
					ORDER BY CD.orden";
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    /**
	 * Obtener menu desde el id
	 */
    public function getMenuInicioUsuario($id_usuario) {
        try {
            $sql = "SELECT * FROM (
						SELECT M.*
						FROM usuarios U
						INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario
						INNER JOIN perfiles P ON UP.id_perfil=P.id_perfil
						INNER JOIN menus M ON P.id_menu_inicio=M.id_menu
						INNER JOIN permisoss PR ON P.id_perfil=PR.id_perfil AND P.id_menu_inicio=PR.id_menu
						WHERE U.id_usuario=".$id_usuario."
						ORDER BY PR.tipo_acceso DESC
					) T
					LIMIT 1";
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	/**
	 * Obtiene todos los menús visibles com páginas asignadas
	 */
    public function getListaMenusVisibles() {
        try {
            $sql = "SELECT M.id_menu, CONCAT(MP.nombre_menu, ' -> ', M.nombre_menu) AS nombre_completo, M.nombre_menu,
                    M.pagina_menu, M.orden, M.id_menu_padre, M.ind_visible, MP.nombre_menu AS nombre_menu_padre
                    FROM menus M
                    INNER JOIN menus MP ON M.id_menu_padre=MP.id_menu
                    WHERE M.ind_visible=1
                    AND TRIM(IFNULL(M.pagina_menu, ''))<>''
                    ORDER BY MP.nombre_menu, M.nombre_menu";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
}
?>
