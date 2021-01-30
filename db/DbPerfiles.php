<?php
	require_once("DbConexion.php");
	
	class DbPerfiles extends DbConexion {
		//Muestra los perfiles que estan en estado activos e indicador de atiende con valor de 1
		public function getPerfiles() {
			try {
				$sql = "SELECT *
						FROM perfiles
						WHERE ind_activo=1
						AND ind_atiende=1
						ORDER BY nombre_perfil";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Muestra los perfiles que estan en estado activos e indicador de atiende con valor de 1
		public function getListaPerfiles() {
			try {
				$sql = "SELECT p.*, ins.nombre_detalle AS tipo_institucion  FROM perfiles p
                                        LEFT JOIN departamentos d ON d.cod_dep = p.cod_departamento
                                        LEFT JOIN listas_detalle ins ON ins.id_detalle = p.tipo_colegio
                                        ORDER BY p.nombre_perfil";
				
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Devulve los datos de un perfil
		public function getUnPerfil($id_perfil) {
			try {
				$sql = "SELECT * FROM perfiles WHERE id_perfil = $id_perfil";
				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		//Devulve los menus y los permiso que tiene el perfil
		public function getPermisosMenus($id_perfil) {
			try {
				$sql = "SELECT * FROM permisoss WHERE id_perfil = $id_perfil";
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		/**
		 * Metodo para crear un perfil
		 */
		public function crearPerfil($nombre_perfil, $descripcion, $id_menu_inicio, $id_usuario, $menus_permisos, $cod_departamento, $cmb_colegio) {
			try {
				$sql = "CALL pa_crear_perfil('".$nombre_perfil."', '".$descripcion."', ".$id_menu_inicio.", '".$cod_departamento."', ".$cmb_colegio.", ".$id_usuario.", @id)";
                                
                                echo $sql;
				
				
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_perfil = $arrResultado["@id"];
				
				if ($id_perfil > 0) {
					$array_menus_permisos = explode("-", $menus_permisos);
					foreach ($array_menus_permisos as $fila) {
						if ($fila != '') {
							$menu_permiso = explode(",", $fila);
							$menu = $menu_permiso[0];
							$permiso = $menu_permiso[1];
							if ($permiso != '') {
								$sql = "INSERT INTO permisoss
										(id_perfil, id_menu, tipo_acceso, id_usuario_crea, fecha_crea)
										VALUES (".$id_perfil.", ".$menu.", ".$permiso.", ".$id_usuario.", NOW())";
								$arrCampos[0] = "@id";
								$this->ejecutarSentencia($sql, $arrCampos);
							}
						}
					}
				}
				return $id_perfil;
			} catch (Exception $e) {
				return -2;
			}
		}
		
		/**
		 * Metodo para editar un perfil
		 */
		public function editarPerfil($id_perfil, $nombre_perfil, $descripcion, $id_menu_inicio, $ind_activo, $id_usuario, $menus_permisos, $cod_departamento, $cmb_colegio) {
			try {
				if (trim($id_menu_inicio) == "") {
					$id_menu_inicio = "NULL";
				}
				$sql = "CALL pa_editar_perfil(".$id_perfil.", '".$nombre_perfil."', '".$descripcion."', ".$id_menu_inicio.", '".$cod_departamento."', ".$cmb_colegio.", ".$ind_activo.", ".$id_usuario.", @id)";
				$arrCampos[0] = "@id";
				$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
				$id_resultado = $arrResultado["@id"];
				
				if ($id_resultado > 0) {
					$array_menus_permisos = explode("-", $menus_permisos);
					foreach ($array_menus_permisos as $fila) {
						if ($fila != '') {
							$menu_permiso = explode(",", $fila);
							$menu = $menu_permiso[0];
							$permiso = $menu_permiso[1];
							if ($permiso != '') {
								$sql = "INSERT INTO permisoss
										(id_perfil, id_menu, tipo_acceso, id_usuario_crea, fecha_crea)
										VALUES (".$id_perfil.", ".$menu.", ".$permiso.", ".$id_usuario.", NOW())";
										
								$arrCampos[0] = "@id";
								$this->ejecutarSentencia($sql, $arrCampos);
							}
						}
					}
				}
				return $id_resultado;
			} catch (Exception $e) {
				return -2;
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
		
		//Perfiles
		public function getNombrePerfilColegio($id_usuario) {
			try {
				$sql = "SELECT p.id_perfil, p.nombre_perfil
                                        FROM perfiles p 
                                        INNER JOIN usuarios_perfiles up  ON up.id_perfil = p.id_perfil
                                        WHERE up.id_usuario = $id_usuario";
						
						//echo $sql;

				return $this->getUnDato($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		public function getNombrePerfil($id_usuario) {
			try {
				$sql = "SELECT p.id_perfil, p.nombre_perfil as nombre_perfil
						FROM perfiles p 
						INNER JOIN usuarios_perfiles up  ON up.id_perfil = p.id_perfil
						WHERE up.id_usuario = $id_usuario";
						
						//echo $sql;

				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		
		public function getListaColegio($id_usuario, $tipo_institucion) {
			try {
				$sql = "SELECT p.id_perfil, p.nombre_perfil
                                        FROM perfiles p 
                                        INNER JOIN usuarios_perfiles up  ON up.id_perfil = p.id_perfil
                                        WHERE p.tipo_colegio = $tipo_institucion AND up.id_usuario = $id_usuario";						
                                        //echo $sql;
				return $this->getDatos($sql);
			} catch (Exception $e) {
				return array();
			}
		}
		
		
		
		
		
		
		
		
		
	}
?>
