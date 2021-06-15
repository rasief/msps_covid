<?php
require_once("DbConexion.php");

class DbVariables extends DbConexion {
    public function getVariable($id) {
        try {
            $sql = "SELECT * 
                    FROM variables 
                    WHERE id_variable = $id";
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function getVariables($id) {
        try {
            $sql = "SELECT * 
                    FROM variables 
                    WHERE id_variable = $id";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    //Fecha del servidor de base de datos
    public function getFechaactual() {
        try {
            $sql = "SELECT DATE_FORMAT(NOW(),'%Y-%m-%d') AS fecha_actual";
          
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	//Fecha del servidor de base de datos format
	public function getFechaActualMostrar() {
		try {
			$sql = "SELECT DATE_FORMAT(NOW(),'%d/%m/%Y') AS fecha_actual_mostrar,
					DATE_FORMAT(NOW(),'%h:%i %p') AS hora_actual_mostrar,
					DATE_FORMAT(NOW(),'%H:%i') AS hora24_actual_mostrar";
			
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return array();
		}
	}
	
	//Fecha del servidor de base de datos
    public function getAnoMesDia() {
        try {
            $sql = "SELECT DATE_FORMAT(NOW(),'%Y') AS anio_actual, DATE_FORMAT(NOW(),'%m') AS mes_actual, DATE_FORMAT(NOW(),'%d') AS dia_actual";
          
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function getListaVariables() {
        try {
            $sql = "SELECT * ".
                   "FROM variables ".
                   "ORDER BY nombre_variable";
			
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function actualizar_variable($id_variable, $nombre_variable, $descripcion_variable, $tipo_variable, $valor_variable, $id_usuario) {
		try {
			$sql = "CALL pa_editar_variable(".$id_variable.", '".$nombre_variable."', '".$descripcion_variable."', ".
				   $tipo_variable.", '".$valor_variable."', ".$id_usuario.", @id)";
			
			$arrCampos[0] = "@id";
			$arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			
			if (isset($arrResultado["@id"])) {
				return $arrResultado["@id"];
			} else {
				return $arrResultado;
			}
		} catch (Exception $e) {
			return -2;
		}
	}
	
	
	/*
	 * $fecha= en formato  '%d/%m/%Y'
	 * $dias= numero de dias
	 */
	public function sumar_dias_fecha($fecha, $dias) {
		try {
			$sql = "SELECT DATE_FORMAT(DATE_ADD(STR_TO_DATE('$fecha', '%d/%m/%Y'), INTERVAL $dias DAY), '%d/%m/%Y') AS fecha_resultado";
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return -2;
		}
	}
	
	
	/*
	 * $fecha= en formato  '%d/%m/%Y'
	 * $dias= numero de dias
	 */
	public function restar_dias_fecha($fecha, $dias) {
		try {
			$sql = "SELECT DATE_FORMAT(DATE_SUB(STR_TO_DATE('$fecha', '%d/%m/%Y'), INTERVAL $dias DAY), '%d/%m/%Y') AS fecha_resultado";
			return $this->getUnDato($sql);
		} catch (Exception $e) {
			return -2;
		}
	}
	
	public function getListaFechas($fecha_ini) {
		try {
			$sql = "SELECT fecha, DATE_FORMAT(fecha, '%d/%m/%Y') AS fecha_t
                    FROM (
                        SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS fecha
                        FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                        CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                        CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                        CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                    ) T
                    WHERE fecha>=STR_TO_DATE('" . $fecha_ini . "', '%Y/%m/%d')
                    ORDER BY fecha";
			
            //echo($sql  . "<br/>");
			return $this->getDatos($sql);
		} catch (Exception $e) {
			return array();
		}
	}
    
}
?>
