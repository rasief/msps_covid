<?php
require_once("DbConexion.php");

class DbViajeros extends DbConexion {
    
    public function getFechaSiguiente() {
        try {
            $sql = "SELECT IFNULL(DATE_ADD(MAX(fecha_viaje), INTERVAL 1 DAY), CURDATE()) AS fecha_viaje
                    FROM consolidado_viajeros";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getFechasMinMax() {
        try {
            $sql = "SELECT IFNULL(MIN(fecha_viaje), CURDATE()) AS fecha_min, IFNULL(MAX(fecha_viaje), CURDATE()) AS fecha_max
                    FROM consolidado_viajeros";
            
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getListaConsolidados($fecha_ini, $fecha_fin) {
        try {
            $sql = "SELECT fecha_viaje, ind_nacional, SUM(cant_viajeros) AS total, SUM(ind_pregunta_4 * cant_viajeros) AS si_4,
                    SUM(CASE WHEN ind_pregunta_4=0 AND ind_pregunta_6=1 AND ind_pregunta_7=0 THEN cant_viajeros ELSE 0 END) AS no_4_si_6_no_7,
                    SUM(CASE WHEN ind_pregunta_4=0 AND ind_pregunta_6=0 AND ind_pregunta_7=1 THEN cant_viajeros ELSE 0 END) AS no_4_no_6_si_7,
                    SUM(CASE WHEN ind_pregunta_4=0 AND ind_pregunta_6=1 AND ind_pregunta_7=1 THEN cant_viajeros ELSE 0 END) AS no_4_si_6_si_7,
                    SUM(CASE WHEN ind_pregunta_4=0 AND ind_pregunta_6=0 AND ind_pregunta_7=0 THEN cant_viajeros ELSE 0 END) AS no_4_no_6_no_7
                    FROM consolidado_viajeros
                    WHERE fecha_viaje BETWEEN '" . $fecha_ini . "' AND '" . $fecha_fin . "'
                    GROUP BY fecha_viaje, ind_nacional
                    ORDER BY fecha_viaje, ind_nacional DESC";
            
            //echo($sql . "<br>");
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function crearConsolidadoViajeros($mapa_valores) {
        $sql_base = "INSERT INTO consolidado_viajeros (fecha_viaje, ind_nacional, ind_pregunta_4, ind_pregunta_5, ind_pregunta_6, ind_pregunta_7, cant_viajeros) VALUES ";
        $cont_aux = 0;
        $sql = "";
        try {
            foreach ($mapa_valores as $valor_aux) {
                if ($cont_aux > 0 && $cont_aux % 200 == 0) {
                    $sql = $sql_base . $sql;
                    $arr_campos[0] = "@id";
                    $this->ejecutarSentencia($sql, $arr_campos);
                    
                    $sql = "";
                }
                
                if ($sql != "") {
                    $sql .= ", ";
                }
                $sql .= "('" . $valor_aux["fecha_viaje"] . "', " . $valor_aux["ind_nacional"] . ", " . $valor_aux["ind_pregunta_4"] . ", " .
                        $valor_aux["ind_pregunta_5"] . ", " . $valor_aux["ind_pregunta_6"] . ", " . $valor_aux["ind_pregunta_7"] . ", " .
                        $valor_aux["cantidad"] . ")";
                $cont_aux++;
            }
            
            if ($sql != "") {
                $sql = $sql_base . $sql;
                $arr_campos[0] = "@id";
                $this->ejecutarSentencia($sql, $arr_campos);
            }
            
            //echo($sql . "<br>");
            return 1;
        } catch (Exception $e) {
            return -2;
        }
    }

}
?>