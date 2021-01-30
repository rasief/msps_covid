<?php

require_once("DbConexion.php");

class DbUsuarios extends DbConexion {

    public function validarIngreso($loginUsuario, $claveUsuario) {
        try {
            $sql = "SELECT * FROM usuarios
                                        WHERE login_usuario='" . $loginUsuario . "'
                                        AND clave_usuario=SHA(CONCAT('" . $loginUsuario . "', '|', '" . $claveUsuario . "'))
                                        AND ind_activo=1";
			
            $arrResultado = $this->getUnDato($sql);
			
			//echo "Esta dato:    ".$arrResultado['id_usuario'];

            if (count($arrResultado) <= 0) {
                $sql = "SELECT 0 AS id_usuario, NULL AS nombre_usuario, NULL AS id_perfil";

                $arrResultado = $this->getUnDato($sql);
            }

            return $arrResultado;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosBuscar($txt_buscar) {
        try {
            $txt_buscar = str_replace(" ", "%", $txt_buscar);
            $sql = "SELECT * " .
                    "FROM usuarios u " .
                    "WHERE CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) LIKE '%" . $txt_buscar . "%' " .
                    "OR u.login_usuario LIKE '%" . $txt_buscar . "%' " .
                    "OR u.numero_documento LIKE '%" . $txt_buscar . "%' " .
                    "ORDER BY u.nombre_usuario, u.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaPerfilUsuarios($id_usuario) {
        try {
            $sql = "SELECT * " .
                    "FROM perfiles p " .
                    "INNER JOIN usuarios_perfiles up ON up.id_perfil = p.id_perfil " .
                    "WHERE up.id_usuario=" . $id_usuario . " " .
                    "ORDER BY p.nombre_perfil";
			//echo $sql;		
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getUsuario($idUsuario) {
        try {
            $sql = "SELECT * " .
                    "FROM usuarios " .
                    "WHERE id_usuario=" . $idUsuario;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function updatePass($idUsuario, $claveUsuario, $claveUsuario2) {
        try {
            $rta = '';
            $sql1 = "SELECT * 
						 FROM usuarios 
						 WHERE id_usuario = $idUsuario AND clave_usuario = SHA(CONCAT(login_usuario, '|', '" . $claveUsuario2 . "'))";
            $rta = $this->getUnDato($sql1);
            if (count($rta) >= 1) {
                $sql = "UPDATE usuarios 
							SET clave_usuario = SHA(CONCAT(login_usuario, '|', '" . $claveUsuario . "'))
							WHERE id_usuario= $idUsuario
							AND clave_usuario= SHA(CONCAT(login_usuario, '|', '" . $claveUsuario2 . "'))";
                $arrCampos[0] = "@id";
                $this->ejecutarSentencia($sql, $arrCampos);
                return 1;
            } else {
                return 2;
            }
        } catch (Exception $e) {
            
        }
    }

    /* Resetea la contraseña */

    public function resetearPass($idUsuario) {
        try {
            $sql = "CALL pa_resetear_contrasena_usuario(" . $idUsuario . ", @id)";

            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $resultado_out = $arrResultado["@id"];

            return $resultado_out;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getUsuarios() {
        try {
            $sql = "SELECT *, CONCAT(nombre_usuario, ' ', apellido_usuario) AS nombre_completo  FROM usuarios 
						ORDER BY nombre_usuario, apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getNombreUsuariosBuscar($txt_buscar) {
        try {
            $sql = "SELECT * " .
                    "FROM usuarios u " .
                    "WHERE u.login_usuario LIKE '" . $txt_buscar . "' ";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getBuscarDocumento($txt_buscar, $id_usuario) {
        try {
            if ($id_usuario == '') {
                $sql = "SELECT * " .
                        "FROM usuarios u " .
                        "WHERE u.numero_documento LIKE '" . $txt_buscar . "'";
            } else if ($id_usuario != '') {//Validar para editar usuario
                $sql = "SELECT * " .
                        "FROM usuarios u " .
                        "WHERE (u.numero_documento LIKE '" . $txt_buscar . "') " .
                        "AND id_usuario <> " . $id_usuario;
            }
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function InsertUsuario($txt_nombre_usuario, $txt_apellido_usuario, $cmb_tipo_documento, $txt_numero_documento, $txt_usuario, $txt_clave, $perfiles_usuarios, $id_usuario_crea, $txt_email, $txt_telefono) {

        try {
            strlen($txt_email) >= 1 ? $txt_email = "'" . $txt_email . "'" : $txt_email = "NULL";
            strlen($txt_telefono) >= 1 ? $txt_telefono = "'" . $txt_telefono . "'" : $txt_telefono = "NULL";


            $sql = "CALL pa_crear_usuario('" . $txt_nombre_usuario . "', '" . $txt_apellido_usuario . "', $cmb_tipo_documento, '" . $txt_numero_documento . "',
					   '" . $txt_usuario . "', '" . $txt_clave . "', " . $id_usuario_crea . ", $txt_email, $txt_telefono, @id)";

            //echo $sql;	   
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_usuario_creado = $arrResultado["@id"];

            if ($id_usuario_creado > 0) {
                $array_perfiles_usuarios = explode(",", $perfiles_usuarios);
                foreach ($array_perfiles_usuarios as $fila_perfiles) {
                    $sql_insert = "INSERT INTO usuarios_perfiles 
									 (id_usuario, id_perfil, id_usuario_crea, fecha_crea)
									 VALUES (" . $id_usuario_creado . ", " . $fila_perfiles . ", " . $id_usuario_crea . ", NOW())";
                    $arrCampos[0] = "@id";
                    $this->ejecutarSentencia($sql_insert, $arrCampos);
                }
            }
            return $id_usuario_creado;
        } catch (Exception $e) {
            return -2;
        }
    }

    public function UpdateUsuario($hdd_id_usuario, $txt_nombre_usuario, $txt_apellido_usuario, $cmb_tipo_documento, $txt_numero_documento, $check_estado, $perfiles_usuarios, $id_usuario_crea,$txt_email,$txt_telefono) {
        try {

            strlen($txt_email) >= 1 ? $txt_email = "'" . $txt_email . "'" : $txt_email = "NULL";
            strlen($txt_telefono) >= 1 ? $txt_telefono = "'" . $txt_telefono . "'" : $txt_telefono = "NULL";

            $array_perfiles_usuarios = explode(",", $perfiles_usuarios);
            $sql_delete = "DELETE FROM temporal_ldsp WHERE id_usuario = " . $hdd_id_usuario . "";
            $arrCampos_delete[0] = "@id";
            $this->ejecutarSentencia($sql_delete, $arrCampos_delete);

            foreach ($array_perfiles_usuarios as $fila_perfiles) {
                $sql_insert = "INSERT INTO temporal_ldsp
                                (id_usuario, valor)
                                VALUES (" . $hdd_id_usuario . ", " . $fila_perfiles . ")";
                $arrCampos[0] = "@id";
				
				//echo $sql_insert."<br />";
				
                $this->ejecutarSentencia($sql_insert, $arrCampos);
            }

            $sql = "CALL pa_editar_usuario(" . $hdd_id_usuario . ", '" . $txt_nombre_usuario . "', '" . $txt_apellido_usuario . "', $cmb_tipo_documento, '" . $txt_numero_documento . "', " . $check_estado . ", " . $id_usuario_crea . ", $txt_email, $txt_telefono, @id)";


            
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_resultado = $arrResultado["@id"];

            //$id_resultado = 1;

            return $id_resultado;
        } catch (Exception $e) {
            return -2;
        }
    }

    //Funcion que retorna los usuario profesionales en el combo box del formulario estado_atencion.php
    public function getListaUsuariosEstadoAtencion() {
        try {
            $sql = "SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
									FROM usuarios U
									INNER JOIN admisiones A ON A.id_usuario_prof = U.id_usuario
									WHERE DATE_FORMAT(A.fecha_admision,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') AND 
									EXISTS (SELECT id_usuario_prof FROM admisiones)
									GROUP BY U.nombre_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosTipoCitaDet($id_tipo_cita, $id_tipo_reg, $id_usuario_prof) {
        try {
            $sql = "/*Verifica que el usuario tenga permisoss*/
						SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo, 0 AS id_lugar_disp, '' AS lugar_disp
						FROM tipos_citas_det CD
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						INNER JOIN permisoss P ON TR.id_menu=P.id_menu
						INNER JOIN usuarios_perfiles UP ON P.id_perfil=UP.id_perfil
						INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
						WHERE CD.id_tipo_cita=" . $id_tipo_cita . "
						AND CD.id_tipo_reg=" . $id_tipo_reg . "
						AND U.id_usuario=" . $id_usuario_prof . "
						AND CD.ind_usuario_alt=0
						AND P.tipo_acceso=2
						AND U.ind_activo=1
						
						UNION
						
						/*Obtiene los usuarios disponibles*/
						SELECT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo, D.id_lugar_disp, D.lugar_disp
						FROM tipos_citas_det CD
						INNER JOIN tipos_registros_hc TR ON CD.id_tipo_reg=TR.id_tipo_reg
						INNER JOIN permisoss P ON TR.id_menu=P.id_menu
						INNER JOIN usuarios_perfiles UP ON P.id_perfil=UP.id_perfil
						INNER JOIN usuarios U ON UP.id_usuario=U.id_usuario
						INNER JOIN (
							SELECT D.id_usuario, D.id_lugar_disp, LD.nombre_detalle AS lugar_disp
							FROM disponibilidad_prof D
							INNER JOIN listas_detalle LD ON D.id_lugar_disp=LD.id_detalle
							WHERE DATE(D.fecha_cal)=DATE(NOW())
							AND D.id_tipo_disponibilidad=11
							UNION
							SELECT D.id_usuario, D.id_lugar_disp, LD.nombre_detalle AS lugar_disp
							FROM disponibilidad_prof D
							INNER JOIN disponibilidad_prof_det DD ON D.id_disponibilidad=DD.id_disponibilidad
							INNER JOIN listas_detalle LD ON D.id_lugar_disp=LD.id_detalle
							WHERE DATE(D.fecha_cal)=DATE(NOW())
							AND D.id_tipo_disponibilidad=12
							AND NOW() BETWEEN DD.hora_ini AND DD.hora_final
						) D ON UP.id_usuario=D.id_usuario
						WHERE CD.id_tipo_cita=" . $id_tipo_cita . "
						AND CD.id_tipo_reg=" . $id_tipo_reg . "
						AND U.id_usuario<>" . $id_usuario_prof . "
						AND P.tipo_acceso=2
						AND U.ind_activo=1
						
						ORDER BY id_lugar_disp, nombre_usuario, apellido_usuario;";
            //echo($sql);

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaUsuariosCirugia($ind_cirugia) {
        try {
            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM usuarios U
						INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario
						INNER JOIN perfiles P ON UP.id_perfil=P.id_perfil
						WHERE U.ind_activo=1
						AND P.ind_cirugia=" . $ind_cirugia . "
						ORDER BY U.nombre_usuario, U.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /* Función que retorna los usuarios que tienen permisos sobre un listado de menús */

    public function getListaUsuariosAcceso($arr_menus, $tipo_acceso) {
        try {
            $cadena_menus = "";
            foreach ($arr_menus as $id_menu_aux) {
                if ($cadena_menus != "") {
                    $cadena_menus .= ", ";
                }
                $cadena_menus .= $id_menu_aux;
            }

            $sql = "SELECT DISTINCT U.*, CONCAT(U.nombre_usuario, ' ', U.apellido_usuario) AS nombre_completo
						FROM usuarios U
						INNER JOIN usuarios_perfiles UP ON U.id_usuario=UP.id_usuario
						INNER JOIN permisoss P ON UP.id_perfil=P.id_perfil
						WHERE U.ind_activo=1
						AND P.id_menu IN (" . $cadena_menus . ")
						AND P.tipo_acceso=" . $tipo_acceso . "
						ORDER BY U.nombre_usuario, U.apellido_usuario";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDepartamentosPerfiles($id_usuario) {
        try {
            $sql = "SELECT cod_dep FROM departamentos d
						INNER JOIN perfiles p ON p.cod_departamento = d.cod_dep
						WHERE p.id_perfil IN ( SELECT id_perfil FROM usuarios_perfiles WHERE id_usuario = $id_usuario )";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getAdminUsuario($idUsuario) {
        try {
            $sql = "SELECT u.id_usuario as val_admin
						FROM usuarios u
						INNER JOIN usuarios_perfiles up ON up.id_usuario = u.id_usuario
						WHERE u.id_usuario = " . $idUsuario . " AND up.id_perfil = 1";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}

?>
