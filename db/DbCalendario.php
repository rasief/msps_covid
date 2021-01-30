<?php

require_once("DbConexion.php");

class DbCalendario extends DbConexion {

    public function getCalendario($mes, $anio) {
        try {
            $sql = "SELECT *, DAYOFWEEK(fecha_cal) AS dia_semana ".
				   "FROM calendarios ".
				   "WHERE MONTH(fecha_cal)=".$mes." ".
				   "AND YEAR(fecha_cal)=".$anio." ".
				   "ORDER BY fecha_cal";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	public function getMesActual() {
        try {
			$sql = "SELECT MONTH(NOW()) as mes";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	public function getDiaActual() {
        try {
			$sql = "SELECT DAY(NOW()) as dia";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	public function getAnioActual() {
        try {
			$sql = "SELECT YEAR(NOW()) as anio";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	public function getFechaActual() {
        try {
			$sql = "SELECT CURDATE() AS fecha_hoy";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
    public function crear_mes_calendario($anio, $mes, $id_usuario) {
        try {
            $sql = "CALL pa_crear_mes_calendario(".$anio.", ".$mes.", ".$id_usuario.", @id)";
            
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			
            return $arrResultado["@id"];
        } catch (Exception $e) {
            return -2;
        }
    }
	
    public function modificar_calendario($fecha_cal, $ind_laboral, $id_usuario) {
        try {
            $sql = "CALL pa_modificar_calendario(STR_TO_DATE('".$fecha_cal."', '%Y-%m-%d'), ".$ind_laboral.", ".$id_usuario.", @id)";
			
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
			
            return $arrResultado["@id"];
        } catch (Exception $e) {
            return -2;
        }
    }
	
	public function get_siguiente_dia_habil($fecha) {
        try {
			$sql = "SELECT * FROM (
						SELECT *, DATE_FORMAT(fecha_cal, '%d/%m/%Y') AS fecha_cal_t
						FROM calendarios
						WHERE fecha_cal>STR_TO_DATE('".$fecha."', '%d/%m/%Y')
						AND ind_laboral=1
						ORDER BY fecha_cal
					) T
					LIMIT 1";
			
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
}
?>
